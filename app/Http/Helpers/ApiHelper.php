<?php
/*****************************************************/
# ApiHelper
# Page/Class name : ApiHelper
# Author :
# Created Date : 20-05-2019
# Functionality : generateResponseBody, getUserFromHeader
# Purpose : all general function for api
/*****************************************************/
namespace App\Http\Helpers;
use Request;
use \App\Models\Category;
use \App\Models\Cms;
use \App\Models\Notification;
use \App\Models\Order;
use \App\Models\OrderDetail;
use \App\Models\OrderAppliedCoupon;
use \App\Models\ProductVendor;
use \App\Models\SiteSetting;
use \App\Models\Contract;
use \App\Models\PushNotification;
use \Helper;

class ApiHelper
{
    /*****************************************************/
    # ApiHelper
    # Function name : generateResponseBody
    # Author        :
    # Created Date  : 20-05-2019
    # Purpose       : To generate api response body
    # Params        : $code, $data, $success = true, $errorCode = null
    /*****************************************************/
    public static function generateResponseBody($code, $data, $success = true, $errorCode = null)
    {
        $result         = [];
        $collectedData  = [];
        $finalCode      = $code;

        $functionName   = null;
        
        if (strpos($code, '#') !== false) {
            $explodedCode   = explode('#',$code);
            $finalCode      = $explodedCode[0];
            $functionName   = $explodedCode[1];
        }

        $collectedData['code'] = $finalCode;
        if ($success) {
            $collectedData['status'] = '1';     //for success
        } else {
            $collectedData['status'] = '0';     //for error
            if ($errorCode) {
                $collectedData['error_code'] = $errorCode;
            }
        }
        
        if (gettype($data) === 'string') {
            $collectedData['message'] = $data;
        } else if(gettype($data) === 'array' && array_key_exists('errors',$data)){
            $collectedData['message'] = implode(",",$data['errors']);
        }else {
            $collectedData['message'] = "";
            $collectedData['details'] = $data;
        }

        if ($functionName != null) {
            $result[$functionName] = $collectedData;
        } else {
            $result = $collectedData;
        }       

        return $result;
    }

    /*****************************************************/
    # ApiHelper
    # Function name : generateResponseBodyForLoginRegister
    # Author        :
    # Created Date  : 19-09-2019
    # Purpose       : To generate api response body
    # Params        : $code, $data, $success = true, $errorCode = null
    /*****************************************************/
    public static function generateResponseBodyForLoginRegister($code, $data, $success = true, $errorCode = null,$encryptedUserData="")
    {
        $result         = [];
        $collectedData  = [];
        $finalCode      = $code;

        $functionName   = null;
        
        if (strpos($code, '#') !== false) {
            $explodedCode   = explode('#',$code);
            $finalCode      = $explodedCode[0];
            $functionName   = $explodedCode[1];
        }

        $collectedData['code'] = $finalCode;
        if ($success === true) {
            $collectedData['status'] = '1';     //for success
        }else if($success === 2){
            $collectedData['status'] = '2';
        } else {
            $collectedData['status'] = '0';     //for error
            if ($errorCode) {
                $collectedData['error_code'] = $errorCode;
            }
        }
        
        if (gettype($data) === 'string') {
            $collectedData['message'] = $data;
        } else if(gettype($data) === 'array' && array_key_exists('errors',$data)){
            $collectedData['message'] = implode(",",$data['errors']);
        }else {
            $collectedData['message'] = "";
            $collectedData['details'] = $data;
        }
        $collectedData['encryptedUserData'] = $encryptedUserData;
        if ($functionName != null) {
            $result[$functionName] = $collectedData;
        } else {
            $result = $collectedData;
        }       

        return $result;
    }
    
    /*****************************************************/
    # ApiHelper
    # Function name : getUserFromHeader
    # Author :
    # Created Date : 20-05-2019
    # Purpose :  to get header user
    # Params : $request
    /*****************************************************/
    public static function getUserFromHeader($request)
    {   
        $headers = $request->header();
        $token = $headers['x-access-token'][0];
        $userData = \App\User::where('auth_token', $token)->first();
        return $userData;
    }

