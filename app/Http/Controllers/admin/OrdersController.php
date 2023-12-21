<?php
/*****************************************************/
# Page/Class name   : OrdersController
/*****************************************************/

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App;
use AdminHelper;
use Helper;
use Redirect;
use Validator;
use View;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SiteSetting;
use App\Models\User;
use Twilio\Rest\Client;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class OrdersController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_order_list');
        $data['panel_title']= trans('custom_admin.lab_order_list');
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);

            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            $filter = array();
            
            $purchaseDate = $request->purchase_date;
            $filterDate = false;
            
            if ($purchaseDate) {
                $filterDate = true;
                $filter['purchaseDate'] = $purchaseDate;
                $date = explode(" - ",$purchaseDate);
                $minDate = date('Y-m-d', strtotime($date[0]));
                $maxDate = date('Y-m-d', strtotime($date[1]));
            }
            $searchText = $request->searchText;
            $filterSearch = false;
            
            if($searchText){
                $filterSearch = true;
                $filter['searchText'] = $searchText;
            }
            $paymentMethod = $request->payment_method;
            $filterPaymentMethod = false;
            
            if($paymentMethod){
                $filterPaymentMethod = true;
                $filter['payment_method'] = $paymentMethod;
            }
            $paymentStatus = $request->payment_status;
            $filterPaymentStatus = false;
            
            if($paymentStatus){
                $filterPaymentStatus = true;
                $filter['payment_status'] = $paymentStatus;
            }
            $status = $request->status;
            $filterStatus = false;
            
            if($status){
                $filterStatus = true;
                $filter['status'] = $status;
            }
            
            $orderQuery  = Order::where('order_status','<>','IC ');
            if($filterDate){
                $orderQuery  = $orderQuery->where('purchase_date','>=',$minDate.' 00:00:00')->where('purchase_date','<=',$maxDate.' 23:59:59');
            }
            if($filterSearch){
                $orderQuery  = $orderQuery->where(function ($query) use ($searchText) {
                $query->where('unique_order_id','like','%'.$searchText.'%')->orWhereHas(
                        'userDetails' , function ($q) use ($searchText) {
                            $q->where('full_name','like','%'.$searchText.'%');
                        });
                    });
            }

            if($filterPaymentMethod){
                $orderQuery  = $orderQuery->whereIn('payment_method',$paymentMethod);
            }

            if($filterPaymentStatus){
                $orderQuery  = $orderQuery->whereIn('payment_status',$paymentStatus);
            }

            if($filterStatus){
                $mainStatus = $isPrint = [];
                foreach ($status as $key => $val) {
                    $explodedValue = explode('-', $val);
                    $mainStatus[]   = $explodedValue[0];
                    $isPrint[]  = $explodedValue[1];
                }
               
                $orderQuery  = $orderQuery->whereIn('status',$mainStatus);
                $orderQuery  = $orderQuery->WhereIn('is_print',$isPrint);
            }

            $orderQuery = $orderQuery->orderBy('created_at','desc')
                                    ->paginate(AdminHelper::ADMIN_LIST_LIMIT);

            $data['orders'] = $orderQuery;
            $data['data'] = $filter;

            return view('admin.order.list', $data);
            
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_order_list');
        $data['panel_title']= trans('custom_admin.lab_order_list');

        try
        {
            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            $filter = array();
            
            $purchaseDate = $request->purchase_date;
            $filterDate = false;
            
            if ($purchaseDate) {
                $filterDate = true;
                $filter['purchaseDate'] = $purchaseDate;
                $date = explode(" - ",$purchaseDate);
                $minDate = date('Y-m-d', strtotime($date[0]));
                $maxDate = date('Y-m-d', strtotime($date[1]));
            }
            $searchText = $request->searchText;
            $filterSearch = false;
            
            if($searchText){
                $filterSearch = true;
                $filter['searchText'] = $searchText;
            }
            $paymentMethod = $request->payment_method;
            $filterPaymentMethod = false;
            
            if($paymentMethod){
                $filterPaymentMethod = true;
                $filter['payment_method'] = $paymentMethod;
            }
            $paymentStatus = $request->payment_status;
            $filterPaymentStatus = false;
            
            if($paymentStatus){
                $filterPaymentStatus = true;
                $filter['payment_status'] = $paymentStatus;
            }
            $status = $request->status;
            $filterStatus = false;
            
            if($status){
                $filterStatus = true;
                $filter['status'] = $status;
            }
            
            $orderQuery  = Order::where('order_status','<>','IC ');
            if($filterDate){
                $orderQuery  = $orderQuery->where('purchase_date','>=',$minDate.' 00:00:00')->where('purchase_date','<=',$maxDate.' 23:59:59');
            }
            if($filterSearch){
                $orderQuery  = $orderQuery->where(function ($query) use ($searchText) {
                $query->where('unique_order_id','like','%'.$searchText.'%')->orWhereHas(
                        'userDetails' , function ($q) use ($searchText) {
                            $q->where('full_name','like','%'.$searchText.'%');
                        });
                    });
            }

            if($filterPaymentMethod){
                $orderQuery  = $orderQuery->whereIn('payment_method',$paymentMethod);
            }

            if($filterPaymentStatus){
                $orderQuery  = $orderQuery->whereIn('payment_status',$paymentStatus);
            }

            if($filterStatus){
                $mainStatus = $isPrint = [];
                foreach ($status as $key => $val) {
                    $explodedValue = explode('-', $val);
                    $mainStatus[]   = $explodedValue[0];
                    $isPrint[]  = $explodedValue[1];
                }
                
                $orderQuery  = $orderQuery->whereIn('status',$mainStatus);
                $orderQuery  = $orderQuery->WhereIn('is_print',$isPrint);
            }

            $orderQuery = $orderQuery->orderBy('created_at','desc')->get();

            $data['orders'] = $orderQuery;
            $data['data'] = $filter;
            
            return view('admin.order.show_all', $data);
            
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : details
    # Params        : Request $request
    /*****************************************************/
    public function details(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_order_details');
        $data['panel_title']= trans('custom_admin.lab_order_details');

        try
        {
            $orderId = $request->id;
            if ($orderId != '') {
                $orderDetails = Order::where(['id' => $orderId])->first();

                $getOrderDetails = AdminHelper::getOrderDetails($orderId);

                $data['getOrderDetails']    = $getOrderDetails;
                $data['orderDetails']       = $orderDetails;

                return view('admin.order.details', $data);
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            } 
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.details')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : status
    # Params        : Request $request, $id
    /*****************************************************/
    public function status(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.'.\App::getLocale().'.order.list');
            }
            $details = Order::where('id', $id)->first();
            if ($details != null) {
                if ($details->status == 'P') {
                    $details->payment_status= 'C';
                    $details->status        = 'D';
                    $details->is_print      = '1';
                    $details->save();

                    $reviewLink = route('site.'.\App::getLocale().'.users.order-details', Helper::customEncryptionDecryption($id, 'encrypt'));

                    $userData = User::where('id', $details->user_id)->first();
                    if ($userData->userNotification->order_update == 1) {
                        $siteSetting = Helper::getSiteSettings();
                        // Email to customer
                        \Mail::send('email_templates.admin.review_link_to_customer',
                        [
                            'user'          => $userData,
                            'siteSetting'   => $siteSetting,
                            'app_config'    => [
                                'appname'       => $siteSetting->website_title,
                                'reviewLink'    => $reviewLink,
                                'currentLang'   => \App::getLocale(),
                            ],
                        ], function ($m) use ($userData, $siteSetting) {
                            $m->from($siteSetting->from_email, $siteSetting->website_title);
                            $m->to($userData->email, $userData->full_name)->subject(trans('custom.label_rate_your_order').' - '.$siteSetting->website_title);
                        });
                    }
                    
                    if ($userData->userNotification->sms == 1) {
                        // $sendSms = $this->sendOrderReviewMessage($userData->phone_no, $reviewLink);
                    }
                    
                    $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                    return redirect()->back();
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
                    return redirect()->back();
                }
            } else {
                return redirect()->route('admin.'.\App::getLocale().'.order.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : invoice
    # Params        : Request $request
    /*****************************************************/
    public function invoice(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_order_invoice');
        $data['panel_title']= trans('custom_admin.lab_order_invoice');

        try
        {
            $orderId = $request->id;
            if ($orderId != '') {
                $orderDetails = Order::where(['id' => $orderId])->first();

                $getOrderDetails = AdminHelper::getOrderDetails($orderId);

                $data['getOrderDetails']    = $getOrderDetails;
                $data['orderDetails']       = $orderDetails;
                $data['orderId']            = $orderId;
                $data['siteSettings']       = Helper::getSiteSettings();

                return view('admin.order.invoice', $data);
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            } 
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.invoice')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : invoice
    # Params        : Request $request
    /*****************************************************/
    public function invoicePrint(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_order_invoice');
        $data['panel_title']= trans('custom_admin.lab_order_invoice');

        try
        {
            $orderId = $request->id;
            if ($orderId != '') {
                $orderDetails = Order::where(['id' => $orderId])->first();
                $mainStatus  = $orderDetails->status;
                $printStatus = $orderDetails->is_print;
                $orderDetails->is_print = '1';
                $orderDetails->save();

                $getOrderDetails = AdminHelper::getOrderDetailsForInvoice($orderId);

                $data['getOrderDetails']    = $getOrderDetails;
                $data['orderDetails']       = $orderDetails;
                $data['orderId']            = $orderId;
                $data['siteSettings']       = Helper::getSiteSettings();
                
                $address    = $orderDetails['delivery_street'].','.$orderDetails['delivery_post_code'];
                //$apiKey     = 'AIzaSyAOAl0P8rnQSpLJlHq4Y12J9e9IGHpvIqk'; //'AIzaSyDCAj77zFhbZaigrjnNNVbeGzbJZ1v1K8w'; // Google maps now requires an API key.
                $apiKey     = 'AIzaSyDCAj77zFhbZaigrjnNNVbeGzbJZ1v1K8w';
                $geo        = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&key='.$apiKey);
                $geo        = json_decode($geo, true); // Convert the JSON to an array
                $data['latitude'] = $data['longitude'] = $latitude   = $longitude = '';
                if (isset($geo['status']) && ($geo['status'] == 'OK')) {
                    $data['latitude']   = $latitude     = $geo['results'][0]['geometry']['location']['lat'];    // Latitude
                    $data['longitude']  = $longitude    = $geo['results'][0]['geometry']['location']['lng'];   // Longitude
                }

                if ($mainStatus == 'P' && $printStatus == '0') {
                    $userData = User::where('id',$orderDetails->user_id)->first();

                    if ($userData->userNotification != null) {
                        // Mail to customer
                        if ($userData->userNotification->order_update == '1') {
                            $siteSetting    = Helper::getSiteSettings();
                            $orderData      = Helper::getOrderDetails($orderId, $orderDetails->user_id);

                            \Mail::send('email_templates.admin.order_delivery_notification_to_customer',
                            [
                                'details'       => $orderDetails,
                                'user'          => $userData,
                                'siteSetting'   => $siteSetting,
                                'orderDetails'  => $orderData,
                                'app_config'    => [
                                    'appname'       => $siteSetting->website_title,
                                    'currentLang'   => \App::getLocale(),
                                ],
                            ], function ($m) use ($orderDetails, $siteSetting) {
                                $m->from($siteSetting->from_email, $siteSetting->website_title);
                                $m->to($orderDetails->delivery_email, $orderDetails->delivery_full_name)->subject(trans('custom_admin.label_delivery_notification').' - '.$siteSetting->website_title);
                            });
                        }
                    }
                }
                return view('admin.order.invoice_print', $data);

            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            } 
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.invoice-print')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : liveOrders
    # Params        : Request $request
    /*****************************************************/
    public function liveOrders(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_live_order_list');
        $data['panel_title']= trans('custom_admin.lab_live_order_list');

        try
        {
            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            
            $data['siteSettings'] = SiteSetting::first();
            
            return view('admin.order.live_order', $data);
            
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : liveOrderList
    # Params        : Request $request
    /*****************************************************/
    public function liveOrderList(Request $request)
    {
        $orderQuery = Order::where('order_status','<>','IC')->where(['status' => 'P', 'is_print' => '0'])->orderBy('created_at','desc')->get();

        $data['orders'] = $orderQuery;

        $alertStatus = 0;
        if (count($data['orders']) > 0) {
            $alertStatus = 1;
        }
            
        $getList = view('admin.order.live_order_list', $data)->render();

        $orderProcessingQuery = Order::where('order_status','<>','IC')->where(['status' => 'P', 'is_print' => '1'])->orderBy('created_at','desc')->get();
        $data['processingOrders'] = $orderProcessingQuery;
        $getProcessingOrderList = view('admin.order.live_processing_order_list', $data)->render();
        
        return response()->json(array('html' => $getList, 'processingHtml' => $getProcessingOrderList, 'alertStatus' => $alertStatus, 'updated_at' => date('d/m/Y H:i:s')));
    }

    /*****************************************************/
    # Function name : processingStatus
    # Params        : Request $request, $id
    /*****************************************************/
    public function processingStatus(Request $request)
    {
        $title      = trans('custom_admin.message_error');
        $message    = trans('custom_admin.error_something_went_wrong');
        $type       = 'error';

        if ($request->isMethod('POST'))
        {
            $details = Order::where('id', $request->rowId)->first();
            if ($details != null) {
                if ($details->status == 'P') {
                    $details->status        = 'D';
                    $details->payment_status= 'C';
                    $details->is_print      = '1';
                    $details->save();

                    $reviewLink = route('site.'.\App::getLocale().'.users.order-details', Helper::customEncryptionDecryption($request->rowId, 'encrypt'));

                    $userData = User::where('id', $details->user_id)->first();
                    if ($userData->userNotification->order_update == 1) {
                        $siteSetting = Helper::getSiteSettings();
                        // Email to customer
                        \Mail::send('email_templates.admin.review_link_to_customer',
                        [
                            'user'          => $userData,
                            'siteSetting'   => $siteSetting,
                            'app_config'    => [
                                'appname'       => $siteSetting->website_title,
                                'reviewLink'    => $reviewLink,
                                'currentLang'   => \App::getLocale(),
                            ],
                        ], function ($m) use ($userData, $siteSetting) {
                            $m->to($userData->email, $userData->full_name)->subject(trans('custom_admin.label_subject_rate_order').' - '.$siteSetting->website_title);
                        });
                    }
                    
                    if ($userData->userNotification->sms == 1) {
                        // $sendSms = $this->sendOrderReviewMessage($userData->phone_no, $reviewLink);
                    }

                    $title      = trans('custom_admin.message_success');
                    $message    = trans('custom_admin.success_data_updated_successfully');
                    $type       = 'success';
                }
            }
        }

        return json_encode([
            'title'     => $title,
            'message'   => $message,
            'type'      => $type
        ]);
    }

    /*****************************************************/
    # Function name : cancelOrder
    # Params        : Request $request, $id
    /*****************************************************/
    public function cancelOrder(Request $request)
    {
        $title      = trans('custom_admin.message_error');
        $message    = trans('custom_admin.error_something_went_wrong');
        $type       = 'error';

        if ($request->isMethod('POST'))
        {
            $details = Order::where('id', $request->rowId)->first();
            if ($details != null) {
                if ($details->status == 'P') {
                    $details->status        = 'C';
                    $details->save();

                    $userData = User::where('id', $details->user_id)->first();
                    if ($userData->userNotification->order_update == 1) {
                        $siteSetting    = Helper::getSiteSettings();
                        $orderDetails   = Helper::getOrderDetails($details->id, $details->user_id);
                        $userData       = User::where('id',$details->user_id)->first();
                        // Email to customer
                        \Mail::send('email_templates.admin.order_cancel_notification',
                        [
                            'user'          => $userData,
                            'details'       => $details,
                            'siteSetting'   => $siteSetting,
                            'orderDetails'  => $orderDetails,
                            'app_config'    => [
                                'appname'       => $siteSetting->website_title,
                                'currentLang'   => \App::getLocale(),
                            ],
                        ], function ($m) use ($userData, $siteSetting) {
                            $m->from($siteSetting->from_email, $siteSetting->website_title);
                            $m->to($userData->email, $userData->full_name)->subject(trans('custom_admin.message_cancel_order').' - '.$siteSetting->website_title);
                        });
                    }

                    $title      = trans('custom_admin.message_success');
                    $message    = trans('custom_admin.success_data_updated_successfully');
                    $type       = 'success';
                }
            }
        }

        return json_encode([
            'title'     => $title,
            'message'   => $message,
            'type'      => $type
        ]);
    }

    /*****************************************************/
    # Function name : deliveryIn
    # Params        : Request $request, $id
    /*****************************************************/
    public function deliveryIn(Request $request)
    {
        $title      = trans('custom_admin.message_error');
        $message    = trans('custom_admin.error_something_went_wrong');
        $type       = 'error';

        if ($request->isMethod('POST'))
        {
            $orderId    = $request->order_id ? $request->order_id : 0;
            $deliveryIn = $request->delivery_in ? $request->delivery_in : 30;

            $details = Order::where('id', $orderId)->first();
            if ($details != null) {
                $details->delivery_in   = $deliveryIn;
                $details->is_print = '1';
                $details->save();
                                
                $siteSetting    = Helper::getSiteSettings();
                $orderDetails   = Helper::getOrderDetails($orderId, $details->user_id);
                $userData       = User::where('id',$details->user_id)->first();

                if ($userData->userNotification != null) {
                    // Mail to customer
                    if ($userData->userNotification->order_update == '1') {
                        // Email to customer
                        \Mail::send('email_templates.admin.order_delivery_notification',
                        [
                            'details'       => $details,
                            'user'          => $userData,
                            'deliveryIn'    => $deliveryIn,
                            'siteSetting'   => $siteSetting,
                            'orderDetails'  => $orderDetails,
                            'app_config'    => [
                                'appname'       => $siteSetting->website_title,
                                'currentLang'   => \App::getLocale(),
                            ],
                        ], function ($m) use ($details, $siteSetting) {
                            $m->from($siteSetting->from_email, $siteSetting->website_title);
                            $m->to($details->delivery_email, $details->delivery_full_name)->subject(trans('custom_admin.label_delivery_notification').' - '.$siteSetting->website_title);
                        });
                    }
                }
                
                $title      = trans('custom_admin.message_success');
                $message    = trans('custom_admin.message_delivery_notification_email');
                $type       = 'success';
            }
        }

        return json_encode([
            'title'     => $title,
            'message'   => $message,
            'type'      => $type
        ]);
    }

    /*****************************************************/
    # Function name : exportToExcel
    # Params        : Request $request
    /*****************************************************/
    public function exportToExcel(Request $request)
    {

        try
        {
            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            $filter = array();
            $orderTotalPrice = $cashPayment = $onlinePayment = $cardPayment = $cancelledPayment = $allTotalPayment = 0;
            
            $purchaseDate = $request->purchase_date;
            $filterDate = false;
            
            if ($purchaseDate) {
                $filterDate = true;
                $filter['purchaseDate'] = $purchaseDate;
                $date = explode(" - ",$purchaseDate);
                $minDate = date('Y-m-d', strtotime($date[0]));
                $maxDate = date('Y-m-d', strtotime($date[1]));
            }
            $searchText = $request->searchText;
            $filterSearch = false;
            
            if($searchText){
                $filterSearch = true;
                $filter['searchText'] = $searchText;
            }
            $paymentMethod = $request->payment_method;
            $filterPaymentMethod = false;
            
            if($paymentMethod){
                $filterPaymentMethod = true;
                $filter['payment_method'] = $paymentMethod;
            }
            $paymentStatus = $request->payment_status;
            $filterPaymentStatus = false;
            
            if($paymentStatus){
                $filterPaymentStatus = true;
                $filter['payment_status'] = $paymentStatus;
            }
            $status = $request->status;
            $filterStatus = false;
            
            if($status){
                $filterStatus = true;
                $filter['status'] = $status;
            }
            
            $orderQuery  = Order::where('order_status','<>','IC ');
            if($filterDate){
                $orderQuery  = $orderQuery->where('purchase_date','>=',$minDate.' 00:00:00')->where('purchase_date','<=',$maxDate.' 23:59:59');
            }
            if($filterSearch){
                $orderQuery  = $orderQuery->where(function ($query) use ($searchText) {
                $query->where('unique_order_id','like','%'.$searchText.'%')->orWhereHas(
                        'userDetails' , function ($q) use ($searchText) {
                            $q->where('full_name','like','%'.$searchText.'%');
                        });
                    });
            }

            if($filterPaymentMethod){
                $orderQuery  = $orderQuery->whereIn('payment_method',$paymentMethod);
            }

            if($filterPaymentStatus){
                $orderQuery  = $orderQuery->whereIn('payment_status',$paymentStatus);
            }

            if($filterStatus){
                $mainStatus = $isPrint = [];
                foreach ($status as $key => $val) {
                    $explodedValue = explode('-', $val);
                    $mainStatus[]   = $explodedValue[0];
                    $isPrint[]  = $explodedValue[1];
                }
                
                $orderQuery  = $orderQuery->whereIn('status',$mainStatus);
                $orderQuery  = $orderQuery->WhereIn('is_print',$isPrint);
            }

            $orderQuery = $orderQuery->orderBy('created_at','desc')->get();

            $dataToPrint = $dataToPrintCounts = $finalData = [];
            if ($orderQuery->count()) {
                foreach ($orderQuery as $key => $value) {
                    $paymentMethod = $paymentStatus = $deliveryStatus = 'NA';
                    // Payment method
                    if ($value->payment_method == '0'){
                        $paymentMethod = trans('custom_admin.lab_payment_pending');
                    } elseif($value->payment_method == '1') {
                        $paymentMethod = trans('custom_admin.lab_payment_cod');
                    } elseif($value->payment_method == '2') {
                        $paymentMethod = trans('custom_admin.lab_payment_stripe');
                    }  elseif($value->payment_method == '3') {
                        $paymentMethod = trans('custom_admin.label_card_on_door');
                    }
                    // Payment status
                    if ($value->payment_status == 'P') {
                        $paymentStatus = trans('custom_admin.lab_order_payment_pending');
                    } elseif($value->payment_status == 'C') {
                        $paymentStatus = trans('custom_admin.lab_order_payment_completed');
                    }
                    // Delivery date & time
                    if ($value->delivery_is_as_soon_as_possible == 'N') {
                        $deliveryDateTime = date('d.m.Y', strtotime($value->delivery_date))." ".date('H:i', strtotime($value->delivery_time));
                    } else {
                        $deliveryDateTime = trans('custom_admin.label_as_soon_as_possible');
                    }
                    // Delivery type
                    if ($value->delivery_type == 'Delivery') {
                        $deliveryType = trans('custom_admin.new_lab_order_delivery_time');
                    } else {
                        $deliveryType = trans('custom_admin.new_lab_order_click_collect');
                    }
                    // Order total
                    $orderTotalPrice = $value->orderDetails->sum('total_price');
                    if ($value->delivery_type == 'Delivery' && $value->delivery_charge > 0) {
                        $orderTotalPrice += $value->delivery_charge;
                    }
                    if ($value->coupon_code != null) {
                        $orderTotalPrice = $orderTotalPrice - $value->discount_amount;
                    }
                    if ($value->payment_method == '2') {
                        $orderTotalPrice += $value->card_payment_amount;
                    }
                    // Delivery status
                    if ($value->status == 'P' && $value->is_print == '1') {
                        $deliveryStatus = trans('custom_admin.lab_order_delivery_status_processing');
                    } elseif ($value->status == 'P' && $value->is_print == '0') {
                        $deliveryStatus = trans('custom_admin.lab_order_delivery_status_new');
                    } elseif ($value->status == 'D') {
                        $deliveryStatus = trans('custom_admin.lab_order_status_delivered');
                    } elseif ($value->status == 'C') {
                        $deliveryStatus = trans('custom_admin.label_cancelled');
                    }

                    $orderTotalPrice = Helper::priceRoundOff($orderTotalPrice);

                    // All payment
                    $allTotalPayment += $orderTotalPrice;

                    if ($value->payment_method == '1' && $value->status != 'C') { // Cash Payment
                        $cashPayment += $orderTotalPrice;
                    } else if ($value->payment_method == '2' && $value->status != 'C') { // Online Payment
                        $onlinePayment += $orderTotalPrice;
                    } else if ($value->payment_method == '3' && $value->status != 'C') { // Online Payment
                        $cardPayment += $orderTotalPrice;
                    } else if ($value->status == 'C') { // Cancelled Payment
                        $cancelledPayment += $orderTotalPrice;
                    }

                    $dataToPrint[] = [
                        'unique_order_id'       => $value->unique_order_id,
                        'customer_name'         => optional($value->userDetails)->full_name,
                        'payment_method'        => $paymentMethod,
                        'payment_status'        => $paymentStatus,
                        'delivery_date_time'    => $deliveryDateTime,
                        'delivery_type'         => $deliveryType,
                        'order_total'           => 'CHF '.AdminHelper::formatToTwoDecimalPlaces($orderTotalPrice),
                        'order_delivery_status' => $deliveryStatus,
                    ];
                }
            }

            if ($orderQuery->count()) {
                $dataToPrintCounts[] = [
                    'unique_order_id'       => '',
                    'customer_name'         => '',
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $dataToPrintCounts[] = [
                    'unique_order_id'       => trans('custom_admin.dashboard_total_orders_print'),
                    'customer_name'         => $orderQuery->count(),
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $dataToPrintCounts[] = [
                    'unique_order_id'       => trans('custom_admin.lab_order_total_print'),
                    'customer_name'         => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($allTotalPayment)).' CHF',
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $dataToPrintCounts[] = [
                    'unique_order_id'       => trans('custom_admin.label_need_pay_cash_print'),
                    'customer_name'         => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cashPayment)).' CHF',
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $dataToPrintCounts[] = [
                    'unique_order_id'       => trans('custom_admin.label_pay_online_print'),
                    'customer_name'         => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($onlinePayment)).' CHF',
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $dataToPrintCounts[] = [
                    'unique_order_id'       => trans('custom_admin.label_card_on_door_print'),
                    'customer_name'         => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cardPayment)).' CHF',
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $dataToPrintCounts[] = [
                    'unique_order_id'       => trans('custom_admin.label_cancelled_print'),
                    'customer_name'         => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cancelledPayment)).' CHF',
                    'payment_method'        => '',
                    'payment_status'        => '',
                    'delivery_date_time'    => '',
                    'delivery_type'         => '',
                    'order_total'           => '',
                    'order_delivery_status' => '',
                ];
                $finalData = array_merge($dataToPrint, $dataToPrintCounts);

                if ($purchaseDate != '') {
                    $dateRangeName = str_replace(" - ","_",$purchaseDate);
                } else {
                    $dateRangeName = date('d.m.Y');
                }
                
                return Excel::download(new OrdersExport($finalData), 'order_export_'.$dateRangeName.'.xlsx');
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.lab_no_records_found'));
                return redirect()->route('admin.'.\App::getLocale().'.order.list');
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : exportToPdf
    # Params        : Request $request
    /*****************************************************/
    public function exportToPdf(Request $request)
    {
        try
        {
            
            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            $filter = array();

            $orderTotalPrice = $cashPayment = $onlinePayment = $cardPayment = $cancelledPayment = $allTotalPayment = 0;
            
            $purchaseDate = $request->purchase_date;
            $filterDate = false;
            $dateRange = '';
            
            if ($purchaseDate) {
                $filterDate = true;
                $filter['purchaseDate'] = $purchaseDate;
                $date = explode(" - ",$purchaseDate);
                $minDate = date('Y-m-d', strtotime($date[0]));
                $maxDate = date('Y-m-d', strtotime($date[1]));

                $dateRange = date('d.m.Y', strtotime($minDate)).' '.trans('custom_admin.label_to').' '.date('d.m.Y', strtotime($maxDate));
            }
            $searchText = $request->searchText;
            $filterSearch = false;
            
            if($searchText){
                $filterSearch = true;
                $filter['searchText'] = $searchText;
            }
            $paymentMethod = $request->payment_method;
            $filterPaymentMethod = false;
            
            if($paymentMethod){
                $filterPaymentMethod = true;
                $filter['payment_method'] = $paymentMethod;
            }
            $paymentStatus = $request->payment_status;
            $filterPaymentStatus = false;
            
            if($paymentStatus){
                $filterPaymentStatus = true;
                $filter['payment_status'] = $paymentStatus;
            }
            $status = $request->status;
            $user = \Auth::guard('admin')->user();
            // if($user->role_id!=1){
            //     $status=['D-1'];
            // }
            $filterStatus = false;
            
            if($status){
                $filterStatus = true;
                $filter['status'] = $status;
            }

            
            
            $orderQuery  = Order::where('order_status','<>','IC ');
            if($filterDate){
                $orderQuery  = $orderQuery->where('purchase_date','>=',$minDate.' 00:00:00')->where('purchase_date','<=',$maxDate.' 23:59:59');
            }
            if($filterSearch){
                $orderQuery  = $orderQuery->where(function ($query) use ($searchText) {
                $query->where('unique_order_id','like','%'.$searchText.'%')->orWhereHas(
                        'userDetails' , function ($q) use ($searchText) {
                            $q->where('full_name','like','%'.$searchText.'%');
                        });
                    });
            }

            if($filterPaymentMethod){
                $orderQuery  = $orderQuery->whereIn('payment_method',$paymentMethod);
            }

            if($filterPaymentStatus){
                $orderQuery  = $orderQuery->whereIn('payment_status',$paymentStatus);
            }

            if($filterStatus){
                $mainStatus = $isPrint = [];
                foreach ($status as $key => $val) {
                    $explodedValue = explode('-', $val);
                    $mainStatus[]   = $explodedValue[0];
                    $isPrint[]  = $explodedValue[1];
                }
                
                $orderQuery  = $orderQuery->whereIn('status',$mainStatus);
                $orderQuery  = $orderQuery->WhereIn('is_print',$isPrint);
            }

            $orderQuery = $orderQuery->orderBy('created_at','desc')->get();

          

            $dataToPrint = [];
            if ($orderQuery->count()) {
                foreach ($orderQuery as $key => $value) {
                    $paymentMethod = $paymentStatus = $deliveryStatus = 'NA';
                    // Payment method
                    if ($value->payment_method == '0'){
                        $paymentMethod = trans('custom_admin.lab_payment_pending');
                    } elseif($value->payment_method == '1') {
                        $paymentMethod = trans('custom_admin.lab_payment_cod');
                    } elseif($value->payment_method == '2') {
                        $paymentMethod = trans('custom_admin.lab_payment_stripe');
                    }  elseif($value->payment_method == '3') {
                        $paymentMethod = trans('custom_admin.label_card_on_door');
                    }
                    // Payment status
                    if ($value->payment_status == 'P') {
                        $paymentStatus = trans('custom_admin.lab_order_payment_pending');
                    } elseif($value->payment_status == 'C') {
                        $paymentStatus = trans('custom_admin.lab_order_payment_completed');
                    }
                    // Delivery date & time
                    if ($value->delivery_is_as_soon_as_possible == 'N') {
                        $deliveryDateTime = date('d.m.Y', strtotime($value->delivery_date))." ".date('H:i', strtotime($value->delivery_time));
                    } else {
                        $deliveryDateTime = trans('custom_admin.label_as_soon_as_possible');
                    }
                    // Delivery type
                    if ($value->delivery_type == 'Delivery') {
                        $deliveryType = trans('custom_admin.new_lab_order_delivery_time');
                    } else {
                        $deliveryType = trans('custom_admin.new_lab_order_click_collect');
                    }
                    // Order total
                    $orderTotalPrice = $value->orderDetails->sum('total_price');
                    if ($value->delivery_type == 'Delivery' && $value->delivery_charge > 0) {
                        $orderTotalPrice += $value->delivery_charge;
                    }
                    if ($value->coupon_code != null) {
                        $orderTotalPrice = $orderTotalPrice - $value->discount_amount;
                    }
                    if ($value->payment_method == '2') {
                        $orderTotalPrice += $value->card_payment_amount;
                    }
                    // Delivery status
                    if ($value->status == 'P' && $value->is_print == '1') {
                        $deliveryStatus = trans('custom_admin.lab_order_delivery_status_processing');
                    } elseif ($value->status == 'P' && $value->is_print == '0') {
                        $deliveryStatus = trans('custom_admin.lab_order_delivery_status_new');
                    } elseif ($value->status == 'D') {
                        $deliveryStatus = trans('custom_admin.lab_order_status_delivered');
                    } elseif ($value->status == 'C') {
                        $deliveryStatus = trans('custom_admin.label_cancelled');
                    }

                    // All payment
                    $allTotalPayment += $orderTotalPrice;

                    if ($value->payment_method == '1' && $value->status != 'C') { // Cash Payment
                        if ($value->orderDetails) {
                            foreach ($value->orderDetails as $keyOD => $valueOD) {
                                $cashPayment += $valueOD->total_price;
                            }
                        }
                        if ($value->delivery_type == 'Delivery' && $value->delivery_charge > 0) {
                            $cashPayment += $value->delivery_charge;
                        }
                        if ($value->coupon_code != null) {
                            $cashPayment = $cashPayment - $value->discount_amount;
                        }
                    } else if ($value->payment_method == '2' && $value->status != 'C') { // Online Payment
                        if ($value->orderDetails) {
                            foreach ($value->orderDetails as $keyOD => $valueOD) {
                                $onlinePayment += $valueOD->total_price;
                            }
                        }
                        if ($value->delivery_type == 'Delivery' && $value->delivery_charge > 0) {
                            $onlinePayment += $value->delivery_charge;
                        }
                        if ($value->coupon_code != null) {
                            $onlinePayment = $onlinePayment - $value->discount_amount;
                        }
                    } else if ($value->payment_method == '3' && $value->status != 'C') { // Online Payment
                        if ($value->orderDetails) {
                            foreach ($value->orderDetails as $keyOD => $valueOD) {
                                $cardPayment += $valueOD->total_price;
                            }
                        }
                        if ($value->delivery_type == 'Delivery' && $value->delivery_charge > 0) {
                            $cardPayment += $value->delivery_charge;
                        }
                        if ($value->coupon_code != null) {
                            $cardPayment = $cardPayment - $value->discount_amount;
                        }
                    } else if ($value->status == 'C') { // Cancelled Payment
                        if ($value->orderDetails) {
                            foreach ($value->orderDetails as $keyOD => $valueOD) {
                                $cancelledPayment += $valueOD->total_price;
                            }
                        }
                        if ($value->delivery_type == 'Delivery' && $value->delivery_charge > 0) {
                            $cancelledPayment += $value->delivery_charge;
                        }
                        if ($value->coupon_code != null) {
                            $cancelledPayment = $cancelledPayment - $value->discount_amount;
                        }
                    }

                    if ($value->status == 'C') {
                        $paymentMethod = trans('custom_admin.label_cancelled_print');
                    }

                    $dataToPrint[] = [
                        'unique_order_id'       => $value->unique_order_id,
                        'order_on'              => date('d.m.Y H:i', strtotime($value['purchase_date'])),
                        'post_code'             => $value['delivery_post_code'],
                        'customer_name'         => optional($value->userDetails)->full_name,
                        'payment_method'        => $paymentMethod,
                        'payment_status'        => $paymentStatus,
                        'delivery_date_time'    => $deliveryDateTime,
                        'delivery_type'         => $deliveryType,
                        'order_total'           => 'CHF '.AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderTotalPrice)),
                        'order_delivery_status' => $deliveryStatus,
                    ];
                }
            }

            if ($orderQuery->count()) {
                $otherPayments = [
                    'total_orders_count'    => $orderQuery->count(),
                    'order_total_amount'    => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($allTotalPayment)).' CHF',
                    'cash_payment_amount'   => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cashPayment)).' CHF',
                    'online_payment_amount' => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($onlinePayment)).' CHF',
                    'card_payment_amount'   => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cardPayment)).' CHF',
                    'cancelled_payment_amount' => AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cancelledPayment)).' CHF',
                    'date_range'            => $dateRange,
                ];
                $siteSetting = Helper::getSiteSettings();

                if ($purchaseDate != '') {
                    $dateRangeName = str_replace(" - ","_",$purchaseDate);
                } else {
                    $dateRangeName = date('d.m.Y');
                }
                
                $pdf = PDF::loadView('admin.order.export_to_pdf', ['dataToPrint' => $dataToPrint, 'otherPayments' => $otherPayments, 'siteSettings' => $siteSetting]);
                return $pdf->download('order_export_'.$dateRangeName.'.pdf');
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.lab_no_records_found'));
                return redirect()->route('admin.'.\App::getLocale().'.order.list');
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.order.list')->with('error', $e->getMessage());
        }
    }

}
