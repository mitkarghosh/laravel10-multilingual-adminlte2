<?php
/*****************************************************/
# Page/Class name   : OrdersController
# Purpose           : all order related functions
/*****************************************************/
namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use Illuminate\Support\Facades\Session;
use Auth;
use Hash;
use \Validator;
use Helper;
use \Response;
Use Redirect;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailLocal;
use App\Models\OrderReview;
use App;

class OrdersController extends Controller
{
    /*****************************************************/
    # Function name : ordersReviews
    # Params        : Request $request
    /*****************************************************/
    public function ordersReviews(Request $request)
    {
        $currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData();
        $orderList = $order = [];       

        try
        {
            $order = Order::where('type', 'O')
                            ->where('user_id', Auth::user()->id)
                            ->with([
                                'orderDetails' => function ($q) use ($lang) {
                                    $q->with([
                                            'orderDetailLocals' => function ($q) use ($lang) {
                                                $q->where('lang_code', '=', $lang);
                                            },
                                        ]);        
                                },
                            ])
                            ->orderBy('created_at', 'desc')
                            ->paginate(Helper::MY_ORDER_LISTING);
            
            foreach ($order as $key => $val) {
                $orderVal['order_id']           = Helper::customEncryptionDecryption($val->id, 'encrypt');
                $orderVal['order_unique_id']    = $val->unique_order_id;
                $orderVal['order_date']         = $val->purchase_date;
                $orderVal['order_updated_at']   = $val->updated_at;
                $orderVal['purchase_date']      = $val->purchase_date;
                $orderVal['delivery_type']      = $val->delivery_type;
                $orderVal['delivery_charge']    = $val->delivery_charge;
                $orderVal['payment_method']     = $val->payment_method;
                $orderVal['card_payment_amount']= $val->card_payment_amount;
                $orderVal['coupon_code']        = $val->coupon_code;
                $orderVal['discount_amount']    = $val->discount_amount;
                $orderVal['delivery_date']      = $val->delivery_date;
                $orderVal['delivery_time']      = $val->delivery_time;
                $orderVal['status']             = $val->status;

                $totalAmount = 0;
                if ($val->orderDetails->count() > 0) {
                    foreach ($val->orderDetails as $keyOrderDetails => $valOrderDetails) {
                        $orderVal['product_details'][$keyOrderDetails]['product_title'] = $valOrderDetails->orderDetailLocals[0]->local_title;
                        $orderVal['product_details'][$keyOrderDetails]['quantity']      = $valOrderDetails->quantity;

                        $totalAmount += $valOrderDetails->total_price;
                    }
                } 
                else {
                    $orderVal['product_details'] = [];
                }
                
                $orderVal['total_price'] = Helper::formatToTwoDecimalPlaces($totalAmount);
                $orderList[] = $orderVal;
                unset($orderVal);
            }
            // echo '<pre>'; print_r($orderList); die;
                       
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('site.user.orders_reviews',[
            'title'         => $metaData['title'],
            'keyword'       => $metaData['keyword'],
            'description'   => $metaData['description'],
            'orderList'     => $orderList,
            'myOrderList'   => $order,
        ]);
    }
    
    /*****************************************************/
    # Function name : orderDetails
    # Params        : Request $request
    /*****************************************************/
    public function orderDetails(Request $request, $orderId)
    {
        $currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData();        

        try
        {
            $cartUserId         = $totalAmount = 0;
            $orderDetails       = [];
            $OrderReviewDetails = '';

            $cartUserId     = Auth::user()->id;
            $id             = $orderId;
            $orderId        = Helper::customEncryptionDecryption($orderId, 'decrypt');

            $cartConditions = ['user_id' => $cartUserId, 'type' => 'O', 'id' => $orderId];

            $getOrderDetails = Order::where($cartConditions)->with([
                                                                'orderDetails' => function ($query) use ($lang) {
                                                                    // $query->orderBy('created_at', 'desc');
                                                                    $query->with([
                                                                        'orderDetailLocals' => function ($query) use ($lang) {
                                                                            $query->where('lang_code', '=', $lang);
                                                                        },
                                                                        'orderAttributeLocalDetails' => function ($query) use ($lang) {
                                                                            $query->where('lang_code', '=', $lang);
                                                                        },
                                                                        'orderIngredients' => function ($query) use ($lang) {
                                                                            $query->with([
                                                                                'orderIngredientLocals' => function ($query) use ($lang) {
                                                                                    $query->where('lang_code', '=', $lang);
                                                                                }
                                                                            ]);
                                                                        },
                                                                    ]);
                                                                },
                                                            ])
                                                            ->first();
            
            $orderVal       = Helper::getOrderDetails($orderId, $cartUserId);

            $orderDetails = $orderVal;
            // echo '<pre>'; print_r($orderDetails); die;
            $orderReviewDetails = OrderReview::where(['order_id' => $orderId, 'user_id' => $cartUserId])->first();
                       
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('site.user.order_details',[
            'title'                 => $metaData['title'],
            'keyword'               => $metaData['keyword'],
            'description'           => $metaData['description'],
            'id'                    => $id,
            'getOrderDetails'       => $getOrderDetails,
            'orderDetails'          => $orderDetails,
            'orderReviewDetails'    => $orderReviewDetails,
        ]);
    }
    
    /*****************************************************/
    # Function name : orderReviewSubmit
    # Params        : Request $request
    /*****************************************************/
    public function orderReviewSubmit(Request $request)
    {
        try
        {
            if ($request->isMethod('POST')) {
                $orderId = isset($request->order_id) ? Helper::customEncryptionDecryption($request->order_id, 'decrypt') : 0;

                $orderReviewDetails = OrderReview::where(['order_id' => $orderId, 'user_id' => Auth::user()->id])->count();
                if ($orderReviewDetails == 0) {
                    $newOrderReview = new OrderReview;
                    $newOrderReview->order_id               = $orderId;
                    $newOrderReview->user_id                = Auth::user()->id;
                    $newOrderReview->food_quality           = isset($request->food_quality) ? $request->food_quality : 0;
                    $newOrderReview->delivery_time          = isset($request->delivery_time) ? $request->delivery_time : 0;
                    $newOrderReview->driver_friendliness    = isset($request->driver_friendliness) ? $request->driver_friendliness : 0;
                    $newOrderReview->avg_rating             = floor(($newOrderReview->food_quality + $newOrderReview->delivery_time + $newOrderReview->driver_friendliness) / 3);
                    $newOrderReview->short_review           = isset($request->short_review) ? $request->short_review : null;
                    $save = $newOrderReview->save();
                    if ($save) {
                        $request->session()->flash('alert-success', trans('custom.success_order_review'));
                    } else {
                        $request->session()->flash('alert-danger', trans('custom.please_try_again'));
                    }                    
                } else {
                    $request->session()->flash('alert-danger', trans('custom.error_order_review_already_submitted'));
                }
                return redirect()->back();
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : invoicePrint
    # Params        : Request $request
    /*****************************************************/
    public function invoicePrint(Request $request, $orderId)
    {
        $currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData();        

        try
        {
            $cartUserId         = $totalAmount = 0;
            $orderDetails       = [];
            $OrderReviewDetails = '';

            $cartUserId     = Auth::user()->id;
            $id             = $orderId;
            $orderId        = Helper::customEncryptionDecryption($orderId, 'decrypt');

            $cartConditions = ['user_id' => $cartUserId, 'type' => 'O', 'id' => $orderId];

            $getOrderDetails = Order::where($cartConditions)->with([
                                                                'orderDetails' => function ($query) use ($lang) {
                                                                    // $query->orderBy('created_at', 'desc');
                                                                    $query->with([
                                                                        'orderDetailLocals' => function ($query) use ($lang) {
                                                                            $query->where('lang_code', '=', $lang);
                                                                        },
                                                                        'orderAttributeLocalDetails' => function ($query) use ($lang) {
                                                                            $query->where('lang_code', '=', $lang);
                                                                        },
                                                                        'orderIngredients' => function ($query) use ($lang) {
                                                                            $query->with([
                                                                                'orderIngredientLocals' => function ($query) use ($lang) {
                                                                                    $query->where('lang_code', '=', $lang);
                                                                                }
                                                                            ]);
                                                                        },
                                                                    ]);
                                                                },
                                                            ])
                                                            ->first();
            
            $orderVal       = Helper::getOrderDetails($orderId, $cartUserId);

            $orderDetails = $orderVal;
            // echo '<pre>'; print_r($orderDetails); die;
            $orderReviewDetails = OrderReview::where(['order_id' => $orderId, 'user_id' => $cartUserId])->first();
                       
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('site.user.invoice_print',[
            'title'                 => $metaData['title'],
            'keyword'               => $metaData['keyword'],
            'description'           => $metaData['description'],
            'id'                    => $id,
            'getOrderDetails'       => $getOrderDetails,
            'orderDetails'          => $orderDetails,
            'orderReviewDetails'    => $orderReviewDetails,
        ]);
    }

}