    /*****************************************************/
    # ApiHelper
    # Function name : checkUserRole
    # Author        :
    # Created Date  : 27-06-2019
    # Purpose       : checking user is wholesaler or contractor
    # Params        : Request $request
    /*****************************************************/
    public static function checkUserRole($userData, $type)
    {   
        if($userData && $type != null ) {
            $data = [];

            if($type == 'wholesaler') {
                if($userData->is_wholesaler == null) {
                   if($userData->userWholesaler != null) {
                        if ($userData->userWholesaler->status == 0) {
                            return 'approval-pending';
                        } elseif ($userData->userWholesaler->status == 1) {
                            return 'already-wholesaler';
                        } else {
                            return 'rejected';
                        }
                    } else {
                        $data['full_name']      = $userData->full_name;
                        $data['mobile_number']  = $userData->phone_no;
                        return $data;
                    }                
                } else {
                    return 'already-wholesaler';
                }
            } else if($type == 'contractor') {
                if($userData->is_contractor == null) {
                    if($userData->userContractor != null) {
                        if ($userData->userContractor->status == 0) {
                            return 'approval-pending';
                        } elseif($userData->userContractor->status == 1) {
                            return 'already-contractor';
                        } else {
                            return 'rejected';
                        }
                    } else {
                        $data['full_name']      = $userData->full_name;
                        $data['mobile_number']  = $userData->phone_no;
                        return $data;
                    }                
                } else {
                    return 'already-contractor';
                }
            }
        } else {
            return 'error';
        }
    }

    /*****************************************************/
    # ApiHelper
    # Function name : checkUserRoleNew
    # Author        :
    # Created Date  : 11-10-2019
    # Purpose       : checking user is wholesaler or contractor
    # Params        : Request $request
    /*****************************************************/
    public static function checkUserRoleNew($userData, $type)
    {   
        if($userData && $type != null ) {
            $data = [];

            if($type == 'wholesaler') {
                if($userData->is_wholesaler == null) {
                   if($userData->userWholesalerMany != null) {
                        if ($userData->userWholesalerMany->status == 0) {
                            return 'approval-pending';
                        } elseif ($userData->userWholesalerMany->status == 1) {
                            return 'already-wholesaler';
                        } else {
                            return 'rejected';
                        }
                    } else {
                        $data['full_name']      = $userData->full_name;
                        $data['mobile_number']  = $userData->phone_no;
                        return $data;
                    }                
                } else {
                    return 'already-wholesaler';
                }
            } else if($type == 'contractor') {
                if($userData->is_contractor == null) {
                    if($userData->userContractorMany != null) {
                        if ($userData->userContractorMany->status == 0) {
                            return 'approval-pending';
                        } elseif($userData->userContractorMany->status == 1) {
                            return 'already-contractor';
                        } else {
                            return 'rejected';
                        }
                    } else {
                        $data['full_name']      = $userData->full_name;
                        $data['mobile_number']  = $userData->phone_no;
                        return $data;
                    }                
                } else {
                    return 'already-contractor';
                }
            }
        } else {
            return 'error';
        }
    }

    /*****************************************************/
    # ApiHelper
    # Function name : getSetLocale
    # Author        :
    # Created Date  : 02-07-2019
    # Purpose       : get and set locale for api call
    # Params        : Request $request
    /*****************************************************/
    public static function getSetLocale($request)
    {
        $headerLang = $request->header('x-lang');
        if (!$headerLang) {
            $headerLang =  \Lang::locale();
        }
        \App::setLocale(strtolower($headerLang));
        return $headerLang;
    }

    /*****************************************************/
    # ApiHelper
    # Function name : getFunctionNameFromUrl
    # Author        :
    # Created Date  : 04-07-2019
    # Purpose       : returns function name from url 
    # Params        : 
    /*****************************************************/
    public static function getFunctionNameFromUrl()
    {
        $url = Request::getRequestUri();
        $urlAfterVersion = substr($url, strpos($url, 'v1/') + 3);
        $urlSegments = explode("/",$urlAfterVersion);
        $functionName = $urlSegments[0];
        if($functionName == 'user-register'){
            return 'sign_up';
        }else if($functionName == 'user'){
            return 'user_details';
        }else if($functionName == 'settings'){
            return 'site_settings_list';
        }else if($functionName == 'category'){
            return 'category_list';
        }else if($functionName == 'product' || $functionName == 'contract' || $functionName == 'orders'){
            $return = '';
            foreach($urlSegments as $key => $val){
                $return .= str_replace("-","_",$val).'_';
            }
            return  rtrim($return,"_");
        }else if($functionName == 'bids'){
            return  $urlSegments[0].'_'. $urlSegments[1];
        }else if($functionName == 'contract'){
            return  $urlSegments[0].'_'. $urlSegments[1];
        }
        return str_replace("-","_",$functionName);

    }

