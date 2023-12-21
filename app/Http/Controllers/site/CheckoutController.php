<?php
/*****************************************************/
# Page/Class name   : CheckoutController
# Purpose           : Checkout related functions
/*****************************************************/
namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Payrexx\Payrexx;
use App;
use Auth;
use URL;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailLocal;
use App\Models\OrderAttributeLocal;
use App\Models\OrderIngredient;
use App\Models\OrderIngredientLocal;
use App\Models\DeliveryAddress;
use App\Models\DeliverySlot;
use App\Models\PinCode;
use App\Models\SiteSetting;
use App\Models\Coupon;
use App\Models\SpecialHour;
use Twilio\Rest\Client;
use Helper;
use \DB;
use Cookie;

class CheckoutController extends Controller
{
    /*****************************************************/
    # Function name : index
    # Params        : Request $request
    /*****************************************************/
    public function index(Request $request)
    {
        $title      = trans('custom.error');
        $message    = trans('custom.please_try_again');
        $type       = 'error';
        $redirectTo = '';
        
        if ($request->isMethod('POST')) {
            $currentLang        = App::getLocale();
            $siteSettingsData   = Helper::getSiteSettings();
            $cartDetails        = Helper::getCartItemDetails();

            if (count($cartDetails['itemDetails']) == 0) {
                $title      = trans('custom.error');
                $message    = trans('custom.message_empty_order');
                $type       = 'error';
                $redirectTo = '';
            } else if (($cartDetails['totalCartPrice'] < Cookie::get('minimum_order_amount')) && $request->delivery_option!='Click & Collect') {
                $title      = trans('custom.error');
                $message    = trans('custom.text_min_order_amount1');
                $type       = 'error';
                $redirectTo = '';
            } else {
                $title      = trans('custom.success');
                $message    = trans('custom.message_proceed_to_checkout');
                $type       = 'success';

                $deliveryOption = isset($request->delivery_option) ? $request->delivery_option : 'Delivery';
                
                Session::put('deliveryOption', $deliveryOption);

                $redirectTo = 'checkout';

                if (!Auth::user()) {
                    $redirectTo = 'guest_checkout';
                } else {
                    $redirectTo = 'checkout';
                }
            }
        }
        
        return json_encode([
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'redirectTo'    => $redirectTo,
        ]);
    }

    /**
     * Parex Payment Gatway
     */
    public function successByPayrex(Request $request) {
        $cartDetails        = Helper::getCartItemDetails();
        $cids=!empty($cartDetails['cartOrderId'])?$cartDetails['cartOrderId']:'';
        $siteSettings       = Helper::getSiteSettings();
        $payment_setting    = Helper::getPaymentSettings();
        $payrexx_active=!empty($payment_setting->payrexx_active)?$payment_setting->payrexx_active:'';
        $enablepayrex='';

        if ($payrexx_active) {
            if ($payment_setting->payrexx_method=='Y') {
                $enablepayrex=1;
            }
        }
        if ($cids && $cartDetails && $payrexx_active && $enablepayrex) {
            $payrexx_instance=!empty($payment_setting)?$payment_setting->payrexx_instance:''; 
            $payrexx_secret_key=!empty($payment_setting)?$payment_setting->payrexx_secret_key:'';
            $payrexx = new \Payrexx\Payrexx($payrexx_instance,$payrexx_secret_key);
            $invoice = new \Payrexx\Models\Request\Gateway(); 
            $conditions = ['id' =>$cartDetails['cartOrderId'],'type' => 'C'];
            $orderUpdate = Order::where($conditions)->first();
            $invoice->setId($orderUpdate->payrexx_id);
            $invid=$orderUpdate->payrexx_id;
            $currentLang        = App::getLocale();
            $response = $payrexx->getOne($invoice);

            try {
                $response = $payrexx->getOne($invoice);
                $status=!empty($response->getStatus())?$response->getStatus():'';
                
                if ($status=='confirmed') {
                    $getOrderData = Order::where([
                        'id'            => $cartDetails['cartOrderId'],
                        'type'          => 'C',
                        'order_status'  => 'IC',
                    ])
                    ->first();
                    if (!Auth::user()) {
                        // $explodedName = explode(' ', $request->full_name);
                        // if (count($explodedName) > 1) {
                        //     $firstName  = $explodedName[0];
                        //     $lastName   = $explodedName[1];
                        // } else {
                        //     $firstName  = $request->full_name;
                        //     $lastName   = ' ';
                        // }

                        $first_name    = Session::get('guest_first_name') ? Session::get('guest_first_name') : null;
                        $last_name     = Session::get('guest_last_name') ? Session::get('guest_last_name') : null;
                        
                        $phone = null;
                        if (Session::get('guest_phone_no') != '') {
                            // if (strpos(Session::get('guest_phone_no'), '+49') !== false) {
                            //     $phone = Session::get('guest_phone_no');
                            // } else {
                            //     $phone = env('COUNTRY_CODE','+49').Session::get('guest_phone_no');
                            // }
                            $phone = Session::get('guest_phone_no');
                        }
                        // Guest user to make registration
                        $guestEmailExist = User::where(['email' => Session::get('guest_email'), 'status' => '1', 'type' => 'C'])->whereNull('deleted_at')->first();
                        $guestUserId = '';
                        if ($guestEmailExist == null) {
                            $userPassword = $this->getRandomPassword();
                            $newUser = new User();
                            $newUser->first_name    = Session::get('guest_first_name') ? Session::get('guest_first_name') : null;
                            $newUser->last_name     = Session::get('guest_last_name') ? Session::get('guest_last_name') : null;
                            $newUser->full_name     = Session::get('guest_full_name') ? Session::get('guest_full_name') : null;
                            $newUser->email         = Session::get('guest_email') ? Session::get('guest_email') : null;
                            $newUser->phone_no      = $phone;
                            $newUser->password      = $userPassword;
                            $newUser->agree         = 1;
                            $newUser->status        = '1';
                            $newUser->save();

                            $guestUserId = $newUser->id;

                            // Insert into notification
                            Helper::insertNotification($newUser->id);
                        } else {
                            $newUser     = $guestEmailExist;
                            $guestUserId = $guestEmailExist->id;

                            // Delete if something exist in cart
                            $alreadyExistingCart = Order::where(['user_id' => $guestEmailExist->id, 'type' => 'C'])->get();
                            if ($alreadyExistingCart->count() > 0)  {
                                if($alreadyExistingCart){
                                    foreach ($alreadyExistingCart as $key => $value) {
                                        OrderIngredientLocal::where(['order_id' => $value->id])->delete();
                                        OrderIngredient::where(['order_id' => $value->id])->delete();
                                        OrderAttributeLocal::where(['order_id' => $value->id])->delete();
                                        OrderDetailLocal::where(['order_id' => $value->id])->delete();
                                        OrderDetail::where(['order_id' => $value->id])->delete();
                                        Order::where(['id' => $value->id])->delete();
                                     }
                                }
                            }
                        }

                        // update address table
                        $addressData = DeliveryAddress::where('session_id',Session::get('cartSessionId'))->first();
                        if ($addressData != null) {
                            $addressData->session_id    = null;
                            $addressData->user_id       = $guestUserId;
                            $addressData->save();
                        }                

                        // update order table
                        $getOrderData->session_id = null;
                        $getOrderData->user_id    = $guestUserId;
                        $getOrderData->save();

                        $orderedUserId = $guestUserId;
                    } else {
                        $orderedUserId = Auth::user()->id;
                    }

                    $userData = User::where('id',$orderedUserId)->first();

                    if ($getOrderData != null) {
                        $transactionResponse['id']                  = $invid;
                        $transactionResponse['object']              = !empty($response->object) ? $response->object : '';
                        $transactionResponse['balance_transaction'] = !empty($response->getInvoices()[0]['amount']) ? $response->getInvoices()[0]['amount']/100 : '';
                        $transactionResponse['payment_method']      = !empty($response->getPsp()) ? $response->getPsp(): '';
                        $transactionResponse['receipt_url']         = !empty($response->receipt_url) ? $response->receipt_url : '';
                        $transactionResponse['status']              = !empty($response->getStatus()) ? $response->getStatus() : '';
                        
                        $getOrderData->coupon_code                  = Session::get('coupon_code') ? Session::get('coupon_code') : null;
                        $getOrderData->card_payment_amount          = Session::get('calculated_card_amount') ? Session::get('calculated_card_amount') : 0;
                        $getOrderData->discount_amount              = Session::get('calculated_discount_amount') ? Session::get('calculated_discount_amount') : 0;
                        $getOrderData->coupon_details               = Session::get('coupon_details') ? Session::get('coupon_details') : null;
                        $getOrderData->payment_status               = 'C';
                        $getOrderData->type                         = 'O';
                        $getOrderData->payment_method               = '2';
                        $getOrderData->order_status                 = 'O';
                        $getOrderData->transaction_id               = $invid;
                        $getOrderData->transaction_response         = json_encode($transactionResponse);
                        $getOrderData->save();

                        Coupon::where(['code' => $getOrderData->coupon_code, 'is_one_time_use' => 'Y'])->update(['is_used' => 'Y']);
                        
                        // Update Order Details
                        OrderDetail::where('order_id', $getOrderData->id)->update(['order_status' => 'O']);
                        
                        $orderDetails = Helper::getOrderDetails($getOrderData->id, $orderedUserId);

                        if ($userData->userNotification != null) {
                            // Mail to customer
                            if ($userData->userNotification->order_update == '1') {
                                /* 06.04.2021
                                \Mail::send('email_templates.site.order_details_to_customer',
                                [
                                    'user'          => $userData,
                                    'siteSetting'   => $siteSettings,
                                    'orderDetails'  => $orderDetails,
                                    'getOrderData'  => $getOrderData,
                                    'app_config'    => [
                                        'appname'       => $siteSettings->website_title,
                                        'appLink'       => Helper::getBaseUrl(),
                                        'currentLang'   => $currentLang,
                                    ],
                                ], function ($m) use ($userData) {
                                    $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                });
                                06.04.2021 */
                            }

                            // SMS to customer
                            // if ($userData->userNotification->sms == '1') {
                                // $sendSms = $this->sendOrderMessage($getOrderData->delivery_phone_no, $getOrderData->unique_order_id);
                            // }
                        }
                        else if ( Session::get('guest_full_name') != '' && Session::get('guest_first_name') != '' && Session::get('guest_email') != '' ) {
                            // Registration mail to customer
                            $guestUserExist = User::where(['email'=>Session::get('guest_email'), 'status' => '1', 'type' => 'C'])->whereNull('deleted_at')->first();
                            if ($guestUserExist == null) {
                                if ($guestUserExist->userNotification != null) {
                                    // Mail to customer
                                    if ($guestUserExist->userNotification->order_update == '1') {
                                        \Mail::send('email_templates.site.guest_registration',
                                        [
                                            'user'          => $userData,
                                            'password'      => $userPassword,
                                            'siteSetting'   => $siteSettings,
                                            'app_config'    => [
                                                'appname'       => $siteSettings->website_title,
                                                'appLink'       => Helper::getBaseUrl(),
                                                'controllerName'=> 'users',
                                                'currentLang'   => $currentLang,
                                            ],
                                        ], function ($m) use ($guestUserExist, $siteSettings) {
                                            $m->to($guestUserExist->email, $guestUserExist->full_name)->subject(trans('custom.label_thank_you').' - '.$siteSettings->website_title);
                                        });
                                    }
                                }                                    
                            }

                            // Order email to customer
                            /* 06.04.2021
                            \Mail::send('email_templates.site.order_details_to_customer',
                                [
                                    'user'          => $userData,
                                    'siteSetting'   => $siteSettings,
                                    'orderDetails'  => $orderDetails,
                                    'getOrderData'  => $getOrderData,
                                    'app_config'    => [
                                        'appname'       => $siteSettings->website_title,
                                        'appLink'       => Helper::getBaseUrl(),
                                        'currentLang'   => $currentLang,
                                    ],
                                ], function ($m) use ($userData) {
                                    $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                });
                            06.04.2021 */
                        }

                        Session::put([
                            'deliveryOption'            => '',
                            'cartSessionId'             => '',
                            'redirectTo'                => '',
                            'guest_full_name'           => '',
                            'guest_first_name'          => '',
                            'guest_last_name'           => '',
                            'guest_email'               => '',
                            'guest_phone_no'            => '',
                            'coupon_code'               => '',
                            'calculated_card_amount'    => '',
                            'calculated_discount_amount'=> '',
                            'coupon_details'            => ''
                        ]);

                        // Mail to admin
                        \Mail::send('email_templates.site.order_details_to_admin',
                        [
                            'user'          => $userData,
                            'siteSetting'   => $siteSettings,
                            'orderDetails'  => $orderDetails,
                            'getOrderData'  => $getOrderData,
                            'app_config'    => [
                                'appname'       => $siteSettings->website_title,
                                'appLink'       => Helper::getBaseUrl(),
                                'currentLang'   => $currentLang,
                            ],
                        ], function ($m) use ($siteSettings, $userData) {
                            $ordertoemail=!empty($siteSettings->to_email)?$siteSettings->to_email:env('ORDER_EMAIL');
                            $m->to($ordertoemail, $siteSettings->website_title)->replyTo($userData->email)->subject(trans('custom.message_new_order_placed').' - '.trans('custom.label_web_site_title'));
                        });
                        
                        $request->session()->flash('alert-success',trans('custom.success_order_placed_success'));
                        return redirect()->route('site.'.$currentLang.'.thank-you', $getOrderData->unique_order_id);
                        
                    } else {
                        $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                        if (Auth::user()) {
                            return redirect()->route('site.'.\App::getLocale().'.checkout');
                        } else {
                            return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
                        }
                    }
                }else{
                    $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                    if (Auth::user()) {
                        return redirect()->route('site.'.\App::getLocale().'.checkout');
                    } else {
                        return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
                    }
                }
            } catch (\Payrexx\PayrexxException $e) {      
                $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                if (Auth::user()) {
                    return redirect()->route('site.'.\App::getLocale().'.checkout');
                } else {
                    return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
                }

            }
        }else{
            $request->session()->flash('alert-danger', trans('custom.please_try_again'));
            if (Auth::user()) {
                return redirect()->route('site.'.\App::getLocale().'.checkout');
            } else {
                return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
            }
        }
}
 
