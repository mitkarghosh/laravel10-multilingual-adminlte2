<?php
/*****************************************************/
# Page/Class name   : Controller
# Purpose           :
/*****************************************************/

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use \Auth;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailLocal;
use App\Models\OrderAttributeLocal;
use App\Models\OrderIngredient;
use App\Models\OrderIngredientLocal;
use Helper;
use Twilio\Rest\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $currentLang;
    private $setLang;

    public function __construct(Request $request)
    {
		// For frontend
        if (strpos(\Request::fullUrl(), 'securepanel') !== false) {
            $segmentValue = $request->segment(2);
        }
        // For admin
        else {
            $segmentValue = $request->segment(1);
        }		

        // Setting language
        if (in_array($segmentValue, Helper::WEBITE_LANGUAGES)) {
            Session::put('websiteLang', '');
            Session::put('websiteLang', $segmentValue);
            \App::setLocale($segmentValue);
        } else {
            Session::put('websiteLang', '');
            Session::put('websiteLang', \App::getLocale());
            \App::setLocale(\App::getLocale());
        }
    }

    /*****************************************************/
    # Function name : getSessionWiseCartItemDetails
    # Params        :
    /*****************************************************/
    public function getSessionWiseCartItemDetails()
    {
        $currentSessionId = 0;
        $sessionCartArray = [];
        $cartOrderId = 0;
        $getSessionCartData = [];

        if (Session::get('cartSessionId') != '') {
            $currentSessionId = Session::get('cartSessionId');

            $cartConditions = ['session_id' => $currentSessionId, 'type' => 'C'];

            $getOrderDetails = Order::where($cartConditions)->first();
            // dd($getOrderDetails->orderDetails);

            $totalCartPrice = 0;            
            $paymentMethod = 0;
            $productExistFlag = 0;
            
            if ($getOrderDetails != null) {
                $cartOrderId = $getOrderDetails->id;
                $paymentMethod = $getOrderDetails->payment_method;
                // Main Cart array
                if (isset($getOrderDetails->orderDetails) && count($getOrderDetails->orderDetails) > 0) {
                    $i = 0;
                    foreach ($getOrderDetails->orderDetails as $orderDetails) {
                        $productImage = ''; $vatAmount = 0;
                        $sessionCartArray[$i]['id']                = $orderDetails->id;
                        $sessionCartArray[$i]['order_id']          = $orderDetails->order_id;
                        $sessionCartArray[$i]['product_id']        = $orderDetails->product_id;
                        $sessionCartArray[$i]['drink_id']          = $orderDetails->drink_id;
                        $sessionCartArray[$i]['special_menu_id']   = $orderDetails->special_menu_id;
                        $sessionCartArray[$i]['has_ingredients']   = $orderDetails->has_ingredients;
                        $sessionCartArray[$i]['has_attribute']     = $orderDetails->has_attribute;
                        $sessionCartArray[$i]['attribute_id']      = $orderDetails->attribute_id;
                        $sessionCartArray[$i]['is_menu']           = $orderDetails->is_menu;
                        $sessionCartArray[$i]['menu_option_ids']   = $orderDetails->menu_option_ids;
                        $sessionCartArray[$i]['quantity']          = $orderDetails->quantity;
                        $sessionCartArray[$i]['price']             = $orderDetails->price;
                        $sessionCartArray[$i]['total_price']       = $orderDetails->total_price;
                        
                        // order product locals
                        if (count($orderDetails->orderDetailLocals) > 0) {
                            foreach ($orderDetails->orderDetailLocals as $detailLocal) {
                                $sessionCartArray[$i]['local_details'][$detailLocal->lang_code]['local_title'] = $detailLocal->local_title;
                            }
                        } else {
                            $sessionCartArray[$i]['local_details'] = [];
                        }

                        // ingredients locals
                        if (count($orderDetails->orderIngredients) > 0) {
                            foreach ($orderDetails->orderIngredients as $key => $orderIngredient) {
                                $sessionCartArray[$i]['ingredient_local_details'][$key]['order_ingredient_id'] = $orderIngredient->id;
                                $sessionCartArray[$i]['ingredient_local_details'][$key]['ingredient_id']   = $orderIngredient->ingredient_id;
                                $sessionCartArray[$i]['ingredient_local_details'][$key]['product_id']      = $orderIngredient->product_id;
                                $sessionCartArray[$i]['ingredient_local_details'][$key]['quantity']        = $orderIngredient->quantity;
                                $sessionCartArray[$i]['ingredient_local_details'][$key]['price']           = $orderIngredient->price;
                                $sessionCartArray[$i]['ingredient_local_details'][$key]['total_price']     = $orderIngredient->total_price;
                                foreach ($orderIngredient->orderIngredientLocals as $detailIngredientLocal) {
                                    $sessionCartArray[$i]['ingredient_local_details'][$key][$detailIngredientLocal->lang_code]['local_title'] = $detailIngredientLocal->local_ingredient_title;
                                }
                            }
                        } else {
                            $sessionCartArray[$i]['ingredient_local_details'] = [];
                        }

                        // attributes locals
                        if ($orderDetails->has_attribute == 'Y') {
                            $orderAttributeLocalDetails = OrderAttributeLocal::where([
                                                                                    'order_id'          => $orderDetails->order_id,
                                                                                    'order_details_id'  => $orderDetails->id,
                                                                                    'product_id'        => $orderDetails->product_id,
                                                                                    'attribute_id'      => $orderDetails->attribute_id,
                                                                                ])
                                                                                ->get();
                            
                            foreach ($orderAttributeLocalDetails as $key => $detailAttributeLocal) {
                                $sessionCartArray[$i]['attribute_local_details'][$detailAttributeLocal->lang_code]['local_title'] = $detailAttributeLocal->local_attribute_title;
                            }
                        } else {
                            $sessionCartArray[$i]['attribute_local_details'] = [];
                        }                

                        if ($orderDetails->product_id != '') {
                            $productExistFlag = 1;
                        }

                        //Total price
                        $totalCartPrice += $orderDetails->total_price;
                        $i++;
                    }
                }
                $getSessionCartData = array('cartOrderId' => $cartOrderId, 'sessionItemDetails' => $sessionCartArray);
            }
        }        
        // echo '<pre>'; print_r($getSessionCartData); die;
        return $getSessionCartData;
    }

    /*****************************************************/
    # Function name : mergeCartItemDetails
    # Params        :
    /*****************************************************/
    public function mergeCartItemDetails()
    {
        if (Auth::user()) {
            $currentSessionWiseOrderDetails = self::getSessionWiseCartItemDetails();    //current session id wise order details
            
            $userExistingOrderDetails = Helper::getCartItemDetails();    // user existing order details
            // dd($userExistingOrderDetails);
            
            // update payment method
            $currentSessionCartOrderIdNew = isset($currentSessionWiseOrderDetails['cartOrderId']) ? $currentSessionWiseOrderDetails['cartOrderId'] : 0;
            
            $existingCartOrderIdNew = isset($userExistingOrderDetails['cartOrderId']) ? $userExistingOrderDetails['cartOrderId'] : 0;
            
            if (!empty($userExistingOrderDetails) && !empty($userExistingOrderDetails['itemDetails']) && $currentSessionCartOrderIdNew != 0) {
                $paymentMethod = Order::select('payment_method')->where('id', $currentSessionCartOrderIdNew)->first()->payment_method;
                Order::where([
                    'id' => $existingCartOrderIdNew
                ])->update([
                    'payment_method' => $paymentMethod
                ]);
            }

            // If session related cart data exist
            if (!empty($currentSessionWiseOrderDetails) && !empty($currentSessionWiseOrderDetails['sessionItemDetails'])) {

                // Session related order id
                $currentSessionCartOrderId = isset($currentSessionWiseOrderDetails['cartOrderId']) ? $currentSessionWiseOrderDetails['cartOrderId'] : 0;
                                
                // Existing order id
                $existingCartOrderId = isset($userExistingOrderDetails['cartOrderId']) ? $userExistingOrderDetails['cartOrderId'] : 0;
                // echo $existingCartOrderId = isset($userExistingOrderDetails['cartOrderId']) ? $userExistingOrderDetails['cartOrderId'] : 0;
                // echo '<hr>';

                $updatedQuantity = 0; $updatedTotalPrice = 0;
                
                // Loop for Session Cart related details start
                foreach ($currentSessionWiseOrderDetails['sessionItemDetails'] as $sessionKey => $sessionValue) {
                    $sessionOrderId         = isset($sessionValue['order_id']) ? $sessionValue['order_id'] : 0;
                    $sessionOrderDetailId   = isset($sessionValue['id']) ? $sessionValue['id'] : 0;
                    $sessionProductPrice    = isset($sessionValue['price']) ? $sessionValue['price'] : null;
                    $sessionProductQuantity = isset($sessionValue['quantity']) ? $sessionValue['quantity'] : 1;

                    // Loop for EXISTING order details start here
                    if (!empty($userExistingOrderDetails) && !empty($userExistingOrderDetails['itemDetails']) && $currentSessionCartOrderId != 0) {

                        foreach ($userExistingOrderDetails['itemDetails'] as $userCartKey => $userCartValue) {
                            // echo '<pre>'; print_r($userCartValue); die;

                            // Product
                            if ($userCartValue['product_id'] != '') {
                                // If attribute exist START
                                if ($userCartValue['has_attribute'] == 'Y') {
                                    // Product id and Attribute id matched START
                                    if ($userCartValue['product_id'] == $sessionValue['product_id'] && $userCartValue['attribute_id'] == $sessionValue['attribute_id']) {
                                        $updatedAttributeQuantity   = $sessionProductQuantity + $userCartValue['quantity'];
                                        $updatedAttributeTotalPrice = $sessionValue['price'] * $updatedAttributeQuantity;

                                        // Update user order detail
                                        OrderDetail::where([
                                                        'id'            => $userCartValue['id'],
                                                        'order_id'      => $userCartValue['order_id'],
                                                        'product_id'    => $userCartValue['product_id'],
                                                        'attribute_id'  => $userCartValue['attribute_id'],
                                                    ])
                                                    ->update([
                                                        'quantity'          => $updatedAttributeQuantity,
                                                        'price'             => $sessionValue['price'],
                                                        'unit_total_price'  => $updatedAttributeTotalPrice,
                                                        'total_price'       => $updatedAttributeTotalPrice,
                                                    ]);

                                        // So delete the session id respective order detail
                                        $sessionOrd = OrderDetail::where([
                                                                        'id'            => $sessionOrderDetailId,
                                                                        'order_id'      => $currentSessionCartOrderId,
                                                                        'product_id'    => $sessionValue['product_id'],
                                                                        'attribute_id'  => $sessionValue['attribute_id'],
                                                                    ])
                                                                    ->get();
                                        if (count($sessionOrd) > 0) {
                                            foreach ($sessionOrd as $detailsKey => $detailsValue) {
                                                OrderDetailLocal::where([
                                                                    'order_details_id' => $detailsValue->id
                                                                ])
                                                                ->delete();

                                                OrderDetail::where([
                                                                'id'            => $detailsValue->id,
                                                                'order_id'      => $currentSessionCartOrderId,
                                                                'product_id'    => $detailsValue->product_id,
                                                                'attribute_id'  => $detailsValue->attribute_id,
                                                            ])
                                                            ->delete();

                                                OrderAttributeLocal::where([
                                                                'order_id'      => $currentSessionCartOrderId,
                                                                'product_id'    => $detailsValue->product_id,
                                                                'attribute_id'  => $detailsValue->attribute_id,
                                                            ])
                                                            ->delete();
                                            }
                                        }
                                    }
                                    // Product id and Attribute id matched END                                    
                                }
                                // If attribute exist END
                                // If attribute NOT exist START
                                else {
                                    
                                    // If Ingredient NOT exist START
                                    if ($userCartValue['has_ingredients'] == 'N') {
                                        // Product id matched && It is not a Menu (Drop down) START
                                        if ($userCartValue['product_id'] == $sessionValue['product_id'] && $sessionValue['is_menu'] != 'Y') {

                                            // Only product related operations START
                                            if (empty($sessionValue['ingredient_local_details'])) {
                                                $updatedProductQuantity   = $sessionProductQuantity + $userCartValue['quantity'];
                                                $updatedProductTotalPrice = $sessionValue['price'] * $updatedProductQuantity;

                                                // Update user order detail
                                                OrderDetail::where([
                                                                'id'            => $userCartValue['id'],
                                                                'order_id'      => $userCartValue['order_id'],
                                                                'product_id'    => $userCartValue['product_id'],
                                                            ])->update([
                                                                'quantity'          => $updatedProductQuantity,
                                                                'price'             => $sessionValue['price'],
                                                                'unit_total_price'  => $updatedProductTotalPrice,
                                                                'total_price'       => $updatedProductTotalPrice,
                                                            ]);

                                                // So delete the session id respective order detail
                                                $sessionOrd = OrderDetail::where([
                                                                                'id'            => $sessionOrderDetailId,
                                                                                'order_id'      => $currentSessionCartOrderId,
                                                                                'product_id'    => $sessionValue['product_id'],
                                                                            ])
                                                                            ->get();
                                                if (count($sessionOrd) > 0) {
                                                    foreach ($sessionOrd as $detailsKey => $detailsValue) {
                                                        OrderDetailLocal::where([
                                                                            'order_details_id' => $detailsValue->id
                                                                        ])
                                                                        ->delete();

                                                        OrderDetail::where([
                                                                        'id'            => $detailsValue->id,
                                                                        'order_id'      => $currentSessionCartOrderId,
                                                                        'product_id'    => $detailsValue->product_id,
                                                                    ])
                                                                    ->delete();
                                                    }
                                                }
                                            }
                                            // Only product related operations END
                                            // Product + Ingredient related operations START
                                            else {
                                                $existingOrderDetails = OrderDetail::where([
                                                                                        'id'            => $userCartValue['id'],
                                                                                        'order_id'      => $userCartValue['order_id'],
                                                                                        'product_id'    => $userCartValue['product_id'],
                                                                                    ])
                                                                                    ->first();
                                                
                                                if (!empty($sessionValue['ingredient_local_details'])) {
                                                    $updatedProductQuantity   = $sessionProductQuantity + $userCartValue['quantity'];
                                                    $updatedProductTotalPrice = $sessionValue['price'] * $updatedProductQuantity;

                                                    $totalIngredientPrice = 0;
                                                    foreach ($sessionValue['ingredient_local_details'] as $key => $val) {
                                                        $totalIngredientPrice += $val['total_price'];

                                                        // Update order ingredient & order ingredient local
                                                        OrderIngredient::where([
                                                                            'id' => $val['order_ingredient_id'],
                                                                        ])
                                                                        ->update([
                                                                            'order_id'          => $existingOrderDetails->order_id,
                                                                            'order_details_id'  => $existingOrderDetails->id,
                                                                        ]);
                                                        OrderIngredientLocal::where([
                                                                            'order_ingredient_id' => $val['order_ingredient_id'],
                                                                        ])
                                                                        ->update([
                                                                            'order_id'          => $existingOrderDetails->order_id,
                                                                            'order_details_id'  => $existingOrderDetails->id,
                                                                        ]);
                                                    }

                                                    $existingOrderDetails->has_ingredients  = 'Y';
                                                    $existingOrderDetails->quantity         = $updatedProductQuantity;
                                                    $existingOrderDetails->price            = $sessionValue['price'];
                                                    $existingOrderDetails->unit_total_price = $updatedProductTotalPrice;
                                                    $existingOrderDetails->total_price      = $updatedProductTotalPrice + $totalIngredientPrice;
                                                    $existingOrderDetails->save();

                                                    // Delete order details & order details local
                                                    OrderDetail::where([
                                                                    'id'        => $sessionValue['id'],
                                                                    'order_id'  => $sessionValue['order_id'],
                                                                    'product_id'=> $sessionValue['product_id'],
                                                                ])
                                                                ->delete();
                                                    OrderDetailLocal::where([
                                                                        'order_id'          => $sessionValue['order_id'],
                                                                        'order_details_id'  => $sessionValue['id'],
                                                                        'product_id'        => $sessionValue['product_id'],
                                                                    ])
                                                                    ->delete();
                                                }
                                            }
                                            // Product + Ingredient related operations END

                                        }
                                        // Product id matched && It is not a Menu (Drop down) END
                                        // Product id matched && It is Menu (Drop down) START
                                        else {
                                            $existingMenuOrderDetails = OrderDetail::where([
                                                                                        'id'                => $userCartValue['id'],
                                                                                        'order_id'          => $userCartValue['order_id'],
                                                                                        'product_id'        => $userCartValue['product_id'],
                                                                                        'is_menu'           => $userCartValue['is_menu'],
                                                                                        'menu_option_ids'   => $sessionValue['menu_option_ids'],
                                                                                    ])
                                                                                    ->first();
                                            // If match found
                                            if ($existingMenuOrderDetails != null) {
                                                $updatedProductQuantity   = $sessionProductQuantity + $existingMenuOrderDetails['quantity'];
                                                $updatedProductTotalPrice = $sessionValue['price'] * $updatedProductQuantity;

                                                $existingMenuOrderDetails->quantity         = $updatedProductQuantity;
                                                $existingMenuOrderDetails->price            = $sessionValue['price'];
                                                $existingMenuOrderDetails->unit_total_price = $updatedProductTotalPrice;
                                                $existingMenuOrderDetails->total_price      = $updatedProductTotalPrice;
                                                $existingMenuOrderDetails->save();

                                                // Delete order details & order details local
                                                OrderDetail::where([
                                                    'id'                => $sessionValue['id'],
                                                    'order_id'          => $sessionValue['order_id'],
                                                    'product_id'        => $sessionValue['product_id'],
                                                    'menu_option_ids'   => $sessionValue['menu_option_ids'],
                                                ])
                                                ->delete();
                                                OrderDetailLocal::where([
                                                                    'order_id'          => $sessionValue['order_id'],
                                                                    'order_details_id'  => $sessionValue['id'],
                                                                    'product_id'        => $sessionValue['product_id'],
                                                                ])
                                                                ->delete();
                                            }
                                        }
                                        // Product id matched && It is Menu (Drop down) END
                                    }
                                    else if ($userCartValue['has_ingredients'] == 'Y') {

                                        // Product id matched START
                                        if ($userCartValue['product_id'] == $sessionValue['product_id']) {
                                            // echo '<hr>here is the';
                                            $updatedProductQuantity   = $sessionProductQuantity + $userCartValue['quantity'];
                                            $updatedProductTotalPrice = $sessionValue['price'] * $updatedProductQuantity;
                                            
                                            // Only product & existing ingredients related operations START
                                            if (empty($sessionValue['ingredient_local_details'])) {

                                                // Get Total Amount of Existing Cart data from order ingredients
                                                $existingIngredientsTotal = 0;
                                                foreach ($userCartValue['ingredient_local_details'] as $key => $val) {
                                                    $existingIngredientsTotal += $val['total_price'];
                                                }

                                                // Update user order detail
                                                OrderDetail::where([
                                                                'id'            => $userCartValue['id'],
                                                                'order_id'      => $userCartValue['order_id'],
                                                                'product_id'    => $userCartValue['product_id'],
                                                            ])->update([
                                                                'quantity'          => $updatedProductQuantity,
                                                                'price'             => $sessionValue['price'],
                                                                'unit_total_price'  => $updatedProductTotalPrice,
                                                                'total_price'       => ($updatedProductTotalPrice + $existingIngredientsTotal),
                                                            ]);

                                                // So delete the session id respective order detail
                                                $sessionOrd = OrderDetail::where([
                                                                                'id'            => $sessionOrderDetailId,
                                                                                'order_id'      => $currentSessionCartOrderId,
                                                                                'product_id'    => $sessionValue['product_id'],
                                                                            ])
                                                                            ->get();
                                                if (count($sessionOrd) > 0) {
                                                    foreach ($sessionOrd as $detailsKey => $detailsValue) {
                                                        OrderDetailLocal::where([
                                                                            'order_details_id' => $detailsValue->id
                                                                        ])
                                                                        ->delete();

                                                        OrderDetail::where([
                                                                        'id'            => $detailsValue->id,
                                                                        'order_id'      => $currentSessionCartOrderId,
                                                                        'product_id'    => $detailsValue->product_id,
                                                                    ])
                                                                    ->delete();
                                                    }
                                                }
                                            }
                                            // Only product related operations END
                                            
                                            // Product + Ingredient related operations START
                                            else {
                                                $existingIngredientsTotal = 0;
                                                
                                                // Session wise Ingredient loop
                                                foreach($sessionValue['ingredient_local_details'] as $sessionKeyIngredient => $sessionValueIngredient) {  
                                                    $sessionProductIngredientExist = OrderIngredient::where([
                                                                                                        'id' => $sessionValueIngredient['order_ingredient_id']
                                                                                                        ])
                                                                                                        ->first();

                                                    $currentSessionIngredientUpdateStatus = 0;
                                                    
                                                    // User cart wise Ingredient loop START
                                                    foreach($userCartValue['ingredient_local_details'] as $userKeyIngredient => $userValueIngredient) {

                                                        if ($userValueIngredient['ingredient_id'] == $sessionProductIngredientExist->ingredient_id && $userValueIngredient['product_id'] == $sessionProductIngredientExist->product_id) {

                                                            $currentSessionIngredientUpdateStatus = 1;

                                                            $updatedIngredientQuantity   = $sessionValueIngredient['quantity'] + $userValueIngredient['quantity'];
                                                            $updatedIngredientTotalPrice = $sessionValueIngredient['price'] * $updatedIngredientQuantity;

                                                            $existingIngredientsTotal += $updatedIngredientTotalPrice;

                                                            // Update user order ingredient table
                                                            OrderIngredient::where([
                                                                                'id'            => $userValueIngredient['order_ingredient_id'],
                                                                                'order_id'      => $userCartValue['order_id'],
                                                                                'product_id'    => $userCartValue['product_id'],
                                                                            ])
                                                                            ->update([
                                                                                'quantity'      => $updatedIngredientQuantity,
                                                                                'price'         => $sessionValueIngredient['price'],
                                                                                'total_price'   => $updatedIngredientTotalPrice,
                                                                            ]);

                                                            // So delete the session id respective order ingredient & local details
                                                            $sessionOrdIngredient = OrderIngredient::where([
                                                                                        'id'        => $sessionValueIngredient['order_ingredient_id'],
                                                                                        'order_id'  => $sessionOrderId,
                                                                                        'product_id'=> $sessionValueIngredient['product_id'],
                                                                                    ])
                                                                                ->get();
                                                            if (count($sessionOrdIngredient) > 0) {
                                                                foreach($sessionOrdIngredient as $ingredientDetailsKey => $ingredientDetailsValue) {
                                                                    OrderIngredientLocal::where([
                                                                                            'order_ingredient_id' => $ingredientDetailsValue->id,
                                                                                            'order_id'          => $sessionOrderId,
                                                                                            'order_details_id'  => $sessionOrderDetailId,
                                                                                        ])
                                                                                        ->delete();

                                                                    OrderIngredient::where([
                                                                                        'id'                => $ingredientDetailsValue->id,
                                                                                        'order_id'          => $sessionOrderId,
                                                                                        'order_details_id'  => $sessionOrderDetailId,
                                                                                    ])
                                                                                    ->delete();
                                                                }
                                                            }
                                                        }
                                                    }
                                                    // User cart wise Ingredient loop END
                                                    
                                                    // If session wise ingredient NOT matched with user cart ingredients then update to => User cart
                                                    if ($currentSessionIngredientUpdateStatus == 0) {
                                                        $sessionProductIngredientExist->order_id         = $userCartValue['order_id'];
                                                        $sessionProductIngredientExist->order_details_id = $userCartValue['id'];
                                                        $sessionProductIngredientExist->save();

                                                        $existingIngredientsTotal += $sessionProductIngredientExist->total_price;

                                                        OrderIngredientLocal::where('order_ingredient_id', $sessionProductIngredientExist->id)
                                                                            ->update([
                                                                                'order_id'          => $userCartValue['order_id'],
                                                                                'order_details_id'  => $userCartValue['id'],
                                                                            ]);
                                                    }

                                                    // Checking if session order_details_id respective any Ingredients exists or not : If not then delete Order Details & Order Details Local
                                                    $checkingSessionRespectiveIngredientsExist = OrderIngredient::where([
                                                                                    'order_details_id'  => $sessionOrderDetailId,
                                                                                    'order_id'          => $sessionOrderId,
                                                                                    // 'product_id'        => $sessionValueIngredient['product_id'],
                                                                                ])
                                                                                ->count();
                                                    if ($checkingSessionRespectiveIngredientsExist == 0) {
                                                        OrderDetail::where([
                                                                        'id'        => $sessionOrderDetailId,
                                                                        'order_id'  => $sessionOrderId,
                                                                        'product_id'=> $sessionValueIngredient['product_id'],
                                                                    ])
                                                                    ->delete();

                                                        OrderDetailLocal::where([
                                                                        'order_id'          => $sessionOrderId,
                                                                        'order_details_id'  => $sessionOrderDetailId,
                                                                        'product_id'        => $sessionValueIngredient['product_id'],
                                                                    ])
                                                                    ->delete();
                                                        }
                                                    }

                                                    // Update user order detail
                                                    OrderDetail::where([
                                                                    'id'            => $userCartValue['id'],
                                                                    'order_id'      => $userCartValue['order_id'],
                                                                    'product_id'    => $userCartValue['product_id'],
                                                                ])
                                                                ->update([
                                                                    'quantity'          => $updatedProductQuantity,
                                                                    'price'             => $sessionValue['price'],
                                                                    'unit_total_price'  => $updatedProductTotalPrice,
                                                                    'total_price'       => ($updatedProductTotalPrice + $existingIngredientsTotal),
                                                                ]);                                                
                                            }
                                            // Product + Ingredient related operations END
                                        }
                                    }
                                }
                            }
                            
                            // Drinks
                            else if ($userCartValue['drink_id'] != '') {
                                // Drink id matched START
                                if ($userCartValue['drink_id'] == $sessionValue['drink_id']) {
                                    $updatedDrinkQuantity   = $sessionProductQuantity + $userCartValue['quantity'];
                                    $updatedDrinkTotalPrice = $sessionValue['price'] * $updatedDrinkQuantity;

                                    // Update user order detail
                                    OrderDetail::where([
                                        'id'        => $userCartValue['id'],
                                        'order_id'  => $userCartValue['order_id'],
                                    ])->update([
                                        'quantity'          => $updatedDrinkQuantity,
                                        'price'             => $sessionValue['price'],
                                        'unit_total_price'  => $updatedDrinkTotalPrice,
                                        'total_price'       => $updatedDrinkTotalPrice,
                                    ]);

                                    // So delete the session id respective order detail
                                    $sessionOrd = OrderDetail::where([
                                                                    'id'        => $sessionOrderDetailId,
                                                                    'order_id'  => $currentSessionCartOrderId,
                                                                ])
                                                                ->get();
                                    if (count($sessionOrd) > 0) {
                                        foreach ($sessionOrd as $detailsKey => $detailsValue) {
                                            OrderDetailLocal::where([
                                                                'order_details_id' => $detailsValue->id
                                                            ])
                                                            ->delete();

                                            OrderDetail::where([
                                                            'id' => $detailsValue->id,
                                                            'order_id' => $currentSessionCartOrderId,
                                                        ])
                                                        ->delete();
                                        }
                                    }
                                }
                                // Drink id matched END
                            }

                            // Special Menu
                            else if ($userCartValue['special_menu_id'] != '') {
                                // Special Menu id matched START
                                if ($userCartValue['special_menu_id'] == $sessionValue['special_menu_id']) {
                                    $updatedDrinkQuantity   = $sessionProductQuantity + $userCartValue['quantity'];
                                    $updatedDrinkTotalPrice = $sessionValue['price'] * $updatedDrinkQuantity;

                                    // Update user order detail
                                    OrderDetail::where([
                                                    'id'        => $userCartValue['id'],
                                                    'order_id'  => $userCartValue['order_id'],
                                                ])->update([
                                                    'quantity'          => $updatedDrinkQuantity,
                                                    'price'             => $sessionValue['price'],
                                                    'unit_total_price'  => $updatedDrinkTotalPrice,
                                                    'total_price'       => $updatedDrinkTotalPrice,
                                                ]);

                                    // So delete the session id respective order detail
                                    $sessionOrd = OrderDetail::where([
                                                                    'id'        => $sessionOrderDetailId,
                                                                    'order_id'  => $currentSessionCartOrderId,
                                                                ])
                                                                ->get();
                                    if (count($sessionOrd) > 0) {
                                        foreach ($sessionOrd as $detailsKey => $detailsValue) {
                                            OrderDetailLocal::where([
                                                                'order_details_id' => $detailsValue->id
                                                            ])
                                                            ->delete();

                                            OrderDetail::where([
                                                            'id' => $detailsValue->id,
                                                            'order_id' => $currentSessionCartOrderId,
                                                        ])
                                                        ->delete();
                                        }
                                    }
                                }
                                // Special Menu id matched END
                            }
                        }
                    } else {
                        if( $currentSessionCartOrderId != 0 ) {
                            $ordData = Order::where('id',$currentSessionCartOrderId)->update(['session_id' => null, 'user_id' => Auth::user()->id]);
                            Session::put('cartSessionId','');
                        }
                    }
                }
                //Loop for Session Cart related details end

                // For those that doesn't match with the existing order details then change the ORDER ID
                if(!empty($userExistingOrderDetails) && !empty($userExistingOrderDetails['itemDetails'])) {
                    // Update the session order id with existing one
                    OrderDetail::where('order_id', $currentSessionCartOrderId)->update(['order_id' => $existingCartOrderId]);
                    OrderDetailLocal::where('order_id', $currentSessionCartOrderId)->update(['order_id' => $existingCartOrderId]);

                    // If Session id related attribute local exist record exist then change the ORDER ID
                    if (OrderAttributeLocal::where('order_id', $currentSessionCartOrderId)->count() > 0) {
                        OrderAttributeLocal::where('order_id', $currentSessionCartOrderId)->update(['order_id' => $existingCartOrderId]);
                    }

                    //If Session id related no record exist in order_details table then delete that order because all have been moved to LOGGED IN user's account
                    if (OrderDetail::where('order_id', $currentSessionCartOrderId)->count() == 0) {
                        Order::where('id',$currentSessionCartOrderId)->delete();
                    }

                    // Storing order id for further development
                    Session::put('lastCartOrderId', $existingCartOrderId);
                }

            }
        }
        // dd('merged successfully.');
    }

    /*****************************************************/
    # Function name : getRandomKey
    # Params        :
    /*****************************************************/
    public function getRandomKey($string_length=8,$is_numeric=0)
    {
        $randon_string = substr(str_shuffle(str_repeat($x = $is_numeric?'012345678955654667577678879878899977997':'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($string_length / strlen($x)))), 1, $string_length);

        return $randon_string;
    }

    /*****************************************************/
    # Function name : getRandomPassword
    # Params        :
    /*****************************************************/
    public function getRandomPassword($stringLength = 8)
    {
        $capitalRandom = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 2)), 0, 2);
        $smallRandom   = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3);
        $specailRandom = substr(str_shuffle(str_repeat("!@#$%^&*", 1)), 0, 1);
        $numberRandom  = substr(str_shuffle(str_repeat("0123456789", 1)), 0, 2);
        
        $randonString = $capitalRandom.$smallRandom.$specailRandom.$numberRandom;

        return $randonString;
    }

    /*****************************************************/
    # Function name : sendOrderMessage
    # Params        :
    /*****************************************************/
    public function sendOrderMessage($deliveryPhone, $uniqueOrderId)
    {
        try
        {
            $sid    = env('SMS_SID_KEY','ACdc0c320b703d5087fc9425a7fef33a30');
            $token  = env('SMS_TOKEN','6f3fce7f1a75ee0be2b38928a420e7f4');
            $twilio = new Client($sid, $token);

            $message = $twilio->messages
                ->create($deliveryPhone, // to
                        [
                            "body" => trans('custom.success_order_placed_success').'. '.trans('custom.sms_order_id_is', ['orderid' => $uniqueOrderId]),
                            "from" => "+12028312237"
                        ]
                );
            } catch (Exception $e) {
                $message = '';
            }

        return $message;
    }

    /*****************************************************/
    # Function name : sendOrderReviewMessage
    # Params        :
    /*****************************************************/
    public function sendOrderReviewMessage($deliveryPhone, $reviewLink)
    {
        try
        {
            $sid    = env('SMS_SID_KEY','ACdc0c320b703d5087fc9425a7fef33a30');
            $token  = env('SMS_TOKEN','6f3fce7f1a75ee0be2b38928a420e7f4');
            $twilio = new Client($sid, $token);

            $message = $twilio->messages
                ->create($deliveryPhone, // to
                        [
                            "body" => trans('custom_admin.label_order_review_link').'. '.$reviewLink,
                            "from" => "+12028312237"
                        ]
                );
            } catch (Exception $e) {
                $message = '';
            }

        return $message;
    }

}