    /*****************************************************/
    # ApiHelper
    # Function name : replaceNulltoEmptyString
    # Author        :
    # Created Date  : 04-07-2019
    # Purpose       : returns array with null value replaced with blank string
    # Params        : 
    /*****************************************************/
    
    public static function replaceNulltoEmptyStringAndIntToString($arr){
        array_walk_recursive($arr, function (&$item, $key) {
            $item = null === $item ? '' : $item;
            if($key != 'id'){
                $item = (gettype($item) == 'integer' || gettype($item) == 'double') ? (string)$item : $item;
            }
            $item = Helper::cleanString($item);
        });
        return $arr;
    }

    /*****************************************************/
    # ApiHelper
    # Function name : getCartItemDetails
    # Author        :
    # Created Date  : 21-08-2019
    # Purpose       : Get cart details
    # Params        :
    /*****************************************************/
    public static function getCartItemDetails($userData,$lang)
    {
        

        $cartUserId = 0; $totalCartCount = 0;
        $getCartData = array();
        
        $cartUserId = $userData->id;
        $cartConditions = ['user_id' => $cartUserId, 'type' => 'C'];
        

        $getOrderDetails = Order::where($cartConditions)->with([ 
            'orderDetails'=> function($query) use ($lang) {
                $query->with([ 
                    'orderDetailLocals'=> function($query) use ($lang) {
                        $query->where('lang_code','=', $lang);
                    },
                ]);
            },
        ])->first();
        //dd($getOrderDetails->orderDetails);

        $grandCartPrice = 0;
        $totalCartPrice = 0;
        $totalVat = 0;
        $cartArray = array();
        $orderCoupon = ((Object)[]);
        $copon = 'N';
        $cartOrderId = 0;
        $wholesalerIds = [];
        $wholesalerProductTotalAmount = 0;
        $wholesalerExistFlag = 0;
        $productIds = [];
        $productTotalAmount = 0;
        $productExistFlag = 0;
        $categoryProductIds = [];
        $categoryProductTotalAmount = 0;
        $categoryProductExistFlag = 0;
        $amountFromDiscount = 0;
        $couponAmount = 0;
        $payableAmount = 0;        

        if ($getOrderDetails != null) {
            $baseUrl            = asset('');
            $productsUrl        = 'uploads/products/thumbs/';
            $productImageUrl    = $baseUrl.$productsUrl;
            $cartOrderId = $getOrderDetails->id;
            //Main Cart array
            if (isset($getOrderDetails->orderDetails) && count($getOrderDetails->orderDetails) > 0) {
                $i = 0;
                foreach ($getOrderDetails->orderDetails as $orderDetails) {
                    $productImage = ''; $vatAmount = 0;
                    
                    $cartArray[$i]['id'] = $orderDetails->id;
                    $cartArray[$i]['order_detail_id'] = $orderDetails->id;
                    $cartArray[$i]['order_id'] = $orderDetails->order_id;
                    $cartArray[$i]['product_id'] = $orderDetails->product_vendor_id;
                    $cartArray[$i]['product_vendor_id'] = $orderDetails->product_vendor_id;
                    $cartArray[$i]['mrp'] = $orderDetails->mrp ? (string)$orderDetails->mrp : '';
                    $cartArray[$i]['product_selling_price'] = $orderDetails->product_selling_price ? (string)$orderDetails->product_selling_price : '';
                    $cartArray[$i]['quantity'] = $orderDetails->quantity ? (string)$orderDetails->quantity : '';
                    $cartArray[$i]['total_price'] = $orderDetails->total_price ? (string)$orderDetails->total_price : '';
                    $cartArray[$i]['pack_value'] = $orderDetails->pack_value ? (string)number_format($orderDetails->pack_value) : '';
                    $cartArray[$i]['vendor_id'] = $orderDetails->vendor_id;
                    
                    $cartArray[$i]['vendor_shop_name'] = '';
                    $cartArray[$i]['wholesaler_shop_name'] = '';
                    if ($orderDetails->orderVendorDetails != null) {
                        $cartArray[$i]['vendor_shop_name'] = $orderDetails->orderVendorDetails['shop_name'];
                        $cartArray[$i]['wholesaler_shop_name'] = $orderDetails->orderVendorDetails['shop_name'];
                    }

                    $cartArray[$i]['product_category_id'] = '';
                    $cartArray[$i]['product_subcategory_id'] = '';
                    if ($orderDetails->productVendor != null) {
                        $cartArray[$i]['product_category_id'] = $orderDetails->productVendor['category_id'];
                        $cartArray[$i]['product_subcategory_id'] = $orderDetails->productVendor['product_subcategory_id'];
                    }

                    if ($orderDetails->vat_price != null) {
                        $vatAmount += $orderDetails->vat_price * $orderDetails->quantity;
                    }
                    $cartArray[$i]['vat_price'] = Helper::formatToTwoDecimalPlaces($vatAmount);

                    if (isset($orderDetails->productVendor->productDefaultImage) && $orderDetails->productVendor->productDefaultImage != null) {
                        if (count($orderDetails->productVendor->productDefaultImage)) {
                            $productImage = $orderDetails->productVendor->productDefaultImage[0]->image_name;
                            $cartArray[$i]['image'] = $productImageUrl.$productImage;
                        }else{
                            $cartArray[$i]['image'] = '';
                        }
                    }else{
                        $cartArray[$i]['image'] = '';
                    }

                    if (count($orderDetails->orderDetailLocals) > 0) {
                        
                        $cartArray[$i]['name'] = $orderDetails->orderDetailLocals[0]->local_product_name;
                        $cartArray[$i]['unit_name'] = $orderDetails->orderDetailLocals[0]->local_unit_name;
                        
                    } else {
                        $cartArray[$i]['name'] = '';
                        $cartArray[$i]['unit_name'] = '';
                    }
                    
                    //Total price
                    $totalCartPrice += $orderDetails->total_price;
                    $totalVat += $vatAmount;
                    
                    $i++;
                }
            }
        }
        //echo '<pre>'; print_r($cartArray); die;
        $payableAmount = $amountFromDiscount =  $grandCartPrice = $totalCartPrice;

        if ($getOrderDetails != null) {
            //Coupon checking section start
            if(isset($getOrderDetails->orderAppliedCoupon) && $getOrderDetails->orderAppliedCoupon != null) {
                $now = strtotime(date('Y-m-d H:i'));
                
                //Deleting coupon if coupon expiry time is over
                if($now > $getOrderDetails->orderAppliedCoupon['end_time']) {
                    OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                }

                //If Coupon related to whole cart
                if($getOrderDetails->orderAppliedCoupon['applied_for'] == 'WC') {
                    //If cart value is less than Minimum cart value
                    if( $totalCartPrice < $getOrderDetails->orderAppliedCoupon['minimum_cart_amount'] ) {
                        OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                    }
                }
                //If Coupon related to products
                else if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'P') {
                    $productIds = [];
                    if (strpos($getOrderDetails->orderAppliedCoupon['product_id'], ',') !== false) {
                        $productIds = explode(',', $getOrderDetails->orderAppliedCoupon['product_id']);
                    } else {
                        $productIds[] = $getOrderDetails->orderAppliedCoupon['product_id'];
                    }
                    //product total price & discount amount
                    if ((count($productIds) > 0) && (count($cartArray) > 0)) {
                        foreach ($cartArray as $checkProduct) {
                            if (in_array($checkProduct['product_vendor_id'], $productIds)) {
                                $productTotalAmount += $checkProduct['total_price'];
                                $productExistFlag++;
                            }
                        }
                        if ($productExistFlag == 0) {
                            OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                        } else {
                            //getting discount amount
                            if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                                $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                            }
                            else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                                $couponAmount = (($productTotalAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                            }
                            //if category product related total amount < coupon amount then delete that coupon
                            if ($productTotalAmount < $couponAmount) {
                                OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                            }
                        }
                    } else {
                        OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                    }
                    $productTotalAmount = 0;
                }
                //If Coupon related to category products
                else if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'C') {
                    $categoryProductIds = [];
                    if (strpos($getOrderDetails->orderAppliedCoupon['category_id'], ',') !== false) {
                        $categoryProductIds = explode(',', $getOrderDetails->orderAppliedCoupon['category_id']);
                    } else {
                        $categoryProductIds[] = $getOrderDetails->orderAppliedCoupon['category_id'];
                    }
                    //category product total price & discount amount
                    if ((count($categoryProductIds) > 0) && (count($cartArray) > 0)) {
                        foreach ($cartArray as $checkCategoryProduct) {
                            $categoryId = isset($checkCategoryProduct['product_category_id']) ? $checkCategoryProduct['product_category_id'] : 0;
                            $subcategoryId = isset($checkCategoryProduct['product_subcategory_id']) ? $checkCategoryProduct['product_subcategory_id'] : 0;
                            if(in_array($categoryId, $categoryProductIds) || in_array($subcategoryId, $categoryProductIds)){
                                $categoryProductTotalAmount += $checkCategoryProduct['total_price'];
                                $categoryProductExistFlag++;
                            }
                        }
                        if ($categoryProductExistFlag == 0) {
                            OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                        } else {
                            //getting discount amount
                            if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                                $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                            }
                            else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                                $couponAmount = (($categoryProductTotalAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                            }                            
                            //if category product related total amount < coupon amount then delete that coupon
                            if ($categoryProductTotalAmount < $couponAmount) {
                                OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                            }
                        }
                    } else {
                        OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                    }
                    $categoryProductTotalAmount = 0;
                }
                //If Coupon related to wholesaler products
                else if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'WP') {
                    $wholesalerIds = [];
                    if (strpos($getOrderDetails->orderAppliedCoupon['vendor_id'], ',') !== false) {
                        $wholesalerIds = explode(',', $getOrderDetails->orderAppliedCoupon['vendor_id']);
                    } else {
                        $wholesalerIds[] = $getOrderDetails->orderAppliedCoupon['vendor_id'];
                    }
                    //wholesaler product total price & discount amount
                    if ((count($wholesalerIds) > 0) && (count($cartArray) > 0)) {
                        foreach ($cartArray as $checkWholesaler) {
                            if (in_array($checkWholesaler['vendor_id'], $wholesalerIds)) {
                                $wholesalerProductTotalAmount += $checkWholesaler['total_price'];
                                $wholesalerExistFlag++;
                            }
                        }
                        if ($wholesalerExistFlag == 0) {
                            OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                        } else {
                            //getting discount amount
                            if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                                $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                            }
                            else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                                $couponAmount = (($categoryProductTotalAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                            }                            
                            //if wholesaler related products total amount < coupon amount then delete that coupon
                            if ($wholesalerProductTotalAmount < $couponAmount) {
                                OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                            }
                        }
                    } else {
                        OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                    }
                    $wholesalerProductTotalAmount = 0;
                }
                