    public function payByPayrex() {
        $currentLang        = App::getLocale();
        $cartDetails        = Helper::getCartItemDetails();
        if(empty($cartDetails)){
           echo 'error';
           exit();
        }   
        $siteSettings       = Helper::getSiteSettings();
        $currentLang =  App::getLocale();      
        $cancel=URL::to('/').'/'.$currentLang.'/cancelByPayrex';

        $isLoggedInUser = 0;

        if (Auth::user()) {
            $isLoggedInUser = 1;

            $existUser  = User::where(['id' => Auth::user()->id])->whereNull('deleted_at')->first();
            $first_name = !empty($existUser->first_name) ? $existUser->first_name : null;
            $last_name  = !empty($existUser->last_name) ? $existUser->last_name : null;
            $email      = !empty($existUser->email) ? $existUser->email : null;
            $phone_no   = !empty($existUser->phone_no) ? $existUser->phone_no : null;
        } else {
            $first_name = Session::get('guest_first_name') ? Session::get('guest_first_name') : null;
            $last_name  = Session::get('guest_last_name') ? Session::get('guest_last_name') : null;
            $email      = Session::get('guest_email') ? Session::get('guest_email') : null;
            $phone_no   = Session::get('guest_phone_no') ? Session::get('guest_phone_no') : null;
        }
        $userData       = $isLoggedInUser.'~'.$first_name.'~'.$last_name.'~'.$email.'~'.$phone_no;

        $oid=$cartDetails['cartOrderId'];
        //$amount= ($cartDetails['totalCartPrice'] + $cartDetails['deliveryCharges']);

        $discountAmount     = Session::get('couponDiscountAmount') ? Session::get('couponDiscountAmount') : 0; 
        $netPayableAmountss   = ($cartDetails['totalCartPrice'] + $cartDetails['deliveryCharges']) - $discountAmount;
        $netPayableAmountss   = $netPayableAmountss + Helper::formatToTwoDecimalPlaces(Helper::paymentCardFee($netPayableAmountss));

        $amount   = Helper::priceRoundOff($netPayableAmountss);
        
        if ($last_name=='') {
            $last_name = $first_name;
        }
        $messageOrder    = trans('custom.label_order_no').' '.$oid;
        $payment_setting    = Helper::getPaymentSettings();
        $payrexx_instance=!empty($payment_setting)?$payment_setting->payrexx_instance:''; 
        $payrexx_secret_key=!empty($payment_setting)?$payment_setting->payrexx_secret_key:'';
        $payrexx = new \Payrexx\Payrexx($payrexx_instance,$payrexx_secret_key);
        $gateway = new \Payrexx\Models\Request\Gateway();
        $gateway->setAmount($amount * 100);
        $gateway->setSku($oid);
        $gateway->setCurrency('CHF');
        // $siteurl=URL::to('/').'/'.$currentLang.'/payrex-payment-sucess';
        $siteurl=URL::to('/').'/'.$currentLang.'/';
        $gateway->setSuccessRedirectUrl($siteurl);
        $gateway->setFailedRedirectUrl($cancel);
        $gateway->setCancelRedirectUrl($cancel);
        $gateway->setPsp(['']);
        $gateway->setPreAuthorization(false);
        $gateway->setReservation(false);
        $gateway->setReferenceId($oid);
        $gateway->addField('title', 'Order #'.$cartDetails['cartOrderId']);
        $webtitle=!empty($siteSettings->website_title)?$siteSettings->website_title:'';
        $gateway->addField($type = 'forename', $value = $first_name);
        $gateway->addField($type = 'surname', $value = $last_name);
        $gateway->addField($type = 'company', $value = $webtitle);
        $gateway->addField($type = 'street', $value = '');
        $gateway->addField($type = 'postcode', $value = '');
        $gateway->addField($type = 'place', $value = '');
        $gateway->addField($type = 'country', $value = '');
        $gateway->addField($type = 'phone', $value = '');
        $gateway->addField($type = 'email', $value = $email);
        // New custom fields
        $gateway->addField($type = 'custom_field_1', $value = $userData);
        $gateway->addField($type = 'custom_field_2', $value = $currentLang);
        $gateway->addField($type = 'custom_field_3', $value = Session::get('calculated_card_amount') ? Session::get('calculated_card_amount') : 0);
        $gateway->addField($type = 'custom_field_4', $value = Session::get('coupon_details') ? Session::get('coupon_details') : '');
        
        $gateway->addField($type = 'custom_field_5', $value = Session::get('calculated_discount_amount') ? Session::get('calculated_discount_amount') : 0);
        
        
        // $gateway->addField($type = 'terms', '');
        //$gateway->addField($type = 'privacy_policy', '');
        try {
            $response = $payrexx->create($gateway);
            $paymentlink=$response->getLink();
            if ($paymentlink) {
                $invid=$response->getId();
                $cartDetails        = Helper::getCartItemDetails();
                $cids=!empty($cartDetails['cartOrderId'])?$cartDetails['cartOrderId']:'';
                $conditions=[];
                if ($cids && $cartDetails) {
                    //$invid=$_GET['transaction_id'];
                    $conditions = ['id' =>$cartDetails['cartOrderId'],'type' => 'C'];
                    $orderUpdate = Order::where($conditions)->first();
                    $conditions = ['id' =>$cartDetails['cartOrderId'],'type' => 'C'];
                    $orderUpdate = Order::where($conditions)->first();
                    $orderUpdate->payrexx_id= $invid;
                    $orderUpdate->save();
                }
                echo str_replace("?payment","$currentLang/?payment",$paymentlink);
                exit();                 
            }
        } catch (\Payrexx\PayrexxException $e) {
            //print $e->getMessage();
        }
        die();
    }

    public function cancelByPayrex(){
        $currentLang =  App::getLocale();
        if (Auth::user()) {
            return redirect()->route('site.'.$currentLang.'.checkout')->withErrors(['msg' => trans('custom_admin.error_something_went_wrong')]);
        } else {
            return redirect()->route('site.'.$currentLang.'.guest-checkout')->withErrors(['msg' => trans('custom_admin.error_something_went_wrong')]);
        }
    }

    // Redirect to thank you page with order unique id, after payment success
    public function payrexPaymentRedirectThankYouPage(Request $request) {
        $currentLang =  App::getLocale();
        $uniqueOrderId = 0;

        if ($request->isMethod('POST')) {
            $orderId = isset($request->orderId) ? $request->orderId : 0;

            if ($orderId) {
                $orderDetails = Order::where(['id' => $orderId])->first();
                if ($orderDetails != null) {
                    $uniqueOrderId = $orderDetails->unique_order_id;
                }
            }
        }
        
        echo $uniqueOrderId;
        exit(0);
    }


    public function webhookSuccessByPayrex(Request $request) {
        $transactionResponse = $request->getContent();

        Helper::webhookSuccessByPayrexUpdateDatabase($transactionResponse);
    }


    /*****************************************************/
    # Function name : checkingRestaurantSlotAvailability
    # Params        : Request $request
    /*****************************************************/
    public function checkingRestaurantSlotAvailability(Request $request)
    {
        $title      = trans('custom.error');
        $message    = trans('custom.error_restaurant_unavailable');
        $type       = 'error';
        
        if ($request->isMethod('POST')) {
            $currentDate            = date('Y-m-d');
            $dayName                = date('l');
            $currentTimeStamp       = strtotime(date('H:i'));
            $selectedSlotTimeStamp  = strtotime($request->delivery_time);

            $siteSettings           = Helper::getSiteSettings();

            $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
            // Current time + minimum delivery delay = Delivery start time
            $deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);
            
            // Start :: special hour
            $availability = SpecialHour::where('special_date', $currentDate)->first();
            if ($availability == null) {
                $availability = DeliverySlot::where('day_title', $dayName)->first();
            }
            // End :: special hour            

            if ($availability != null) {
                if ($availability->holiday == '0') {
                    // If current time is over than selected time slot
                    if ($deliveryStartTime < $selectedSlotTimeStamp) {
                        if ($availability->start_time2 != null && $availability->end_time2 != null) {
                            if ($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time) || $deliveryStartTime >= strtotime($availability->start_time2) && $deliveryStartTime <= strtotime($availability->end_time2)) {
                                $title      = trans('custom.success');
                                $message    = trans('custom.success_restaurant_available');
                                $type       = 'success';
                            }
                        } else {
                            if ($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) {
                                $title      = trans('custom.success');
                                $message    = trans('custom.success_restaurant_available');
                                $type       = 'success';
                            }
                        }
                    } else {
                        $title      = trans('custom.error');
                        $message    = trans('custom.error_restaurant_delivery_slot', ['delaytime' => $minimumDeliveryDelayTime]);
                        $type       = 'error';
                    }
                }
            }