                //Checking if no product exist then delete the coupon
                if (isset($getOrderDetails->orderDetails) && count($getOrderDetails->orderDetails) == 0) {
                    //Delete if any coupon is applied
                    OrderAppliedCoupon::where('order_id',$cartOrderId)->delete();
                }
            }
            //Coupon checking section end

            $getOrderDetails = Order::where($cartConditions)->first();
            if ($getOrderDetails->orderAppliedCoupon != null) {
                $orderCoupon = [];
                $orderCoupon['id'] = $getOrderDetails->orderAppliedCoupon['id'];
                $orderCoupon['coupon_id'] = $getOrderDetails->orderAppliedCoupon['coupon_id'];
                $orderCoupon['applied_for'] = $getOrderDetails->orderAppliedCoupon['applied_for'];
                $orderCoupon['category_id'] = $getOrderDetails->orderAppliedCoupon['category_id'];
                $orderCoupon['product_id'] = $getOrderDetails->orderAppliedCoupon['product_id'];
                $orderCoupon['vendor_id'] = $getOrderDetails->orderAppliedCoupon['vendor_id'];
                $orderCoupon['coupon_code'] = $getOrderDetails->orderAppliedCoupon['coupon_code'];                
                $orderCoupon['discount_type'] = $getOrderDetails->orderAppliedCoupon['discount_type'];
                $orderCoupon['coupon_amount'] = $getOrderDetails->orderAppliedCoupon['coupon_amount'];                
                //$orderCoupon['coupon_discount_amount'] = $getOrderDetails->orderAppliedCoupon['coupon_discount_amount'];

                //Update grand total
                $couponAmount = 0; $productIds = []; $productTotalAmount = 0; $wholesalerIds = []; $wholesalerProductTotalAmount = 0; $categoryProductIds = []; $categoryProductTotalAmount = 0;
                if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'WC') {  //based on whole cart
                    //discount amount
                    if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                        $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                    }
                    else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                        $couponAmount = (($payableAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                    }
                }                
                else if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'P') {  //based on products
                    if (strpos($getOrderDetails->orderAppliedCoupon['product_id'], ',') !== false) {
                        $productIds = explode(',', $getOrderDetails->orderAppliedCoupon['product_id']);
                    } else {
                        $productIds[] = $getOrderDetails->orderAppliedCoupon['product_id'];
                    }
                    //product total price & discount amount
                    if ((count($productIds) > 0) && (count($cartArray) > 0)) {
                        foreach ($cartArray as $checkProduct) {
                            if (in_array($checkProduct['product_vendor_id'], $productIds)) {
                                $productTotalAmount += $checkProduct['total_price'];
                            }
                        }
                        if ($productTotalAmount > 0) {
                            if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                                $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                            }
                            else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                                $couponAmount = (($productTotalAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                            }
                        }
                    }
                }
                else if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'C') {  //based on categories
                    if (strpos($getOrderDetails->orderAppliedCoupon['category_id'], ',') !== false) {
                        $categoryProductIds = explode(',', $getOrderDetails->orderAppliedCoupon['category_id']);
                    } else {
                        $categoryProductIds[] = $getOrderDetails->orderAppliedCoupon['category_id'];
                    }
                    //category product total price & discount amount
                    if ((count($categoryProductIds) > 0) && (count($cartArray) > 0)) {
                        foreach ($cartArray as $checkCategoryProduct) {
                            $categoryId = isset($checkCategoryProduct['product_category_id']) ? $checkCategoryProduct['product_category_id'] : 0;
                            $subcategoryId = isset($checkCategoryProduct['product_subcategory_id']) ? $checkCategoryProduct['product_subcategory_id'] : 0;
                            if(in_array($categoryId, $categoryProductIds) || in_array($subcategoryId, $categoryProductIds)){
                                $categoryProductTotalAmount += $checkCategoryProduct['total_price'];
                            }
                        }
                        if ($categoryProductTotalAmount > 0) {
                            if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                                $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                            }
                            else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                                $couponAmount = (($categoryProductTotalAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                            }
                        }
                    }
                }
                else if ($getOrderDetails->orderAppliedCoupon['applied_for'] == 'WP') {  //based on wholesaler products
                    if (strpos($getOrderDetails->orderAppliedCoupon['vendor_id'], ',') !== false) {
                        $wholesalerIds = explode(',', $getOrderDetails->orderAppliedCoupon['vendor_id']);
                    } else {
                        $wholesalerIds[] = $getOrderDetails->orderAppliedCoupon['vendor_id'];
                    }
                    //wholesaler product total price & discount amount
                    if ((count($wholesalerIds) > 0) && (count($cartArray) > 0)) {
                        foreach ($cartArray as $checkWholesaler) {
                            if (in_array($checkWholesaler['vendor_id'], $wholesalerIds)) {
                                $wholesalerProductTotalAmount += $checkWholesaler['total_price'];
                            }
                        }
                        if ($wholesalerProductTotalAmount > 0) {
                            if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'F') {
                                $couponAmount = $getOrderDetails->orderAppliedCoupon['coupon_amount'];
                            }
                            else if ($getOrderDetails->orderAppliedCoupon['discount_type'] == 'P') {
                                $couponAmount = (($wholesalerProductTotalAmount * $getOrderDetails->orderAppliedCoupon['coupon_amount']) / 100);
                            }
                        }
                    }
                }

                $payableAmount = $payableAmount - $couponAmount;
                $copon = 'Y';
            } else {
                $copon = 'N';
                $orderCoupon = ((Object)[]);
                $couponAmount = 0;
            }
        }
        //echo '<pre>'; print_r($cartArray); die;
        
        $totalCartCount = count($cartArray);
        $cartDetailArray = array('cartOrderId' => $cartOrderId,'itemDetails' => $cartArray, 'totalItem' => $totalCartCount, 'totalCartPrice' => Helper::formatToTwoDecimalPlaces($totalCartPrice), 'totalVat' => Helper::formatToTwoDecimalPlaces($totalVat), 'grandCartPrice' => Helper::formatToTwoDecimalPlaces($grandCartPrice), 'orderCoupon' => $orderCoupon, 'payableAmount' => Helper::formatToTwoDecimalPlaces($payableAmount), 'couponAmount' => Helper::formatToTwoDecimalPlaces($couponAmount), 'isCouponApplied' => $copon);

        return $cartDetailArray;
    }

}