            return json_encode([
                'title'             => $title,
                'message'           => $message,
                'type'              => $type,
            ]);
        }
    }

    /*****************************************************/
    # Function name : dateWiseDeliverySlots
    # Params        : Request $request
    /*****************************************************/
    public function dateWiseDeliverySlots(Request $request)
    {
        // $options = '<option value="">'.trans('custom.option_select').'</option>';
        if ($request->isMethod('POST')) {
            $gettingShopStatus = Helper::gettingShopStatusFlag();
            $selecteddate=!empty($request->delivery_date)?$request->delivery_date:'';
            echo '<option value="">'.trans('custom.option_select').'</option>';
            Helper::generateDeliverySlotNew('options',Helper::dateInYmd($selecteddate));
        }
        exit(); 
    }
    public function dateWiseDeliverySlotsOld_1(Request $request)
    {
        $options = '<option value="">'.trans('custom.option_select').'</option>';
        $k = 1;
        $deliverySlotArr = [];

        if ($request->isMethod('POST')) {
            $siteSettings = Helper::getSiteSettings();
            $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;

            $dayName        = date('l', strtotime(date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)))));
            $today          = date('Y-m-d');
            $selectedDate   = date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)));

            // Start :: special hour
            $shopOpenCloseTimeAccordingToDay = SpecialHour::where('special_date', $selectedDate)->first();
            if ($shopOpenCloseTimeAccordingToDay == null) {
                $shopOpenCloseTimeAccordingToDay = DeliverySlot::where('day_title', $dayName)->first();
            }
            // End :: special hour

            if (strtotime($today) == strtotime($selectedDate)) {
                // If not holiday
                if ($shopOpenCloseTimeAccordingToDay->holiday == 0) {

                    $currentTimeStamp   = strtotime(date('H:i'));
                    // current time + minimum delivery delay = Delivery start time
                    $deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);
                    
                    // Shop open and close time
                    $shopOpenTime       = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
                    $shopCloseTime      = strtotime($shopOpenCloseTimeAccordingToDay->end_time);

                    // Shop open Hour & minute
                    $shopOpenTimeInHour     = date('H', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
                    $shopOpenTimeInMinute   = date('i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
                    
                    $remainder = $shopOpenTimeInMinute % 15;
                    
                    $remainingFromMinute = $shopOpenTimeInMinute - $remainder;
                    
                    if ($remainingFromMinute / 15 == 0) {
                        $slotStartTime = strtotime($shopOpenTimeInHour.':15');
                    }
                    else if ($remainingFromMinute / 15 == 1) {
                        $slotStartTime = strtotime($shopOpenTimeInHour.':30');
                    }
                    else if ($remainingFromMinute / 15 == 2) {
                        $slotStartTime = strtotime($shopOpenTimeInHour.':45');
                    }
                    else if ($remainingFromMinute / 15 == 3) {
                        $slotStartTime = strtotime(($shopOpenTimeInHour+1).':00');
                    }            

                    // slot break up
                    if ($slotStartTime < $shopCloseTime) {
                        // start :: added 01.06.2021
                        $firstSlotStartTime = $shopOpenCloseTimeAccordingToDay->start_time;
                        if ($slotStartTime > strtotime($firstSlotStartTime)) {
                            $slotStartTime = strtotime($firstSlotStartTime);
                        }
                        // end :: added 01.06.2021
                        for ($slotStartTime; $slotStartTime <= $shopCloseTime;) {
                            if ($slotStartTime > $deliveryStartTime) {
                                if ($k == 1) {
                                    if (strtotime($today) == strtotime($selectedDate)) {
                                        $options .= '<option value="'.date('H:i', $slotStartTime).'" data-assoon="Y" selected>'.trans('custom.label_as_soon_as_possible').'</option>';
                                        $deliverySlotArr[] = date('H:i', $slotStartTime);
                                    } else {
                                        $options .= '<option value="'.date('H:i', $slotStartTime).'" data-assoon="" selected>'.date('H:i', $slotStartTime).'</option>';
                                        $deliverySlotArr[] = date('H:i', $slotStartTime);
                                    }
                                } else {
                                    $options .= '<option value="'.date('H:i', $slotStartTime).'" data-assoon="">'.date('H:i', $slotStartTime).'</option>';
                                    $deliverySlotArr[] = date('H:i', $slotStartTime);
                                }
                                $k++;
                            }
                            $slotStartTime = strtotime("+15 minutes", $slotStartTime);
                        }
                    }
                    // start :: added 01.06.2021
                    if ($deliveryStartTime <= $shopCloseTime) {
                        $options .= '<option value="'.date('H:i', $shopCloseTime).'" data-assoon="">'.date('H:i', $shopCloseTime).'</option>';
                        $deliverySlotArr[] = date('H:i', $slotStartTime);
                    }
                    // end :: added 01.06.2021

                    // If slot 2 exist START
                    if ($shopOpenCloseTimeAccordingToDay->start_time2 != null && $shopOpenCloseTimeAccordingToDay->end_time2 != null) {
                        // Shop open and close time
                        $shopOpenTime2      = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                        $shopCloseTime2     = strtotime($shopOpenCloseTimeAccordingToDay->end_time2);

                        // Shop open Hour & minute
                        $shopOpenTimeInHour2     = date('H', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                        $shopOpenTimeInMinute2   = date('i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                        
                        $remainder2 = $shopOpenTimeInMinute2 % 15;
                        
                        $remainingFromMinute2 = $shopOpenTimeInMinute2 - $remainder2;            
                        
                        if ($remainingFromMinute2 / 15 == 0) {
                            $slotStartTime2 = strtotime($shopOpenTimeInHour2.':15');
                        }
                        else if ($remainingFromMinute2 / 15 == 1) {
                            $slotStartTime2 = strtotime($shopOpenTimeInHour2.':30');
                        }
                        else if ($remainingFromMinute2 / 15 == 2) {
                            $slotStartTime2 = strtotime($shopOpenTimeInHour2.':45');
                        }
                        else if ($remainingFromMinute2 / 15 == 3) {
                            $slotStartTime2 = strtotime(($shopOpenTimeInHour2+1).':00');
                        }

                        // slot break up
                        if ($slotStartTime2 < $shopCloseTime2) {
                            // start :: added 01.06.2021
                            $secondSlotStartTime = $shopOpenCloseTimeAccordingToDay->start_time2;
                            if ($slotStartTime2 > strtotime($secondSlotStartTime)) {
                                $slotStartTime2 = strtotime($secondSlotStartTime);
                            }
                            // end :: added 01.06.2021

                            for ($slotStartTime2; $slotStartTime2 <= $shopCloseTime2;) {
                                if ($slotStartTime2 > $deliveryStartTime) {
                                    if ($k == 1) {
                                        if (strtotime($today) == strtotime($selectedDate)) {                                        
                                            $options .= '<option value="'.date('H:i', $slotStartTime2).'" data-assoon="Y" selected>'.trans('custom.label_as_soon_as_possible').'</option>';
                                            $deliverySlotArr[] = date('H:i', $slotStartTime2);
                                        } else {
                                            $options .= '<option value="'.date('H:i', $slotStartTime2).'" data-assoon="" selected>'.date('H:i', $slotStartTime2).'</option>';
                                            $deliverySlotArr[] = date('H:i', $slotStartTime2);
                                        }
                                    }
                                    // else { blocked on 01.06.2021
                                        $options .= '<option value="'.date('H:i', $slotStartTime2).'" data-assoon="">'.date('H:i', $slotStartTime2).'</option>';
                                        $deliverySlotArr[] = date('H:i', $slotStartTime2);
                                    // }
                                    $k++;
                                }
                                $slotStartTime2 = strtotime("+15 minutes", $slotStartTime2);
                            }
                        }

                        // start :: added 01.06.2021
                        if ($deliveryStartTime <= $shopCloseTime2) {
                            $options .= '<option value="'.date('H:i', $shopCloseTime2).'" data-assoon="">'.date('H:i', $shopCloseTime2).'</option>';
                            $deliverySlotArr[] = date('H:i', $shopCloseTime2);
                        }
                        // end :: added 01.06.2021
                    }
                    // If slot 2 exist END

                    if (count($deliverySlotArr)) {
                        $options = '<option value="">'.trans('custom.option_select').'</option>';
                        $options .= '<option value="'.$deliverySlotArr[count($deliverySlotArr) - 1].'" data-assoon="Y" selected>'.trans('custom.label_as_soon_as_possible').'</option>';
                        foreach ($deliverySlotArr as $keySlots => $valSlots) {
                            $options .= '<option value="'.$valSlots.'" data-assoon="">'.$valSlots.'</option>';
                        }
                    }
                }
            } else {
                // If not holiday
                if ($shopOpenCloseTimeAccordingToDay->holiday == 0) {

                    $currentTimeStamp   = strtotime(date('H:i'));
                    // current time + minimum delivery delay = Delivery start time
                    $deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);
                    
                    // Shop open and close time
                    $shopOpenTime       = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
                    $shopCloseTime      = strtotime($shopOpenCloseTimeAccordingToDay->end_time);

                    // Shop open Hour & minute
                    $shopOpenTimeInHour     = date('H', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
                    $shopOpenTimeInMinute   = date('i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
                    
                    $remainder = $shopOpenTimeInMinute % 15;
                    
                    $remainingFromMinute = $shopOpenTimeInMinute - $remainder;
                    
                    if ($remainingFromMinute / 15 == 0) {
                        $slotStartTime = strtotime($shopOpenTimeInHour.':15');
                    }
                    else if ($remainingFromMinute / 15 == 1) {
                        $slotStartTime = strtotime($shopOpenTimeInHour.':30');
                    }
                    else if ($remainingFromMinute / 15 == 2) {
                        $slotStartTime = strtotime($shopOpenTimeInHour.':45');
                    }
                    else if ($remainingFromMinute / 15 == 3) {
                        $slotStartTime = strtotime(($shopOpenTimeInHour+1).':00');
                    }            

                    // slot break up
                    if ($slotStartTime <= $shopCloseTime) {     // added < / <= 01.06.2021
                        // start :: added 01.06.2021
                        $firstSlotStartTime = $shopOpenCloseTimeAccordingToDay->start_time;
                        if ($slotStartTime > strtotime($firstSlotStartTime)) {
                            $slotStartTime = strtotime($firstSlotStartTime);
                        }
                        // end :: added 01.06.2021
                        for ($slotStartTime; $slotStartTime <= $shopCloseTime;) {                            
                            if ($k == 1) {
                                if (strtotime($today) == strtotime($selectedDate)) {                                        
                                    $options .= '<option value="'.date('H:i', $slotStartTime).'" data-assoon="Y" selected>'.trans('custom.label_as_soon_as_possible').'</option>';
                                } else {
                                    $options .= '<option value="'.date('H:i', $slotStartTime).'" data-assoon="" selected>'.date('H:i', $slotStartTime).'</option>';
                                }
                            } else {
                                $options .= '<option value="'.date('H:i', $slotStartTime).'" data-assoon="">'.date('H:i', $slotStartTime).'</option>';
                            }
                            $k++;                            
                            $slotStartTime = strtotime("+15 minutes", $slotStartTime);
                        }
                    }

                    // If slot 2 exist START
                    if ($shopOpenCloseTimeAccordingToDay->start_time2 != null && $shopOpenCloseTimeAccordingToDay->end_time2 != null) {
                        // Shop open and close time
                        $shopOpenTime2      = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                        $shopCloseTime2     = strtotime($shopOpenCloseTimeAccordingToDay->end_time2);

                        // Shop open Hour & minute
                        $shopOpenTimeInHour2     = date('H', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                        $shopOpenTimeInMinute2   = date('i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                        
                        $remainder2 = $shopOpenTimeInMinute2 % 15;
                        
                        $remainingFromMinute2 = $shopOpenTimeInMinute2 - $remainder2;            
                        
                        if ($remainingFromMinute2 / 15 == 0) {
                            $slotStartTime2 = strtotime($shopOpenTimeInHour2.':15');
                        }
                        else if ($remainingFromMinute2 / 15 == 1) {
                            $slotStartTime2 = strtotime($shopOpenTimeInHour2.':30');
                        }
                        else if ($remainingFromMinute2 / 15 == 2) {
                            $slotStartTime2 = strtotime($shopOpenTimeInHour2.':45');
                        }
                        else if ($remainingFromMinute2 / 15 == 3) {
                            $slotStartTime2 = strtotime(($shopOpenTimeInHour2+1).':00');
                        }

                        // slot break up
                        if ($slotStartTime2 <= $shopCloseTime2) {   // added < / <= 01.06.2021
                            // start :: added 01.06.2021
                            $secondSlotStartTime = $shopOpenCloseTimeAccordingToDay->start_time2;
                            if ($slotStartTime2 > strtotime($secondSlotStartTime)) {
                                $slotStartTime2 = strtotime($secondSlotStartTime);
                            }
                            // end :: added 01.06.2021
                            for ($slotStartTime2; $slotStartTime2 <= $shopCloseTime2;) {                                
                                if ($k == 1) {
                                    if (strtotime($today) == strtotime($selectedDate)) {                                        
                                        $options .= '<option value="'.date('H:i', $slotStartTime2).'" data-assoon="Y" selected>'.trans('custom.label_as_soon_as_possible').'</option>';
                                    } else {
                                        $options .= '<option value="'.date('H:i', $slotStartTime2).'" data-assoon="" selected>'.date('H:i', $slotStartTime2).'</option>';
                                    }
                                } else {
                                    $options .= '<option value="'.date('H:i', $slotStartTime2).'" data-assoon="">'.date('H:i', $slotStartTime2).'</option>';
                                }
                                $k++;                                
                                $slotStartTime2 = strtotime("+15 minutes", $slotStartTime2);
                            }

                            // start :: added 01.06.2021
                            if($slotStartTime2 != $shopCloseTime2) {
                                $options .= '<option value="'.date('H:i', $shopCloseTime2).'" data-assoon="">'.date('H:i', $shopCloseTime2).'</option>';
                            }
                            // end :: added 01.06.2021
                        }
                    }
                    // If slot 2 exist END
                }
            }
        }
        echo $options;
    }

    /*****************************************************/
    # Function name : pinCodeWiseDeliveryCharge
    # Params        : Request $request
    /*****************************************************/
    public function pinCodeWiseDeliveryCharge(Request $request)
    {
        $pinCodeWiseDeliveryCharge = '0.00';        
        if ($request->isMethod('POST')) {
            $selectedAddressId = isset($request->selectedAddressId) ? $request->selectedAddressId : '';

            if ($selectedAddressId != '') {
                $getPinCode = DeliveryAddress::select('post_code')->where(['id' => $selectedAddressId, 'user_id' => Auth::user()->id])->first();
                if ($getPinCode != null) {
                    $getDeliveryCharge = PinCode::select('delivery_charge')->where('code', $getPinCode->post_code)->first();
                    $pinCodeWiseDeliveryCharge = Helper::formatToTwoDecimalPlaces($getDeliveryCharge->delivery_charge);
                }
            }
        }
        
        $cartDetails        = Helper::getCartItemDetails();
        $discountAmount     = isset($request->discount_Amount) ? $request->discount_Amount : 0;
        $netPayableAmount   = $cartDetails['payableAmount'] + $pinCodeWiseDeliveryCharge - $discountAmount;
        $paymentMethod      = isset($request->payment_method) ? $request->payment_method : 1;
        $cardAmount         = 0;
        if ($paymentMethod == 2) {
            // $cardAmount     = (($netPayableAmount * 2.9) / 100) + 0.30;
           // $cardAmount     = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
            $cardAmount             = Helper::paymentCardFee($netPayableAmount);
        }
        
        $netPayableAmount = $netPayableAmount + Helper::formatToTwoDecimalPlaces($cardAmount);
        $netPayableAmount = Helper::priceRoundOff($netPayableAmount);

        $siteSettings   = Helper::getSiteSettings();
        if (!Auth::user()) {
            $paymentForm    = view('site.elements.guest_payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        } else {
            $paymentForm    = view('site.elements.payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        }
        $totalAmount = 0;
        $totalAmount = $cartDetails['payableAmount'] + $pinCodeWiseDeliveryCharge;

        $response['has_error']                  = 0;
        $response['msg']                        = trans('custom.success');
        $response['total_amount']               = Helper::formatToTwoDecimalPlaces($totalAmount);
        $response['net_payable_amount']         = Helper::formatToTwoDecimalPlaces($netPayableAmount);
        $response['card_amount']                = Helper::formatToTwoDecimalPlaces($cardAmount);
        $response['payment_form']               = $paymentForm;
        $response['pinCodeWiseDeliveryCharge']  = $pinCodeWiseDeliveryCharge;

        echo json_encode($response);

        // $paymentForm = view('site.elements.payment_form_with_delivery_charge')->with(['cartDetails' => $cartDetails, 'siteSettings' => $siteSettings, 'pinCodeWiseDeliveryCharge' => $pinCodeWiseDeliveryCharge])->render();

        // return json_encode([
        //     'pinCodeWiseDeliveryCharge' => $pinCodeWiseDeliveryCharge,
        //     'payment_form'              => $paymentForm,
        // ]);
    }

    /*****************************************************/
    # Function name : checkout
    # Params        : Request $request
    /*****************************************************/
    public function checkout(Request $request)
    { 
  
        //Helper::checkAsSoonCloseTime('13:15',''); die;
        $currentLang    = $lang = App::getLocale();
        $cmsData        = $metaData = Helper::getMetaData();
        $cartDetails    = Helper::getCartItemDetails();
        $siteSettings   = Helper::getSiteSettings();
        Session::put('couponCode', '');
        Session::put('couponDiscountAmount', '');
        //Session::put('couponCode', '');
       // Session::put('couponDiscountAmount', '');
        Session::put([
            'coupon_code'               => '',
            'calculated_card_amount'    => '',
            'calculated_discount_amount'=> '',
            'coupon_details'            => '',
        ]);
        
        if (count($cartDetails['itemDetails']) == 0) {
            return redirect()->route('site.'.$currentLang.'.home');
        } else if (!Auth::user()) {
            return redirect()->route('site.'.$currentLang.'.guest-checkout');
        } else {
            $doption=!empty(Session::get('deliveryOption'))?Session::get('deliveryOption'):'';
            if ($doption!='Click & Collect' && Cookie::get('minimum_order_amount') > $cartDetails['totalCartPrice']) {
                $request->session()->flash('alert-danger',trans('custom.text_min_order_amount1').' CHF '.Helper::formatToTwoDecimalPlaces(Cookie::get('minimum_order_amount')));
                return redirect()->route('site.'.\App::getLocale().'.home');
            }

            $deliverySlots = Helper::generateDeliverySlot();
            $deliveryAddresses  = DeliveryAddress::where('user_id', Auth::user()->id)->orderBy('id','desc')->get();
            return view('site.checkout',[
                'title'             => $metaData['title'],
                'keyword'           => $metaData['keyword'],
                'description'       => $metaData['description'],
                'cmsData'           => $cmsData,
                'deliverySlots'     => $deliverySlots,
                'deliveryAddresses' => $deliveryAddresses,
                'cartDetails'       => $cartDetails,
                'siteSettings'      => $siteSettings,
                'deliveryOptioncheck'=>$doption,
            ]);
        }
    }

    /*****************************************************/
    # Function name : guestCheckout
    # Params        : Request $request
    /*****************************************************/
    public function guestCheckout(Request $request)
    {
        $currentLang    = $lang = App::getLocale();
        $cmsData        = $metaData = Helper::getMetaData();
        $cartDetails    = Helper::getCartItemDetails();
        $siteSettings   = Helper::getSiteSettings();
        $sessionId      = Session::get('cartSessionId');

        Session::put('couponCode', '');
        Session::put('couponDiscountAmount', '');
        Session::put([
            'coupon_code'               => '',
            'calculated_card_amount'    => '',
            'calculated_discount_amount'=> '',
            'coupon_details'            => '',
        ]);


        if (count($cartDetails['itemDetails']) == 0) {
            return redirect()->route('site.'.$currentLang.'.home');
        } else if (Auth::user()) {
            
            return redirect()->route('site.'.$currentLang.'.checkout');
        } else {
            $doption=!empty(Session::get('deliveryOption'))?Session::get('deliveryOption'):'';
            if ($doption!='Click & Collect' &&  Cookie::get('minimum_order_amount') > $cartDetails['totalCartPrice']) {
                $request->session()->flash('alert-danger',trans('custom.text_min_order_amount1').' CHF '.Helper::formatToTwoDecimalPlaces(Cookie::get('minimum_order_amount')));
                return redirect()->route('site.'.\App::getLocale().'.home');
            }

            $deliverySlots      = Helper::generateDeliverySlot();
            $deliveryAddresses  = DeliveryAddress::where('session_id', $sessionId)->orderBy('id','desc')->first();

            
            
            return view('site.guest_checkout',[
                'title'             => $metaData['title'],
                'keyword'           => $metaData['keyword'],
                'description'       => $metaData['description'],
                'cmsData'           => $cmsData,
                'deliverySlots'     => $deliverySlots,
                'deliveryAddresses' => $deliveryAddresses,
                'cartDetails'       => $cartDetails,
                'siteSettings'      => $siteSettings,
                'deliveryOptioncheck' => $doption
            ]);
        }
    }

    /*****************************************************/
    # Function name : placeOrder
    # Params        : Request $request
    /*****************************************************/
    public function placeOrder(Request $request)
    {
        $currentLang    = App::getLocale();
        $cmsData        = $metaData = Helper::getMetaData();
        $cartDetails    = Helper::getCartItemDetails();
        $siteSettings   = Helper::getSiteSettings();
        $userData       = Auth::user();
        $currentTime    = strtotime(date('Y-m-d H:i'));

        $title              = trans('custom.error');
        $message            = trans('custom.error_required_fields');
        $type               = 'error';
        $redirectPage       = '';
        $emailExist         = 0;
        $oId                = '';
        $addressId          = 0;
        $returnHeaderHTML   = '';

        // echo $sloatsavailable =  Helper::gettingShopStatusFlagPlaceOrder(); die;
        $delivery_date_check=!empty($request->delivery_date)?Helper::dateInYmd($request->delivery_date):date('Y-m-d');
        $siteSettings=Helper::getSiteSettings();
        $doption=!empty(Session::get('deliveryOption'))?Session::get('deliveryOption'):'';
        if($siteSettings->delivery_close_date==$delivery_date_check && $siteSettings->is_delivery_close=='Y' && $siteSettings->is_pickup_close=='N' && $doption!='Click & Collect'){
            return json_encode([
                'message'           => 'At the Moment - Click & Collect and Pre-Order is available. <br> Delivery Service unavailable for now.',
                'type'              =>'error',   
            ]);
            exit();
        }
        if($siteSettings->pickup_close_date==$delivery_date_check && $siteSettings->is_delivery_close=='N' && $siteSettings->is_pickup_close=='Y' && $doption=='Click & Collect'){
            return json_encode([
                'message'           => 'At the Moment - Click & Collect unavailable. <br> Delivery Service and Pre-Order is available.',
                'type'              =>'error',   
            ]);
            exit();
        }

        $timecheck  = !empty($request->delivery_time)?$request->delivery_time:''; 
        $sloatsavailable =  Helper::gettingShopStatusFlagPlaceOrder(); 
        if(!Helper::cehckTimeOncheckout($timecheck,$delivery_date_check)){
            if($delivery_date_check==date('Y-m-d')){
                return json_encode([
                    'message'           => !empty($sloatsavailable)?trans('custom.selected_time_error_for_open'):trans('custom.selected_time_error_for_close'),
                    'type'              =>'refetch_sloat',   
                ]);
                exit();
            }
        }

        if ($request->isMethod('POST')) {
            if ($siteSettings->is_shop_close == 'N') {
                // Guest place order start
                if (!Auth::user()) {
                    $doption=!empty(Session::get('deliveryOption'))?Session::get('deliveryOption'):'';
                    if($doption=='Click & Collect'){
                            $validationCondition = array(
                                'delivery_date' => 'required',
                                'delivery_time' => 'required',
                                'full_name'     => 'required',
                                'phone_no'      => 'required',
                                'email'         => 'required',
                                // 'company'       => 'required',
                                //'street'        => 'required',
                                // 'door_code'     => 'required',
                                //'post_code'     => 'required',
                                //'city'          => 'required',
                                'payment_method'=> 'required',
                            );
                     }else{
                            $validationCondition = array(
                                'delivery_date' => 'required',
                                'delivery_time' => 'required',
                                'full_name'     => 'required',
                                'phone_no'      => 'required',
                                'email'         => 'required',
                                // 'company'       => 'required',
                                'street'        => 'required',
                                // 'door_code'     => 'required',
                                'post_code'     => 'required',
                                'city'          => 'required',
                                'payment_method'=> 'required',
                            );
                     }
                    $validationMessages = array();
                    $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                    if ($Validator->fails()) {
                        $title      = trans('custom.error');
                        $message    = trans('custom.error_required_fields');
                        $type       = 'error';
                    } else {
                        if (Cookie::get('minimum_order_amount') > $cartDetails['totalCartPrice'] && $doption!='Click & Collect') {
                            $title      = trans('custom.error');
                            $message    = trans('custom.text_min_order_amount1').' CHF '.Helper::formatToTwoDecimalPlaces(Cookie::get('minimum_order_amount'));
                            $type       = 'error';
                        } 
                        else {
                            $availabilityStatus  = 0;

                            // Checking restaurant availability START
                            $cDate                  = date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)));
                            $dayName                = date('l', strtotime(date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)))));
                            $ctime=!empty(env('CTIME'))?env('CTIME'):date('H:i');
                            $currentTimeStamp       = strtotime($ctime);
                            $selectedSlotTimeStamp  = strtotime($request->delivery_time);

                            $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
                            // Current time + minimum delivery delay = Delivery start time
                            $deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);
                            
                            // Start :: special hour
                            $availability = SpecialHour::where('special_date', $cDate)->first();
                            if ($availability == null) {
                                $availability = DeliverySlot::where('day_title', $dayName)->first();
                            }
                            // End :: special hour                            

                            // if ($availability != null) {
                            //     if ($availability->holiday == '0') {
                            //         // If order day == delivery day then check start
                            //         if (strtotime(date('Y-m-d')) == strtotime(date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date))))) {
                            //             // If current time is over than selected time slot
                            //             if ($deliveryStartTime < $selectedSlotTimeStamp) {
                            //                 if ($availability->start_time2 != null && $availability->end_time2 != null) {
                            //                     if ($selectedSlotTimeStamp >= strtotime($availability->start_time) && $selectedSlotTimeStamp <= strtotime($availability->end_time) || $selectedSlotTimeStamp >= strtotime($availability->start_time2) && $selectedSlotTimeStamp <= strtotime($availability->end_time2)) {
                            //                         $availabilityStatus = 1;
                            //                     }
                            //                 } else {
                            //                     if ($selectedSlotTimeStamp >= strtotime($availability->start_time) && $selectedSlotTimeStamp <= strtotime($availability->end_time)) {
                            //                         $availabilityStatus = 1;
                            //                     }
                            //                 }
                            //             } else {
                            //                 $availabilityStatus = 2;
                            //             }
                            //         } // If order day == delivery day then check end
                            //         else {
                            //             $availabilityStatus = 1;
                            //         }
                            //     }
                            // }

                            // if ($availabilityStatus == 0) {
                            //     $title      = trans('custom.error');
                            //     $message    = trans('custom.error_restaurant_unavailable');
                            //     $type       = 'error';
                            // }
                            // else if ($availabilityStatus == 2) {
                            //     $title      = trans('custom.error');
                            //     $message    = trans('custom.error_restaurant_delivery_slot', ['delaytime' => $minimumDeliveryDelayTime]);
                            //     $type       = 'error';
                            // }
                            // Checking restaurant availability END
                          //  else {
                                $existUser = User::where(['email' => $request->email, 'type' => 'C', 'status' => '1'])->whereNull('deleted_at')->first();
                                $existPinCode = PinCode::where(['code' => $request->post_code, 'status' => '1'])->count();
                                if ($existPinCode == 0) {
                                    $title      = trans('custom.error');
                                    $message    = trans('custom.error_unavailability');
                                    $type       = 'error';
                                }
                                else {                                
                                    // Start //
                                    $sessionId = Session::get('cartSessionId');

                                    $addressData = DeliveryAddress::where('session_id',$sessionId)->first();
                                    if ($addressData != null) {     // Update session address
                                        $addressData->company    = isset($request->company) ? $request->company : null;
                                        $addressData->street     = isset($request->street) ? $request->street : null;
                                        $addressData->floor      = isset($request->floor) ? $request->floor : null;
                                        $addressData->door_code  = isset($request->door_code) ? $request->door_code :null;
                                        $addressData->post_code  = isset($request->post_code) ? $request->post_code : null;
                                        $addressData->city       = isset($request->city) ? $request->city : null;
                                        $addressData->save();
                                    } else {    // Insert new address
                                        $addressData = new DeliveryAddress;
                                        $addressData->session_id = $sessionId;
                                        $addressData->company    = isset($request->company) ? $request->company : null;
                                        $addressData->street     = isset($request->street) ? $request->street : null;
                                        $addressData->floor      = isset($request->floor) ? $request->floor : null;
                                        $addressData->door_code  = isset($request->door_code) ? $request->door_code :null;
                                        $addressData->post_code  = isset($request->post_code) ? $request->post_code : null;
                                        $addressData->city       = isset($request->city) ? $request->city : null;
                                        $addressData->save();
                                    }

                                    $conditions = ['session_id' => $sessionId, 'type' => 'C', 'order_status' => 'IC'];
                                    $orderUpdate = Order::where($conditions)->first();
                    
                                    // Update Order
                                    $orderUpdate->delivery_type         = (Session::get('deliveryOption') != '') ? Session::get('deliveryOption') : 'Delivery';
                                    $orderUpdate->delivery_charge       = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                                    $orderUpdate->delivery_date         = date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)));
                                    $orderUpdate->delivery_time         = $request->delivery_time;
                                    $orderUpdate->delivery_is_as_soon_as_possible   = isset($request->is_as_soon_as_possible) ? $request->is_as_soon_as_possible : 'N';
                                    $orderUpdate->delivery_full_name    = $request->full_name;
                                    $orderUpdate->delivery_email        = $request->email;
                                    // if (strpos($request->phone_no, '+49') !== false) {
                                    //     $phone = $request->phone_no;
                                    // } else {
                                    //     $phone = env('COUNTRY_CODE','+49').$request->phone_no;
                                    // }
                                    $phone = $request->phone_no;

                                    $orderUpdate->delivery_phone_no     = $phone;
                                    $orderUpdate->delivery_company      = isset($request->company) ? $request->company : null;
                                    $orderUpdate->delivery_street       = isset($request->street) ? $request->street : null;
                                    $orderUpdate->delivery_floor        = isset($request->floor) ? $request->floor : null;
                                    $orderUpdate->delivery_door_code    = isset($request->door_code) ? $request->door_code :null;
                                    $orderUpdate->delivery_post_code    = isset($request->post_code) ? $request->post_code : null;
                                    $orderUpdate->delivery_city         = isset($request->city) ? $request->city : null;
                                    $orderUpdate->purchase_date         = date('Y-m-d H:i:s');
                                    $orderUpdate->delivery_note         = isset($request->checkout_message) ? $request->checkout_message : null;
                                    $orderUpdate->save();

                                    $explodedName = explode(' ', $request->full_name);
                                    if (count($explodedName) > 1) {
                                        $firstName  = $explodedName[0];
                                        $lastName   = $explodedName[1];
                                    } else {
                                        $firstName  = $request->full_name;
                                        $lastName   = ' ';
                                    }

                                    // Cash Payment (COD)
                                    if ($request->payment_method == '1' || $request->payment_method == '3') {
                                        $orderUpdate->payment_method        = $request->payment_method;
                                        $orderUpdate->payment_status        = 'P';
                                        $orderUpdate->type                  = 'O';
                                        $orderUpdate->order_status          = 'O';
                                        
                                        // Update Order Details
                                        OrderDetail::where('order_id', $orderUpdate->id)->update(['order_status' => 'O']);

                                        $oId = Helper::customEncryptionDecryption($orderUpdate->unique_order_id);
                                        
                                        // Guest user to make registration
                                        $guestUserId = '';
                                        if ($existUser == null) {
                                            $userPassword = $this->getRandomPassword();
                                            $newUser = new User();
                                            $newUser->first_name    = $firstName;
                                            $newUser->last_name     = $lastName;
                                            $newUser->full_name     = $request->full_name;
                                            $newUser->email         = $request->email;
                                            // if (strpos($request->phone_no, '+49') !== false) {
                                            //     $phone = $request->phone_no;
                                            // } else {
                                            //     $phone = env('COUNTRY_CODE','+49').$request->phone_no;
                                            // }
                                            $phone = $request->phone_no;

                                            $newUser->phone_no      = $phone;
                                            $newUser->password      = $userPassword;
                                            $newUser->agree         = 1;
                                            $newUser->status        = '1';
                                            $newUser->save();
                                            $guestUserId = $newUser->id;

                                            // Insert into notification
                                            Helper::insertNotification($newUser->id);
                                        } else {
                                            $newUser     = $existUser;
                                            $guestUserId = $existUser->id;

                                            // Delete if something exist in cart
                                            $alreadyExistingCart = Order::where(['user_id' => $existUser->id, 'type' => 'C'])->get();
                                            if ($alreadyExistingCart->count() > 0)  {
                                                foreach ($alreadyExistingCart as $key => $value) {
                                                    OrderIngredientLocal::where(['order_id' => $value->id])->delete();
                                                    OrderIngredient::where(['order_id' => $value->id])->delete();
                                                    OrderAttributeLocal::where(['order_id' => $value->id])->delete();
                                                    OrderDetailLocal::where(['order_id' => $value->id])->delete();
                                                    OrderDetail::where(['order_id' => $value->id])->delete();
                                                    Order::where(['id' => $value->id])->delete();
                                                }
                                            }
                                        }

                                        // update address table
                                        $addressData->session_id    = null;
                                        $addressData->user_id       = $guestUserId;
                                        $addressData->save();

                                        // Coupon section
                                        $cardPayAmountStatus = false;
                                        if (isset($request->coupon_code) && $request->coupon_code != '') {
                                            $appliedCouponCode      = isset($request->coupon_code) ? $request->coupon_code : null;
                                            $deliveryChargeAmount   = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                                            $otherDetails           = Helper::couponCalculation($currentTime, $appliedCouponCode, $deliveryChargeAmount, $cardPayAmountStatus);

                                            $orderUpdate->card_payment_amount   = isset($otherDetails['calculated_card_amount']) ? $otherDetails['calculated_card_amount'] : 0;
                                            $orderUpdate->coupon_code           = $appliedCouponCode;
                                            $orderUpdate->discount_amount       = isset($otherDetails['calculated_discount_amount']) ? $otherDetails['calculated_discount_amount'] : 0;
                                            $orderUpdate->coupon_details        = count($otherDetails['coupon_details']) ? json_encode($otherDetails['coupon_details']) : null;
                                            $orderUpdate->save();

                                            Coupon::where(['code' => $appliedCouponCode, 'is_one_time_use' => 'Y'])->update(['is_used' => 'Y']);
                                        }

                                        // update order table
                                        $orderUpdate->session_id = null;
                                        $orderUpdate->user_id    = $guestUserId;
                                        $orderUpdate->save();

                                        // Mail & SMS Section
                                        $orderDetails   = Helper::getOrderDetails($orderUpdate->id, $guestUserId);

                                        $orderMailSent  = 0;
                                        $orderSmsSent   = 0;
                                        if ($existUser == null) {
                                            // Registration mail to customer
                                            \Mail::send('email_templates.site.guest_registration',
                                            [
                                                'user'          => $newUser,
                                                'password'      => $userPassword,
                                                'siteSetting'   => $siteSettings,
                                                'app_config'    => [
                                                    'appname'       => $siteSettings->website_title,
                                                    'appLink'       => Helper::getBaseUrl(),
                                                    'controllerName'=> 'users',
                                                    'currentLang'=> $currentLang,
                                                ],
                                            ], function ($m) use ($newUser, $siteSettings) {
                                                $m->to($newUser->email, $newUser->full_name)->subject(trans('custom.label_thank_you').' - '.$siteSettings->website_title);
                                            });

                                            $orderMailSent  = 1;
                                            $orderSmsSent   = 1;
                                        } else {
                                            if ($existUser->userNotification != null) {
                                                if ($existUser->userNotification->order_update == '1') {
                                                    $orderMailSent  = 1;
                                                }
                                                if ($existUser->userNotification->sms == '1') {
                                                    $orderSmsSent  = 1;
                                                }
                                            }                                        
                                        }

                                        // Order mail to customer
                                        if ($orderMailSent == 1) {
                                            /* 06.04.2021
                                            \Mail::send('email_templates.site.order_details_to_customer',
                                            [
                                                'user'          => $newUser,
                                                'siteSetting'   => $siteSettings,
                                                'orderDetails'  => $orderDetails,
                                                'getOrderData'  => $orderUpdate,
                                                'app_config'    => [
                                                    'appname'       => $siteSettings->website_title,
                                                    'appLink'       => Helper::getBaseUrl(),
                                                    'currentLang'   => $currentLang,
                                                ],
                                            ], function ($m) use ($newUser) {
                                                $m->to($newUser->email, $newUser->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                            });
                                            06.04.2021 */
                                        }
            
                                        // SMS to customer
                                        // if ($orderSmsSent == 1) {
                                            // $sendSms = $this->sendOrderMessage($phone, $orderUpdate->unique_order_id);
                                        // }

                                        // Mail to admin
                                        \Mail::send('email_templates.site.order_details_to_admin',
                                        [
                                            'user'          => $userData,
                                            'siteSetting'   => $siteSettings,
                                            'orderDetails'  => $orderDetails,
                                            'getOrderData'  => $orderUpdate,
                                            'app_config'    => [
                                                'appname'       => $siteSettings->website_title,
                                                'appLink'       => Helper::getBaseUrl(),
                                                'currentLang'   => $currentLang,
                                            ],
                                        ], function ($m) use ($siteSettings, $newUser) {
                                            $ordertoemail=!empty($siteSettings->to_email)?$siteSettings->to_email:env('ORDER_EMAIL');
                                            $m->to($ordertoemail, $siteSettings->website_title)->replyTo($newUser->email)->subject(trans('custom.message_new_order_placed').' - '.trans('custom.label_web_site_title'));
                                        });

                                        Session::put([
                                            'deliveryOption'    => '',
                                            'cartSessionId'     => '',
                                            'redirectTo'        => '',
                                            'guest_email'       => ''
                                        ]);

                                        $request->session()->flash('alert-success',trans('custom.success_order_placed_success'));
                                        $title          = trans('custom.success');
                                        $message        = trans('custom.success_order_placed_success');
                                        $type           = 'success';
                                        // $redirectPage   = 'home';
                                        $redirectPage   = 'thank-you';
                                    }
                                    else if ($request->payment_method == '2' || $request->payment_method == '4') {  // Card Payment
                                        // if (strpos($request->phone_no, '+49') !== false) {
                                        //     $phone = $request->phone_no;
                                        // } else {
                                        //     $phone = env('COUNTRY_CODE','+49').$request->phone_no;
                                        // }

                                        $phone = $request->phone_no;

                                        Session::put([
                                                    'guest_full_name'   => $request->full_name,
                                                    'guest_first_name'  => $firstName,
                                                    'guest_last_name'   => $lastName,
                                                    'guest_email'       => $request->email,
                                                    'guest_phone_no'    => $phone,
                                                ]);

                                        // Coupon section
                                        $cardPayAmountStatus = true;
                                        if (isset($request->coupon_code) && $request->coupon_code != '') {
                                            $appliedCouponCode      = isset($request->coupon_code) ? $request->coupon_code : null;
                                            $deliveryChargeAmount   = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                                            $otherDetails           = Helper::couponCalculation($currentTime, $appliedCouponCode, $deliveryChargeAmount, $cardPayAmountStatus);

                                            Session::put([
                                                'coupon_code'               => $appliedCouponCode,
                                                'calculated_card_amount'    => isset($otherDetails['calculated_card_amount']) ? $otherDetails['calculated_card_amount'] : 0,
                                                'calculated_discount_amount'=> isset($otherDetails['calculated_discount_amount']) ? $otherDetails['calculated_discount_amount'] : 0,
                                                'coupon_details'            => count($otherDetails['coupon_details']) ? json_encode($otherDetails['coupon_details']) : null,
                                            ]);
                                        } else {
                                            Session::put([
                                                'calculated_card_amount'    => isset($request->card_amount) ? $request->card_amount : 0,
                                            ]);
                                        }

                                        $title          = trans('custom.success');
                                        $message        = trans('custom.success_order_payment_processing');
                                        $type           = 'success';
                                        $redirectPage   = '';
                                    }
                                    
                                    // End //
                                }
                           // }
                        }
                    }
                }
                // Guest place order end
                // Logged in user place order
                else {
                    $doption=!empty(Session::get('deliveryOption'))?Session::get('deliveryOption'):'';
                    if($doption=='Click & Collect'){
                        $validationCondition = array(
                            'delivery_date'     => 'required',
                            'delivery_time'     => 'required',
                            'phone_no'          => 'required',
                            //'addressAlias'      => 'required',
                            // 'checkout_message'  => 'required',
                        );
                    }else{
                        $validationCondition = array(
                            'delivery_date'     => 'required',
                            'delivery_time'     => 'required',
                            'phone_no'          => 'required',
                            'addressAlias'      => 'required',
                            // 'checkout_message'  => 'required',
                        );
                    }
                    $validationMessages = array(
                        'delivery_date.required'    => trans('custom.error_please_select_delivery_date'),
                        'delivery_time.required'    => trans('custom.error_please_select_delivery_time'),
                        'phone_no.required'         => trans('custom.error_please_enter_phone_no'),
                        'addressAlias.required'     => trans('custom.error_please_select_delivery_address'),
                        // 'checkout_message.required' => trans('custom.error_please_leave_a_message'),
                    );
                    $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                    if ($Validator->fails()) {
                        $title      = trans('custom.error');
                        $message    = trans('custom.error_required_fields');
                        $type       = 'error';
                    } else {
                        if (Cookie::get('minimum_order_amount') > $cartDetails['totalCartPrice'] && $doption!='Click & Collect') {
                            $title      = trans('custom.error');
                            $message    = trans('custom.text_min_order_amount1').' CHF '.Helper::formatToTwoDecimalPlaces(Cookie::get('minimum_order_amount'));
                            $type       = 'error';
                        }
                        else {
                            $availabilityStatus  = 0;

                            // Checking restaurant availability START
                            $curDate                = date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)));
                            $dayName                = date('l', strtotime(date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)))));
                            $currentTimeStamp       = strtotime(date('H:i'));
                            $selectedSlotTimeStamp  = strtotime($request->delivery_time);

                            $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
                            // Current time + minimum delivery delay = Delivery start time
                            $deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);
                            
                            // Start :: special hour
                            $availability = SpecialHour::where('special_date', $curDate)->first();
                            if ($availability == null) {
                                $availability = DeliverySlot::where('day_title', $dayName)->first();
                            }
                            // End :: special hour                            

                            // if ($availability != null) {
                            //     if ($availability->holiday == '0') {
                            //         // If order day == delivery day then check start
                            //         if (strtotime(date('Y-m-d')) == strtotime(date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date))))) {
                            //             // If current time is over than selected time slot
                            //             if ($deliveryStartTime < $selectedSlotTimeStamp) {
                            //                 if ($availability->start_time2 != null && $availability->end_time2 != null) {
                            //                     if ($selectedSlotTimeStamp >= strtotime($availability->start_time) && $selectedSlotTimeStamp <= strtotime($availability->end_time) || $selectedSlotTimeStamp >= strtotime($availability->start_time2) && $selectedSlotTimeStamp <= strtotime($availability->end_time2)) {
                            //                         $availabilityStatus = 1;
                            //                     }
                            //                 } else {
                            //                     if ($selectedSlotTimeStamp >= strtotime($availability->start_time) && $selectedSlotTimeStamp <= strtotime($availability->end_time)) {
                            //                         $availabilityStatus = 1;
                            //                     }
                            //                 }
                            //             } else {
                            //                 $availabilityStatus = 2;
                            //             }
                            //         }   // If order day == delivery day then check end
                            //         else {
                            //             $availabilityStatus = 1;
                            //         }
                            //     }
                            // }

                            // if ($availabilityStatus == 0) {
                            //     $title      = trans('custom.error');
                            //     $message    = trans('custom.error_restaurant_unavailable');
                            //     $type       = 'error';
                            // }
                            // else if ($availabilityStatus == 2) {
                            //     $title      = trans('custom.error');
                            //     $message    = trans('custom.error_restaurant_delivery_slot', ['delaytime' => $minimumDeliveryDelayTime]);
                            //     $type       = 'error';
                            // }
                            // Checking restaurant availability END
                           // else {
                                $userId     = Auth::user()->id;
                                $userData   = Auth::user();

                                $conditions = ['user_id' => $userId, 'type' => 'C', 'order_status' => 'IC'];
                                $orderUpdate = Order::where($conditions)->first();
                
                                // Getting address details
                                $userAddress = DeliveryAddress::where(['id' => $request->addressAlias, 'user_id' => $userId])->first();
                                 
                                // Update Order
                                $orderUpdate->session_id            = null;
                                $orderUpdate->delivery_type         = (Session::get('deliveryOption') != '') ? Session::get('deliveryOption') : 'Delivery';
                                $orderUpdate->delivery_charge       = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                                $orderUpdate->delivery_date         = date('Y-m-d',strtotime(str_replace('/','-',$request->delivery_date)));
                                $orderUpdate->delivery_time         = $request->delivery_time;
                                $orderUpdate->delivery_is_as_soon_as_possible   = isset($request->is_as_soon_as_possible) ? $request->is_as_soon_as_possible : 'N';
                                $orderUpdate->delivery_full_name    = $request->full_name;
                                $orderUpdate->delivery_email        = $userData->email;
                                // if (strpos($request->phone_no, '+49') !== false) {
                                //     $phone = $request->phone_no;
                                // } else {
                                //     $phone = env('COUNTRY_CODE','+49').$request->phone_no;
                                // }
                                $phone = $request->phone_no;

                                User::where(['id' => $userId])->update(['phone_no' => $phone]);

                                $orderUpdate->delivery_phone_no     = $phone;
                                if($doption!='Click & Collect'){
                                    $orderUpdate->delivery_company      = !empty($userAddress->company)?$userAddress->company:'';
                                    $orderUpdate->delivery_street       = !empty($userAddress->street)?$userAddress->street:'';
                                    $orderUpdate->delivery_floor        = !empty($userAddress->floor)?$userAddress->floor:'';
                                    $orderUpdate->delivery_door_code    = !empty($userAddress->door_code)?$userAddress->door_code:'';
                                    $orderUpdate->delivery_post_code    = !empty($userAddress->post_code)?$userAddress->post_code:'';
                                    $orderUpdate->delivery_city         = !empty($userAddress->city)?$userAddress->city:'';
                                    $orderUpdate->delivery_alias_type   = !empty($userAddress->alias_type)?$userAddress->alias_type:'';
                                    $orderUpdate->delivery_own_alias    = !empty($userAddress->own_alias)?$userAddress->own_alias:'';
                                }
                                $orderUpdate->purchase_date         = date('Y-m-d H:i:s');
                                $orderUpdate->delivery_note         = isset($request->checkout_message) ? $request->checkout_message : null;
                                $orderUpdate->save();

                                if ($request->payment_method == '1' || $request->payment_method == '3') {  // Cash Payment
                                    $orderUpdate->payment_method        = $request->payment_method;
                                    $orderUpdate->payment_status        = 'P';
                                    $orderUpdate->type                  = 'O';
                                    $orderUpdate->order_status          = 'O';
                                    //$orderUpdate->save();

                                    // Coupon section
                                    $cardPayAmountStatus = false;
                                    if (isset($request->coupon_code) && $request->coupon_code != '') {
                                        $appliedCouponCode      = isset($request->coupon_code) ? $request->coupon_code : null;
                                        $deliveryChargeAmount   = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                                        $otherDetails           = Helper::couponCalculation($currentTime, $appliedCouponCode, $deliveryChargeAmount, $cardPayAmountStatus);

                                        $orderUpdate->card_payment_amount   = isset($otherDetails['calculated_card_amount']) ? $otherDetails['calculated_card_amount'] : 0;
                                        $orderUpdate->coupon_code           = $appliedCouponCode;
                                        $orderUpdate->discount_amount       = isset($otherDetails['calculated_discount_amount']) ? $otherDetails['calculated_discount_amount'] : 0;
                                        $orderUpdate->coupon_details        = count($otherDetails['coupon_details']) ? json_encode($otherDetails['coupon_details']) : null;
                                        $orderUpdate->save();

                                        Coupon::where(['code' => $appliedCouponCode, 'is_one_time_use' => 'Y'])->update(['is_used' => 'Y']);
                                    }else{
                                         $orderUpdate->save();
                                    }  
                                    
                                    // Update Order Details
                                    OrderDetail::where('order_id', $orderUpdate->id)->update(['order_status' => 'O']);

                                    $oId = Helper::customEncryptionDecryption($orderUpdate->unique_order_id);
                                    
                                    // Mail & SMS Section
                                    $orderDetails   = Helper::getOrderDetails($orderUpdate->id, Auth::user()->id);
                                    if ($userData->userNotification != null) {
                                        // Mail to customer
                                        if ($userData->userNotification->order_update == '1') {
                                            /* 06.04.2021
                                            \Mail::send('email_templates.site.order_details_to_customer',
                                            [
                                                'user'          => $userData,
                                                'siteSetting'   => $siteSettings,
                                                'orderDetails'  => $orderDetails,
                                                'getOrderData'  => $orderUpdate,
                                                'app_config'    => [
                                                    'appname'       => $siteSettings->website_title,
                                                    'appLink'       => Helper::getBaseUrl(),
                                                    'currentLang'   => $currentLang,
                                                ],
                                            ], function ($m) use ($userData) {
                                                $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                            });
                                            06.04.2021 */
                                        }
        
                                        // SMS to customer
                                        if ($userData->userNotification->sms == '1') {
                                            // $sendSms = $this->sendOrderMessage($phone, $orderUpdate->unique_order_id);
                                        }
                                    }

                                    // Mail to admin
                                    \Mail::send('email_templates.site.order_details_to_admin',
                                    [
                                        'user'          => $userData,
                                        'siteSetting'   => $siteSettings,
                                        'orderDetails'  => $orderDetails,
                                        'getOrderData'  => $orderUpdate,
                                        'app_config'    => [
                                            'appname'       => $siteSettings->website_title,
                                            'appLink'       => Helper::getBaseUrl(),
                                            'currentLang'   => $currentLang,
                                        ],
                                    ], function ($m) use ($siteSettings,$userData) {
                                        $ordertoemail=!empty($siteSettings->to_email)?$siteSettings->to_email:env('ORDER_EMAIL');
                                        $m->to($ordertoemail, $siteSettings->website_title)->replyTo($userData->email)->subject(trans('custom.message_new_order_placed').' - '.trans('custom.label_web_site_title'));
                                    });

                                    Session::put([
                                        'deliveryOption'    => '',
                                        'cartSessionId'     => '',
                                        'redirectTo'        => '',
                                        'guest_email'       => ''
                                    ]);

                                    $request->session()->flash('alert-success',trans('custom.success_order_placed_success'));
                                    $title          = trans('custom.success');
                                    $message        = trans('custom.success_order_placed_success');
                                    $type           = 'success';
                                    // $redirectPage   = 'orders-reviews';
                                    $redirectPage   = 'thank-you';
                                }
                                else if ($request->payment_method == '2' || $request->payment_method == '4') {  // Card Payment
                                    // Coupon section
                                    $cardPayAmountStatus = true;
                                    if (isset($request->coupon_code) && $request->coupon_code != '') {
                                        $appliedCouponCode      = isset($request->coupon_code) ? $request->coupon_code : null;
                                        $deliveryChargeAmount   = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                                        $otherDetails           = Helper::couponCalculation($currentTime, $appliedCouponCode, $deliveryChargeAmount, $cardPayAmountStatus);

                                        Session::put([
                                            'coupon_code'               => $appliedCouponCode,
                                            'calculated_card_amount'    => isset($otherDetails['calculated_card_amount']) ? $otherDetails['calculated_card_amount'] : 0,
                                            'calculated_discount_amount'=> isset($otherDetails['calculated_discount_amount']) ? $otherDetails['calculated_discount_amount'] : 0,
                                            'coupon_details'            => count($otherDetails['coupon_details']) ? json_encode($otherDetails['coupon_details']) : null,
                                        ]);
                                    } else {
                                        Session::put([
                                            'calculated_card_amount'    => isset($request->card_amount) ? $request->card_amount : 0,
                                        ]);
                                    }

                                    $title          = trans('custom.success');
                                    $message        = trans('custom.success_order_payment_processing');
                                    $type           = 'success';
                                    $redirectPage   = '';
                                }
                          //  }
                        }
                    }
                }
            } else {
                $message = trans('custom.message_we_are_not_accepting_order_now');
            }
        }
        return json_encode([
            'title'             => $title,
            'message'           => $message,
            'type'              => $type,
            'redirectPage'      => $redirectPage,
            'emailExist'        => $emailExist,
            'oId'               => Helper::customEncryptionDecryption($oId, 'decrypt'),
            'addressId'         => $addressId,
            'returnHeaderHTML'  => $returnHeaderHTML
        ]);
    }

    /*****************************************************/
    # Function name : paymentProcessStripe
    # Params        : Request $request
    /*****************************************************/
    public function paymentProcessStripe(Request $request)
    {
        if ($request->isMethod('POST')) {
            $payment_setting    = Helper::getPaymentSettings();
            $stripe_method=!empty($payment_setting)?$payment_setting->stripe_method:''; 
            if($stripe_method=='Y'){
                $secretKey=$payment_setting->stripe_secret_key;
            }else{
               // $secretKey = env('STRIPE_SECRET_KEY', 'sk_test_51GygeyH2e89LEoDsnN1QgFTrZMz6fOp2yoliHyErf89dd1IbvzkZr6ouJZJBmAvpyvlMykEZW788xjlbkH4zWq9A00X94Paxur');
               $response=['errorget' => 1, 'error' =>trans('custom.please_try_again')]; 
               echo json_encode($response); 
               exit();
            }
            \Stripe\Stripe::setApiKey($secretKey);
            $token='';
            $expdate=!empty($request->exp_date)?explode('/',$request->exp_date):[];
            $monthexp=$yearexp='';
            $cardnumber=trim($request->card_number);
            $cvc=trim($request->cvc_number);
            if(count($expdate)>1){
                $monthexp=$expdate['0'];
                $yearexp=$expdate['1'];
            }
            
            if($monthexp && $yearexp && $cardnumber && $cvc){
               
                    try {
                            $responseToken = \Stripe\Token::create(array(
                                "card" => array(
                                    "number"    => $cardnumber,
                                    "exp_month" => $monthexp,
                                    "exp_year"  => $yearexp,
                                    "cvc"       => $cvc,
                                    //"name"      => $request->name
                            )));
                            $token=!empty($responseToken->id)?$responseToken->id:''; 
                        }catch (\Stripe\Exception\CardException $e) {
                            $currentLang        = App::getLocale();
                            if($currentLang=='de'){ 
                               $response=['errorget' => 1, 'error' => 'Het kaartnummer is geen geldig creditcardnummer.'];  
                            }else{
                               $response=['errorget' => 1, 'error' => $e->getError()->message];  
                            }
                            echo json_encode($response); 
                            exit();             
                        }
             }

           // $token = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : '';
            if ($token != '') {
                $currentLang        = App::getLocale();
                $cartDetails        = Helper::getCartItemDetails();
                $siteSettings       = Helper::getSiteSettings();

                if (count($cartDetails['itemDetails']) == 0) {
                    // $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                    // return redirect()->route('site.'.\App::getLocale().'.home');
                    $response=['errorget' => 1, 'error' => trans('custom.please_try_again')];  
                    echo json_encode($response);              
                } else {
 
                    $discountAmount     = Session::get('couponDiscountAmount') ? Session::get('couponDiscountAmount') : 0;
                    $netPayableAmountss   = ($cartDetails['totalCartPrice'] + $cartDetails['deliveryCharges']) - $discountAmount;
                    $netPayableAmountss   = $netPayableAmountss + Helper::formatToTwoDecimalPlaces(Helper::paymentCardFee($netPayableAmountss));

                    $netPayableAmountss   = Helper::priceRoundOff($netPayableAmountss); 


                    $getOrderData = Order::where([
                                                'id'            => $cartDetails['cartOrderId'],
                                                'type'          => 'C',
                                                'order_status'  => 'IC',
                                            ])
                                            ->first();
                   try{
                        $charge = \Stripe\Charge::create([
                            'amount'        => $netPayableAmountss * 100,
                            'currency'      => env('WEBSITE_CURRENCY', 'CHF'),
                            'description'   => $siteSettings->website_title,
                            'source'        => $token
                        ]);
                    }
                      catch (\Stripe\Exception\CardException $e) {
                         $response= ['errorget' => 1, 'error' => $e->getError()->message, 'code' => $e->getError()->code];
                         echo json_encode($response);          
                         exit();
                      }
                      catch (Exception $e) {
                         $response= ['errorget' => 1, 'error' => $e->getError()->message, 'code' => 'transaction_error'];
                         echo json_encode($response);          
                         exit();
                      }
                    
                    if (isset($charge->id) && isset($charge->status) && $charge->id != '' && $charge->status == 'succeeded') {                        
                        if (!Auth::user()) {
                            $explodedName = explode(' ', $request->full_name);
                            if (count($explodedName) > 1) {
                                $firstName  = $explodedName[0];
                                $lastName   = $explodedName[1];
                            } else {
                                $firstName  = $request->full_name;
                                $lastName   = ' ';
                            }
                            
                            $phone = null;
                            if (Session::get('guest_phone_no') != '') {
                                // if (strpos(Session::get('guest_phone_no'), '+49') !== false) {
                                //     $phone = Session::get('guest_phone_no');
                                // } else {
                                //     $phone = env('COUNTRY_CODE','+49').Session::get('guest_phone_no');
                                // }
                                $phone = Session::get('guest_phone_no');
                            }
                            // Guest user to make registration
                            $guestEmailExist = User::where(['email' => Session::get('guest_email'), 'status' => '1', 'type' => 'C'])->whereNull('deleted_at')->first();
                            $guestUserId = '';
                            if ($guestEmailExist == null) {
                                $userPassword = $this->getRandomPassword();
                                $newUser = new User();
                                $newUser->first_name    = Session::get('guest_first_name') ? Session::get('guest_first_name') : null;
                                $newUser->last_name     = Session::get('guest_last_name') ? Session::get('guest_last_name') : null;
                                $newUser->full_name     = Session::get('guest_full_name') ? Session::get('guest_full_name') : null;
                                $newUser->email         = Session::get('guest_email') ? Session::get('guest_email') : null;
                                $newUser->phone_no      = $phone;
                                $newUser->password      = $userPassword;
                                $newUser->agree         = 1;
                                $newUser->status        = '1';
                                $newUser->save();

                                $guestUserId = $newUser->id;

                                // Insert into notification
                                Helper::insertNotification($newUser->id);
                            } else {
                                $newUser     = $guestEmailExist;
                                $guestUserId = $guestEmailExist->id;

                                // Delete if something exist in cart
                                $alreadyExistingCart = Order::where(['user_id' => $guestEmailExist->id, 'type' => 'C'])->get();
                                if ($alreadyExistingCart->count() > 0)  {
                                    foreach ($alreadyExistingCart as $key => $value) {
                                        OrderIngredientLocal::where(['order_id' => $value->id])->delete();
                                        OrderIngredient::where(['order_id' => $value->id])->delete();
                                        OrderAttributeLocal::where(['order_id' => $value->id])->delete();
                                        OrderDetailLocal::where(['order_id' => $value->id])->delete();
                                        OrderDetail::where(['order_id' => $value->id])->delete();
                                        Order::where(['id' => $value->id])->delete();
                                    }
                                }
                            }

                            // update address table
                            $addressData = DeliveryAddress::where('session_id',Session::get('cartSessionId'))->first();
                            if ($addressData != null) {
                                $addressData->session_id    = null;
                                $addressData->user_id       = $guestUserId;
                                $addressData->save();
                            }                

                            // update order table
                            $getOrderData->session_id = null;
                            $getOrderData->user_id    = $guestUserId;
                            $getOrderData->save();

                            $orderedUserId = $guestUserId;
                        } else {
                            $orderedUserId = Auth::user()->id;
                        }

                        $userData = User::where('id',$orderedUserId)->first();

                        if ($getOrderData != null) {
                            $transactionResponse['id']                  = $charge->id;
                            $transactionResponse['object']              = isset($charge->object) ? $charge->object : '';
                            $transactionResponse['balance_transaction'] = isset($charge->balance_transaction) ? $charge->balance_transaction : '';
                            $transactionResponse['payment_method']      = isset($charge->payment_method) ? $charge->payment_method : '';
                            $transactionResponse['receipt_url']         = isset($charge->receipt_url) ? $charge->receipt_url : '';
                            $transactionResponse['status']              = isset($charge->status) ? $charge->status : '';

                            $getOrderData->coupon_code                  = Session::get('coupon_code') ? Session::get('coupon_code') : null;
                            $getOrderData->card_payment_amount          = Session::get('calculated_card_amount') ? Session::get('calculated_card_amount') : 0;
                            $getOrderData->discount_amount              = Session::get('calculated_discount_amount') ? Session::get('calculated_discount_amount') : 0;
                            $getOrderData->coupon_details               = Session::get('coupon_details') ? Session::get('coupon_details') : null;
                            $getOrderData->payment_status               = 'C';
                            $getOrderData->type                         = 'O';
                            $getOrderData->payment_method               = '2';
                            $getOrderData->order_status                 = 'O';
                            $getOrderData->transaction_id               = $charge->id;
                            $getOrderData->transaction_response         = json_encode($transactionResponse);
                            $getOrderData->save();

                            Coupon::where(['code' => $getOrderData->coupon_code, 'is_one_time_use' => 'Y'])->update(['is_used' => 'Y']);
                            
                            // Update Order Details
                            OrderDetail::where('order_id', $getOrderData->id)->update(['order_status' => 'O']);
                            
                            $orderDetails = Helper::getOrderDetails($getOrderData->id, $orderedUserId);

                            if ($userData->userNotification != null) {
                                // Mail to customer
                                if ($userData->userNotification->order_update == '1') {
                                    /* 06.04.2021
                                    \Mail::send('email_templates.site.order_details_to_customer',
                                    [
                                        'user'          => $userData,
                                        'siteSetting'   => $siteSettings,
                                        'orderDetails'  => $orderDetails,
                                        'getOrderData'  => $getOrderData,
                                        'app_config'    => [
                                            'appname'       => $siteSettings->website_title,
                                            'appLink'       => Helper::getBaseUrl(),
                                            'currentLang'   => $currentLang,
                                        ],
                                    ], function ($m) use ($userData) {
                                        $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                    });
                                    06.04.2021 */
                                }

                                // SMS to customer
                                if ($userData->userNotification->sms == '1') {
                                    // $sendSms = $this->sendOrderMessage($getOrderData->delivery_phone_no, $getOrderData->unique_order_id);
                                }
                            }
                            else if ( Session::get('guest_full_name') != '' && Session::get('guest_first_name') != '' && Session::get('guest_email') != '' ) {
                                // Registration mail to customer
                                $guestUserExist = User::where(['email'=>Session::get('guest_email'), 'status' => '1', 'type' => 'C'])->whereNull('deleted_at')->first();
                                if ($guestUserExist == null) {
                                    if ($guestUserExist->userNotification != null) {
                                        // Mail to customer
                                        if ($guestUserExist->userNotification->order_update == '1') {
                                            \Mail::send('email_templates.site.guest_registration',
                                            [
                                                'user'          => $userData,
                                                'password'      => $userPassword,
                                                'siteSetting'   => $siteSettings,
                                                'app_config'    => [
                                                    'appname'       => $siteSettings->website_title,
                                                    'appLink'       => Helper::getBaseUrl(),
                                                    'controllerName'=> 'users',
                                                    'currentLang'=> $currentLang,
                                                ],
                                            ], function ($m) use ($newUser, $siteSettings) {
                                                $m->to($newUser->email, $newUser->full_name)->subject(trans('custom.label_thank_you').' - '.$siteSettings->website_title);
                                            });
                                        }
                                    }                                    
                                }

                                // Order email to customer
                                /* 06.04.2021
                                \Mail::send('email_templates.site.order_details_to_customer',
                                    [
                                        'user'          => $userData,
                                        'siteSetting'   => $siteSettings,
                                        'orderDetails'  => $orderDetails,
                                        'getOrderData'  => $getOrderData,
                                        'app_config'    => [
                                            'appname'       => $siteSettings->website_title,
                                            'appLink'       => Helper::getBaseUrl(),
                                            'currentLang'   => $currentLang,
                                        ],
                                    ], function ($m) use ($userData) {
                                        $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                    });
                                06.04.2021 */
                            }

                            Session::put([
                                'deliveryOption'            => '',
                                'cartSessionId'             => '',
                                'redirectTo'                => '',
                                'guest_full_name'           => '',
                                'guest_first_name'          => '',
                                'guest_last_name'           => '',
                                'guest_email'               => '',
                                'guest_phone_no'            => '',
                                'coupon_code'               => '',
                                'calculated_card_amount'    => '',
                                'calculated_discount_amount'=> '',
                                'coupon_details'            => ''
                            ]);

                            // Mail to admin
                            \Mail::send('email_templates.site.order_details_to_admin',
                            [
                                'user'          => $userData,
                                'siteSetting'   => $siteSettings,
                                'orderDetails'  => $orderDetails,
                                'getOrderData'  => $getOrderData,
                                'app_config'    => [
                                    'appname'       => $siteSettings->website_title,
                                    'appLink'       => Helper::getBaseUrl(),
                                    'currentLang'   => $currentLang,
                                ],
                            ], function ($m) use ($siteSettings,$userData) {
                                $ordertoemail=!empty($siteSettings->to_email)?$siteSettings->to_email:env('ORDER_EMAIL');
                                $m->to($ordertoemail, $siteSettings->website_title)->replyTo($userData->email)->subject(trans('custom.message_new_order_placed').' - '.trans('custom.label_web_site_title'));
                            });
                            
                            $response=['errorget' => 0,'oid'=>$getOrderData->unique_order_id,'error' => trans('custom.success_order_placed_success')];  
                            echo json_encode($response);    
                           // $request->session()->flash('alert-success',trans('custom.success_order_placed_success'));
                            //return redirect()->route('site.'.$currentLang.'.thank-you', $getOrderData->unique_order_id);
                            
                        } else {
                            // $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                            // if (Auth::user()) {
                            //     return redirect()->route('site.'.\App::getLocale().'.checkout');
                            // } else {
                            //     return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
                            // }
                            $response=['errorget' => 1, 'error' => trans('custom.please_try_again')];  
                            echo json_encode($response);    
                        }
                    } else {
                        // $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                        // if (Auth::user()) {
                        //     return redirect()->route('site.'.\App::getLocale().'.checkout');
                        // } else {
                        //     return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
                        // }
                        $response=['errorget' => 1, 'error' => trans('custom.please_try_again')];  
                        echo json_encode($response);    
                    }
                }
            } else {
                // $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                // if (Auth::user()) {
                //     return redirect()->route('site.'.\App::getLocale().'.checkout');
                // } else {
                //     return redirect()->route('site.'.\App::getLocale().'.guest-checkout');
                // }
                $response=['errorget' => 1, 'error' => trans('custom.please_try_again')]; 
                echo json_encode($response);    
            }
        } else {
            abort(404);
        }
    }

    /*****************************************************/
    # Function name : thankYou
    # Params        : Request $request
    /*****************************************************/
    public function thankYou(Request $request, $uniqueOrderId = null)
    {
        $currentLang    = App::getLocale();
        $cmsData        = $metaData = Helper::getMetaData();

        if ($uniqueOrderId == null) {
            return redirect()->route('site.'.$currentLang.'.home');
        }
        $uniqueOrderId  = $uniqueOrderId;
        $orderData      = Order::where(['unique_order_id' => $uniqueOrderId])->first();        
        $orderDetails   = Helper::getOrderDetails($orderData->id, $orderData->user_id);

        if ($orderData == null) {
            return redirect()->route('site.'.$currentLang.'.home');
        }

        return view('site.thank_you',[
            'title'         => $metaData['title'],
            'keyword'       => $metaData['keyword'],
            'description'   => $metaData['description'],
            'cmsData'       => $cmsData,
            'uniqueOrderId' => $uniqueOrderId,            
            'orderDetails'  => $orderDetails
        ]);
    }

}
