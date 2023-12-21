<?php
/*****************************************************/
# Page/Class name   : CartController
# Purpose           : Cart related functions
/*****************************************************/
namespace App\Http\Controllers\site;

use App;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailLocal;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeLocal;
use App\Models\Ingredient;
use App\Models\OrderIngredient;
use App\Models\OrderIngredientLocal;
use App\Models\OrderAttributeLocal;
use App\Models\Drink;
use App\Models\DrinkAttribute;
use App\Models\SpecialMenu;
use App\Models\SpecialMenuAttribute;
use App\Models\DeliverySlot;
use App\Models\ProductMenuTitle;
use App\Models\ProductMenuTitleLocal;
use App\Models\ProductMenuValue;
use App\Models\ProductMenuValueLocal;
use App\Models\Coupon;
use Helper;
use Cookie;
use App\Models\User;

class CartController extends Controller
{
    /*****************************************************/
    # Function name : index
    # Params        :
    /*****************************************************/
    public function index()
    {
        $cartDetails = Helper::getCartItemDetails();

        $currentLang = App::getLocale();
        $lang = strtoupper($currentLang);
        $metaData = Helper::getMetaData();

        return view('site.cart', [
            'title' => $metaData['title'],
            'keyword' => $metaData['keyword'],
            'description' => $metaData['description'],
            'lang' => $currentLang,
            'active' => 'cart',
            'cartDetails' => $cartDetails,
        ]);
    }

    /*****************************************************/
    # Function name : addToCart
    # Params        : Request $request
    /*****************************************************/
    public function addToCart(Request $request)
    {
        $title                  = trans('custom.error');
        $message                = trans('custom.please_try_again');
        $type                   = 'error';

        if ($request->isMethod('POST')) {
            $currentLang = App::getLocale();
            $lang = strtoupper($currentLang);
            $userData = Auth::user();
            $metaData = Helper::getMetaData();
    
            $totalCartItems = 0;
            $cartItems = [];
            $headerCart = $newOrdDtls = '';

            if (Session::get('cartSessionId') != '') {
                $sessionId = Session::get('cartSessionId');
            } else {
                $sessionId = Session::getId();
                Session::put('cartSessionId', $sessionId);
                $sessionId = Session::get('cartSessionId');
            }
            
            $conditions = [];
            if (Auth::user()) {
                $userId = Auth::user()->id;
                $conditions = ['user_id' => $userId, 'type' => 'C'];
                $sessionId = null;
                Session::put('cartSessionId', '');
            } else {
                $userId = null;
                $conditions = ['session_id' => $sessionId, 'type' => 'C'];
            }

            // Inputs
            $productId          = isset($request->productId) ? Helper::customEncryptionDecryption($request->productId, 'decrypt') : '';
            $showIngredients    = isset($request->showIngredients) ? $request->showIngredients : '';
            $ingredientIds      = isset($request->ingredientIds) ? $request->ingredientIds : '';

            if (strpos($ingredientIds,',') !== false) {
                $allIngredients = explode(',', $ingredientIds);
                asort($allIngredients);
            } else {
                $allIngredients[] = $ingredientIds;
            }
            $hasAttribute   = isset($request->hasAttribute) ? $request->hasAttribute : '';
            $attributeId    = isset($request->attributeId) ? Helper::customEncryptionDecryption($request->attributeId, 'decrypt') : '';
            $specialId      = isset($request->specialId) ? Helper::customEncryptionDecryption($request->specialId, 'decrypt') : '';
            $drinkId        = isset($request->drinkId) ? Helper::customEncryptionDecryption($request->drinkId, 'decrypt') : '';

            // drop down menu title and value data start

            if(isset($request->menuValueIds))
            {
                 if($request->menuValueIds=='null,'){
                    $request->menuValueIds='';
                 }
            }

            $isMenu             = isset($request->isMenu) ? $request->isMenu : 'N';
            $menuValueIds       = isset($request->menuValueIds) ? $request->menuValueIds : 0;

            

            $menuTitleValueIds  = $menuTitleValueDataEn = $menuTitleValueDataDe = $mainDropDownIds = [];
            $mainDropDownImplodedIds = null;
            if ($menuValueIds != null) {
                if (strpos($menuValueIds,',') !== false) {
                    $allMenuValIds = explode(',', $menuValueIds);
                    asort($allMenuValIds);

                    foreach ($allMenuValIds as $keyMenuVa => $valMenuVa) {
                        if ($valMenuVa != '') {
                            $exData = explode('|', $valMenuVa);
                            $mainDropDownIds[] = $exData[0];
                        }
                    }
                }
                asort($mainDropDownIds);
                $mainDropDownImplodedIds = implode(',', $mainDropDownIds);
            }
            // drop down menu title and value data end
            
            $orderData = Order::where($conditions)->first();
            
            if ($orderData != null) {
                $orderId = $orderData->id;
            } else {
                $newOrder = new Order;
                $newOrder->unique_order_id  = Helper::generateUniquesOrderId();
                $newOrder->session_id       = isset($sessionId) ? $sessionId : null;
                $newOrder->user_id          = $userId;
                $newOrder->payment_method   = '0';
                $newOrder->payment_status   = 'P';
                $newOrder->type             = 'C';
                $newOrder->save();
                $orderId = $newOrder->id;
            }

            // PRODUCT CLICKED START
            if ($productId != '') {
                $checkOrderDetail = OrderDetail::where([
                                                    'order_id'   => $orderId,
                                                    'product_id' => $productId
                                                ])->first();

                $updatedQuantity = $updatedTotalPrice = $dropDownSingleMultiplePrice = 0;
                
                // Then insert into order_details table
                if ($checkOrderDetail == null) {
                    // dd($menuValueIds);

                    // drop down menu title and value data start
                    if (strpos($menuValueIds,',') !== false) {
                        $allMenuValueIds = explode(',', $menuValueIds);
                        asort($allMenuValueIds);

                        foreach ($allMenuValueIds as $keyMenuValue => $valMenuValue) {
                            if ($valMenuValue != '') {
                                $explodedData   = explode('|', $valMenuValue);
                            
                               
                                $menuTitleId    = !empty($explodedData[1])?$explodedData[1]:0;     // drop down menu title id
                                $menuValueId    = !empty($explodedData[0])?$explodedData[0]:0;      // drop down menu value id

                                

                                $getProductMenuTitleData = ProductMenuTitle::where(['id' => $menuTitleId, 'product_id' => $productId])->first();
                                if ($getProductMenuTitleData) {
                                    $getProductMenuValueData = ProductMenuValue::where(['id' => $menuValueId, 'product_id' => $productId])->first();
                                  
                                    //print_r($getProductMenuTitleData->local[0]); die;

                                    $menuTitleValueIds[$menuTitleId][] = $menuValueId;


                                    $menuTitleValueDataEn[$menuTitleId]['menu_title']   = !empty($getProductMenuTitleData->local[0]->local_title)?$getProductMenuTitleData->local[0]->local_title:!empty($getProductMenuTitleData->local[0]->local_title);
                                    $menuTitleValueDataEn[$menuTitleId]['menu_value'][] = !empty($getProductMenuValueData->local[0]->local_title)?$getProductMenuValueData->local[0]->local_title:'';
                                    $menuTitleValueDataEn[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);

                                    $menuTitleValueDataDe[$menuTitleId]['menu_title']   = !empty($getProductMenuTitleData->local[1]->local_title)?$getProductMenuTitleData->local[1]->local_title:'';
                                    $menuTitleValueDataDe[$menuTitleId]['menu_value'][] = !empty($getProductMenuValueData->local[1]->local_title)?$getProductMenuValueData->local[1]->local_title:'';
                                    $menuTitleValueDataDe[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);

                                    $dropDownSingleMultiplePrice += $getProductMenuValueData->price;
                                }
                            }
                        }
                    }
                    // drop down menu title and value data end
                    
                    $productsDetails = Product::whereNull('deleted_at')->where(['id' => $productId,'status' => '1'])->first();

                    if ($productsDetails != null) {
                        $quantity = isset($request->quantity) ? $request->quantity : 1;
                        $newOrderDetail = new OrderDetail;
                        $newOrderDetail->order_id       = $orderId;
                        $newOrderDetail->product_id     = $productId;
                        $newOrderDetail->price          = $productsDetails->price + $dropDownSingleMultiplePrice;
                        $newOrderDetail->quantity       = $quantity;
                        $newOrderDetail->is_menu        = $isMenu;
                        $newOrderDetail->menu_key_values= (count($menuTitleValueIds) > 0) ? json_encode($menuTitleValueIds) : null;
                        $newOrderDetail->menu_option_ids= $mainDropDownImplodedIds;
                        
                        if ($attributeId == '') {
                            $newOrderDetail->unit_total_price   = ($productsDetails->price !== NULL) ? ($productsDetails->price + $dropDownSingleMultiplePrice) * $quantity : null;
                            $newOrderDetail->total_price        = $updatedTotalPrice = $newOrderDetail->unit_total_price;
                        }
                        $newOrderDetail->save();
                        
                        $OrderDetailId = $newOrderDetail->id;

                        if ($OrderDetailId) {
                            // Insertion into locals (for language)
                            if (count($productsDetails->local)) {
                                foreach ($productsDetails->local as $keyLocal => $valLocal) {
                                    $newOrderDetailLocal                    = new OrderDetailLocal;
                                    $newOrderDetailLocal->order_id          = $orderId;
                                    $newOrderDetailLocal->order_details_id  = $OrderDetailId;
                                    $newOrderDetailLocal->product_id        = $productId;
                                    $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                    $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                    $newOrderDetailLocal->local_description = isset($valLocal->local_description) ? $valLocal->local_description : null;
                                    
                                    // for category details
                                    if (count($productsDetails->productCategory->local) > 0) {
                                        if ($productsDetails->productCategory->local[$keyLocal]->lang_code == $valLocal->lang_code) {
                                            $newOrderDetailLocal->local_category_title = $productsDetails->productCategory->local[$keyLocal]->local_title;
                                        }
                                    }

                                    if ($valLocal->lang_code == 'EN') {
                                        $newOrderDetailLocal->local_drop_down_menu_title_value = count($menuTitleValueDataEn) ? json_encode($menuTitleValueDataEn) : null;
                                    } else {
                                        $newOrderDetailLocal->local_drop_down_menu_title_value = count($menuTitleValueDataDe) ? json_encode($menuTitleValueDataDe) : null;
                                    }

                                    $newOrderDetailLocal->save();
                                }
                            }

                            // Insertion into ingredients table start
                            $totalIngredientPrice = 0;
                            if ($showIngredients != '' && $allIngredients[0] != '') {
                                foreach ($allIngredients as $ingredient) {
                                    $ingredientDetails = OrderIngredient::where([
                                                                            'order_id'          => $orderId,
                                                                            'order_details_id'  => $OrderDetailId,
                                                                            'product_id'        => $productId,
                                                                            'ingredient_id'     => $ingredient
                                                                        ])
                                                                        ->first();
                                    if ($ingredientDetails == null) {
                                        $dataIngredient = Ingredient::where(['id' => $ingredient, 'status' => '1'])->whereNull('deleted_at')->first();
                                        $newOrderIngredient                     = new OrderIngredient;
                                        $newOrderIngredient->order_id           = $orderId;
                                        $newOrderIngredient->order_details_id   = $OrderDetailId;
                                        $newOrderIngredient->product_id         = $productId;
                                        $newOrderIngredient->ingredient_id      = $ingredient;
                                        $newOrderIngredient->quantity           = $quantity;
                                        $newOrderIngredient->price              = $dataIngredient->price;
                                        $newOrderIngredient->total_price        = ($dataIngredient->price) * $quantity;
                                        $totalIngredientPrice += $newOrderIngredient->total_price;
                                        if ($newOrderIngredient->save() ) {
                                            //Insertion into locals (for language)
                                            if (count($dataIngredient->local)) {
                                                foreach ($dataIngredient->local as $keyLocal => $valLocal) {
                                                    $newOrderIngredientLocal                        = new OrderIngredientLocal;
                                                    $newOrderIngredientLocal->order_id              = $orderId;
                                                    $newOrderIngredientLocal->order_details_id      = $OrderDetailId;
                                                    $newOrderIngredientLocal->product_id            = $productId;
                                                    $newOrderIngredientLocal->order_ingredient_id   = $newOrderIngredient->id;
                                                    $newOrderIngredientLocal->ingredient_id         = $ingredient;
                                                    $newOrderIngredientLocal->lang_code             = $valLocal->lang_code;
                                                    $newOrderIngredientLocal->local_ingredient_title= $valLocal->local_title;
                                                    $newOrderIngredientLocal->save();
                                                }
                                            }
                                        }
                                    }
                                }
                                // Update order details table
                                $newOrderDetail->has_ingredients = 'Y';
                                $newOrderDetail->save();
                            }
                            //Insertion into ingredients table end

                            //Insertion into attributes start
                            if ($attributeId != '') {
                                $dataProductAttribute = ProductAttribute::where(['id' => $attributeId, 'product_id' => $productId, 'status' => '1'])->whereNull('deleted_at')->first();
                                
                                // Update order details table
                                $newOrderDetail->has_attribute      = 'Y';
                                $newOrderDetail->attribute_id       = $attributeId;
                                $newOrderDetail->price              = $dataProductAttribute->price;
                                $newOrderDetail->unit_total_price   = $dataProductAttribute->price * $quantity;
                                $newOrderDetail->total_price        = $dataProductAttribute->price * $quantity;
                                $saveData = $newOrderDetail->save();

                                if ($saveData) {
                                    // Insertion into locals (for language)
                                    if (count($dataProductAttribute->local)) {
                                        foreach ($dataProductAttribute->local as $keyLocal => $valLocal) {
                                            $newOrderAttributeLocal                         = new OrderAttributeLocal;
                                            $newOrderAttributeLocal->order_id               = $orderId;
                                            $newOrderAttributeLocal->order_details_id       = $OrderDetailId;
                                            $newOrderAttributeLocal->product_id             = $productId;
                                            $newOrderAttributeLocal->attribute_id           = $attributeId;
                                            $newOrderAttributeLocal->lang_code              = $valLocal->lang_code;
                                            $newOrderAttributeLocal->local_attribute_title  = $valLocal->local_title;
                                            $newOrderAttributeLocal->save();
                                        }
                                    }
                                }
                            }
                            // Insertion into attributes table end
                            else {
                                if ($updatedTotalPrice != null) {
                                    $updatedTotalPrice += $totalIngredientPrice;
                                } else {
                                    $updatedTotalPrice = $totalIngredientPrice;
                                }
                                $newOrderDetail->total_price = $updatedTotalPrice;
                                $newOrderDetail->save();
                            }
                        }

                        $title      = trans('custom.success');
                        $message    = trans('custom.add_to_cart_successful');
                        $type       = 'success';
                    }
                }
                else    // update order_details table
                {
                    $updateOrderDetail = OrderDetail::where([
                                                        'order_id'      => $orderId,
                                                        'product_id'    => $productId
                                                    ])
                                                    ->first();
                    // dd($updateOrderDetail);
                    if ($updateOrderDetail != null) {
                        // Getting product details
                        $productsDetails = Product::whereNull('deleted_at')->where(['id' => $productId,'status' => '1'])->first();

                        // Drop down menu Title & Values start => Do not change here
                        if ($updateOrderDetail->is_menu == 'Y') {

                            $updateOrderDetail = OrderDetail::where(['order_id' => $orderId, 'product_id' => $productId, 'menu_option_ids' => $mainDropDownImplodedIds])->first();
                            // dd($updateOrderDetail);

                            // If existing drop down menu details NOT null && menu option ids match with existing one then update
                            if ($updateOrderDetail != null) {
                                $explodedExistingMenuTitleValue = json_decode($updateOrderDetail->menu_key_values, true);

                                $explodedMenuTitleValueLangEn = json_decode($updateOrderDetail->orderDetailLocals[0]->local_drop_down_menu_title_value, true);
                                $explodedMenuTitleValueLangDe = json_decode($updateOrderDetail->orderDetailLocals[1]->local_drop_down_menu_title_value, true);                                

                                if (strpos($menuValueIds,',') !== false) {
                                    $allMenuValueIds = explode(',', $menuValueIds);
                                    asort($allMenuValueIds);

                                    foreach ($allMenuValueIds as $keyMenuValue => $valMenuValue) {
                                        if ($valMenuValue != '') {
                                            $explodedData   = explode('|', $valMenuValue);
            
                                            $menuTitleId    = $explodedData[1];     // drop down menu title id
                                            $menuValueId    = $explodedData[0];     // drop down menu value id

                                            if (array_key_exists($menuTitleId, $explodedExistingMenuTitleValue)) {
                                                // dd($explodedExistingMenuTitleValue[$menuTitleId]);

                                                // if drop down value not exist
                                                $getProductMenuValueData = ProductMenuValue::where(['id' => $menuValueId, 'product_id' => $productId])->first();
                                                if (!in_array($menuValueId, $explodedExistingMenuTitleValue[$menuTitleId])) {
                                                    $getProductMenuTitleData = ProductMenuTitle::where(['id' => $menuTitleId, 'product_id' => $productId])->first();
                                                    if ($getProductMenuTitleData) {
                                                        $menuTitleValueIds[$menuTitleId][count($explodedExistingMenuTitleValue[$menuTitleId])] = $menuValueId;
                                                        $explodedMenuTitleValueLangEn[$menuTitleId]['menu_value'][] = $getProductMenuValueData->local[0]->local_title;
                                                        $explodedMenuTitleValueLangEn[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);
                                                                            
                                                        $menuTitleValueDataDe[$menuTitleId]['menu_title'] = $getProductMenuTitleData->local[1]->local_title;
                                                        $explodedMenuTitleValueLangDe[$menuTitleId]['menu_value'][] = $getProductMenuValueData->local[1]->local_title;
                                                        $explodedMenuTitleValueLangDe[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);

                                                        $dropDownSingleMultiplePrice += $getProductMenuValueData->price;
                                                    }
                                                } else {
                                                    $dropDownSingleMultiplePrice += $getProductMenuValueData->price;
                                                }
                                            }
                                            else {
                                                $getProductMenuTitleData = ProductMenuTitle::where(['id' => $menuTitleId, 'product_id' => $productId])->first();
                                                if ($getProductMenuTitleData) {
                                                    $getProductMenuValueData = ProductMenuValue::where(['id' => $menuValueId, 'product_id' => $productId])->first();
                
                                                    $menuTitleValueIds[$menuTitleId][0] = $menuValueId;
                
                                                    $menuTitleValueDataEn[$menuTitleId]['menu_title'] = $getProductMenuTitleData->local[0]->local_title;
                                                    $menuTitleValueDataEn[$menuTitleId]['menu_value'][0] = $getProductMenuValueData->local[0]->local_title;
                                                    $menuTitleValueDataEn[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);
                
                                                    $menuTitleValueDataDe[$menuTitleId]['menu_title'] = $getProductMenuTitleData->local[1]->local_title;
                                                    $menuTitleValueDataDe[$menuTitleId]['menu_value'][0] = $getProductMenuValueData->local[1]->local_title;
                                                    $menuTitleValueDataDe[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);

                                                    $dropDownSingleMultiplePrice += $getProductMenuValueData->price;
                                                }
                                            }
                                        }
                                    }

                                    $menuTitleValueIds      = array_replace_recursive($explodedExistingMenuTitleValue, $menuTitleValueIds);
                                    ksort($menuTitleValueIds);
                                    $menuTitleValueDataEn   = array_replace_recursive($explodedMenuTitleValueLangEn, $menuTitleValueDataEn);
                                    ksort($menuTitleValueDataEn);
                                    $menuTitleValueDataDe   = array_replace_recursive($explodedMenuTitleValueLangDe, $menuTitleValueDataDe);
                                    ksort($menuTitleValueDataDe);

                                    $updateOrderDetail->menu_key_values = json_encode($menuTitleValueIds);
                                }
                                $OrderDetailId = $updateOrderDetail->id;
                                // $quantity = 1;
                                $quantity = isset($request->quantity) ? $request->quantity : 1;
                            }
                            // If existing drop down menu details NOT null && menu option ids match with existing one then update end
                            // else insert new row
                            else {
                                // $quantity = 1;
                                $quantity = isset($request->quantity) ? $request->quantity : 1;
                                $newOrdDtls = new OrderDetail;
                                $newOrdDtls->order_id       = $orderId;
                                $newOrdDtls->product_id     = $productId;
                                // $newOrdDtls->quantity       = 0;
                                $newOrdDtls->quantity       = $quantity;
                                $newOrdDtls->is_menu        = $isMenu;                                

                                // drop down menu title and value data start
                                if (strpos($menuValueIds,',') !== false) {
                                    $allMenuValueIds = explode(',', $menuValueIds);
                                    asort($allMenuValueIds);

                                    foreach ($allMenuValueIds as $keyMenuValue => $valMenuValue) {
                                        if ($valMenuValue != '') {
                                            $explodedData   = explode('|', $valMenuValue);

                                            $menuTitleId    = $explodedData[1];     // drop down menu title id
                                            $menuValueId    = $explodedData[0];     // drop down menu value id

                                            $getProductMenuTitleData = ProductMenuTitle::where(['id' => $menuTitleId, 'product_id' => $productId])->first();
                                            if ($getProductMenuTitleData) {
                                                $getProductMenuValueData = ProductMenuValue::where(['id' => $menuValueId, 'product_id' => $productId])->first();

                                                $menuTitleValueIds[$menuTitleId][]      = $menuValueId;

                                                $menuTitleValueDataEn[$menuTitleId]['menu_title'] = $getProductMenuTitleData->local[0]->local_title;
                                                $menuTitleValueDataEn[$menuTitleId]['menu_value'][] = $getProductMenuValueData->local[0]->local_title;
                                                $menuTitleValueDataEn[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);

                                                $menuTitleValueDataDe[$menuTitleId]['menu_title'] = $getProductMenuTitleData->local[1]->local_title;
                                                $menuTitleValueDataDe[$menuTitleId]['menu_value'][] = $getProductMenuValueData->local[1]->local_title;
                                                $menuTitleValueDataDe[$menuTitleId]['menu_price'][] = Helper::formatToTwoDecimalPlaces($getProductMenuValueData->price);

                                                $dropDownSingleMultiplePrice += $getProductMenuValueData->price;
                                            }
                                        }
                                    }

                                    $newOrdDtls->menu_option_ids= $mainDropDownImplodedIds;
                                    $newOrdDtls->menu_key_values= json_encode($menuTitleValueIds);
                                }
                                // drop down menu title and value data end

                                $newOrdDtls->price              = $productsDetails->price + $dropDownSingleMultiplePrice;
                                $newOrdDtls->unit_total_price   = $productsDetails->price + $dropDownSingleMultiplePrice;
                                $newOrdDtls->total_price        = $productsDetails->price + $dropDownSingleMultiplePrice;
                                $newOrdDtls->save();

                                // Insertion into locals (for product language)
                                if (count($productsDetails->local)) {
                                    foreach ($productsDetails->local as $keyLocal => $valLocal) {
                                        $newOrdDtlLcl                    = new OrderDetailLocal;
                                        $newOrdDtlLcl->order_id          = $orderId;
                                        $newOrdDtlLcl->order_details_id  = $newOrdDtls->id;
                                        $newOrdDtlLcl->product_id        = $productId;
                                        $newOrdDtlLcl->lang_code         = $valLocal->lang_code;
                                        $newOrdDtlLcl->local_title       = $valLocal->local_title;
                                        $newOrdDtlLcl->local_description = isset($valLocal->local_description) ? $valLocal->local_description : null;
                                        
                                        // for category details
                                        if (count($productsDetails->productCategory->local) > 0) {
                                            if ($productsDetails->productCategory->local[$keyLocal]->lang_code == $valLocal->lang_code) {
                                                $newOrdDtlLcl->local_category_title = $productsDetails->productCategory->local[$keyLocal]->local_title;
                                            }
                                        }

                                        if ($valLocal->lang_code == 'EN') {
                                            $newOrdDtlLcl->local_drop_down_menu_title_value = count($menuTitleValueDataEn) ? json_encode($menuTitleValueDataEn) : null;
                                        } else {
                                            $newOrdDtlLcl->local_drop_down_menu_title_value = count($menuTitleValueDataDe) ? json_encode($menuTitleValueDataDe) : null;
                                        }
                                        $newOrdDtlLcl->save();
                                    }
                                }
                                $OrderDetailId = $newOrdDtls->id;
                                // $quantity = 1;
                                $quantity = isset($request->quantity) ? $request->quantity : 1;

                                $updateOrderDetail = $newOrdDtls;
                            }
                        }
                        // Drop down menu Title & Values end => Do not change here
                        // dd($updateOrderDetail);
                        if ($updateOrderDetail->is_menu != 'Y') {
                            $OrderDetailId = $updateOrderDetail->id;
                            // $quantity = 1;
                            $quantity = isset($request->quantity) ? $request->quantity : 1;
                        }

                        // if attribute exist then don't calculate unit price & total price here
                        if ($attributeId == '') {
                            if ($updateOrderDetail->is_menu != 'Y') {
                                $updatedQuantity = ($updateOrderDetail->quantity + $quantity);

                                $updateOrderDetail->quantity        = $updatedQuantity;
                                $updateOrderDetail->price           = $productsDetails->price;

                                $updateOrderDetail->unit_total_price= ($productsDetails->price !== NULL) ? $productsDetails->price * $updatedQuantity : null;
                                $updatedTotalPrice = $updateOrderDetail->unit_total_price;
                                $updateOrderDetail->save();
                            } else {
                                // $quantity = 1;
                                $quantity = isset($request->quantity) ? $request->quantity : 1;
                                if ($newOrdDtls != '') {    // same menu ids not exist then quantity will be 1
                                    $updatedQuantity = $quantity;

                                    $updateOrderDetail->quantity        = $updatedQuantity;
                                    $updateOrderDetail->price           = $productsDetails->price + $dropDownSingleMultiplePrice;

                                    $updateOrderDetail->unit_total_price= ($productsDetails->price !== NULL) ? ($productsDetails->price + $dropDownSingleMultiplePrice) * $updatedQuantity : null;
                                    $updatedTotalPrice = $updateOrderDetail->unit_total_price;
                                    $updateOrderDetail->save();
                                } else {    // Quantity will increase
                                    $updatedQuantity = ($updateOrderDetail->quantity + $quantity);

                                    $updateOrderDetail->quantity        = $updatedQuantity;
                                    $updateOrderDetail->price           = $productsDetails->price + $dropDownSingleMultiplePrice;

                                    $updateOrderDetail->unit_total_price= ($productsDetails->price !== NULL) ? ($productsDetails->price + $dropDownSingleMultiplePrice) * $updatedQuantity : null;
                                    $updatedTotalPrice = $updateOrderDetail->unit_total_price;
                                    $updateOrderDetail->save();
                                }
                            }
                            
                            // Deleting & Insertion into locals (for language)
                            OrderDetailLocal::where(['order_id' => $orderId, 'order_details_id' => $OrderDetailId, 'product_id' => $productId])->delete();
                            if (count($productsDetails->local)) {
                                foreach ($productsDetails->local as $keyLocal => $valLocal) {
                                    $newOrderDetailLocal                    = new OrderDetailLocal;
                                    $newOrderDetailLocal->order_id          = $orderId;
                                    $newOrderDetailLocal->order_details_id  = $OrderDetailId;
                                    $newOrderDetailLocal->product_id        = $productId;
                                    $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                    $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                    $newOrderDetailLocal->local_description = $valLocal->local_description;
                                    
                                    // for category details
                                    if (count($productsDetails->productCategory->local) > 0) {
                                        if ($productsDetails->productCategory->local[$keyLocal]->lang_code == $valLocal->lang_code) {
                                            $newOrderDetailLocal->local_category_title = $productsDetails->productCategory->local[$keyLocal]->local_title;
                                        }
                                    }

                                    // for drop down menu title values
                                    if ($valLocal->lang_code == 'EN') {
                                        $newOrderDetailLocal->local_drop_down_menu_title_value = count($menuTitleValueDataEn) ? json_encode($menuTitleValueDataEn) : null;
                                    } else {
                                        $newOrderDetailLocal->local_drop_down_menu_title_value = count($menuTitleValueDataDe) ? json_encode($menuTitleValueDataDe) : null;
                                    }

                                    $newOrderDetailLocal->save();
                                }
                            }
                        }

                        // Ingredients table start
                        $totalIngredientPrice = 0;
                        // existing product ingredients details start
                        $ingredeDetails = OrderIngredient::where([
                                                                'order_id'          => $orderId,
                                                                'order_details_id'  => $OrderDetailId,
                                                                'product_id'        => $productId,
                                                            ])
                                                            ->get();
                        // existing product ingredients Total Price Calculation start
                        if ($ingredeDetails->count() > 0) {
                            foreach ($ingredeDetails as $ingntDtls) {
                                $totalIngredientPrice += $ingntDtls->total_price;
                            }
                        }
                        // existing product ingredients Total Price Calculation end
                        // existing product ingredients details end
                        
                        if ($showIngredients != '' && $allIngredients[0] != '') {
                            foreach ($allIngredients as $ingredient) {
                                $ingredientDetails = OrderIngredient::where([
                                                                        'order_id'          => $orderId,
                                                                        'order_details_id'  => $OrderDetailId,
                                                                        'product_id'        => $productId,
                                                                        'ingredient_id'     => $ingredient
                                                                    ])
                                                                    ->first();
                                if ($ingredientDetails == null) {
                                    $dataIngredient = Ingredient::where(['id' => $ingredient, 'status' => '1'])->whereNull('deleted_at')->first();
                                    $newOrderIngredient                     = new OrderIngredient;
                                    $newOrderIngredient->order_id           = $orderId;
                                    $newOrderIngredient->order_details_id   = $OrderDetailId;
                                    $newOrderIngredient->product_id         = $productId;
                                    $newOrderIngredient->ingredient_id      = $ingredient;
                                    $newOrderIngredient->quantity           = $quantity;
                                    $newOrderIngredient->price              = $dataIngredient->price;
                                    $newOrderIngredient->total_price        = ($dataIngredient->price) * $quantity;
                                    $totalIngredientPrice += $newOrderIngredient->total_price;
                                    if ($newOrderIngredient->save() ) {
                                        //Insertion into locals (for language)
                                        if (count($dataIngredient->local)) {
                                            foreach ($dataIngredient->local as $keyLocal => $valLocal) {
                                                $newOrderIngredientLocal                        = new OrderIngredientLocal;
                                                $newOrderIngredientLocal->order_id              = $orderId;
                                                $newOrderIngredientLocal->order_details_id      = $OrderDetailId;
                                                $newOrderIngredientLocal->product_id            = $productId;
                                                $newOrderIngredientLocal->order_ingredient_id   = $newOrderIngredient->id;
                                                $newOrderIngredientLocal->ingredient_id         = $ingredient;
                                                $newOrderIngredientLocal->lang_code             = $valLocal->lang_code;
                                                $newOrderIngredientLocal->local_ingredient_title= $valLocal->local_title;
                                                $newOrderIngredientLocal->save();
                                            }
                                        }
                                    }
                                } else {
                                    $dataIngredient = Ingredient::where(['id' => $ingredient, 'status' => '1'])->whereNull('deleted_at')->first();
                                    
                                    $ingredientDetails->quantity    = $ingredientDetails->quantity + $quantity;
                                    $ingredientDetails->price       = $dataIngredient->price;
                                    $ingredientDetails->total_price = $dataIngredient->price * $ingredientDetails->quantity;
                                    $totalIngredientPrice += $dataIngredient->price;
                                    $ingredientDetails->save();

                                    // Deleting & Insertion into locals (for langauage)
                                    OrderIngredientLocal::where(['order_id' => $orderId, 'order_details_id' => $OrderDetailId, 'product_id' => $productId, 'order_ingredient_id' => $ingredientDetails->id])->delete();
                                    if (count($dataIngredient->local)) {
                                        foreach ($dataIngredient->local as $keyLocal => $valLocal) {
                                            $updateOrderIngredientLocal                        = new OrderIngredientLocal;
                                            $updateOrderIngredientLocal->order_id              = $orderId;
                                            $updateOrderIngredientLocal->order_details_id      = $OrderDetailId;
                                            $updateOrderIngredientLocal->product_id            = $productId;
                                            $updateOrderIngredientLocal->order_ingredient_id   = $ingredientDetails->id;
                                            $updateOrderIngredientLocal->ingredient_id         = $ingredient;
                                            $updateOrderIngredientLocal->lang_code             = $valLocal->lang_code;
                                            $updateOrderIngredientLocal->local_ingredient_title= $valLocal->local_title;
                                            $updateOrderIngredientLocal->save();
                                        }
                                    }
                                }
                            }
                            // Update order details table
                            $updateOrderDetail->has_ingredients = 'Y';
                            $updateOrderDetail->save();
                        }
                        // Ingredients table end

                        // Attributes table start
                        if ($attributeId != '') {
                            $orderAttributeDetails = OrderDetail::where([
                                                                    'order_id'          => $orderId,
                                                                    'product_id'        => $productId,
                                                                    'attribute_id'      => $attributeId,
                                                                ])
                                                                ->first();
                            // dd($orderAttributeDetails);
                            $dataProductAttribute = ProductAttribute::where(['id' => $attributeId, 'product_id' => $productId, 'status' => '1'])->whereNull('deleted_at')->first();

                            // product id + attribute id exist the Update details
                            if ($orderAttributeDetails != null) {
                                // Deleting & Insertion into locals (for langauage)
                                OrderDetailLocal::where(['order_id' => $orderId, 'order_details_id' => $orderAttributeDetails->id, 'product_id' => $productId])->delete();
                                if (count($productsDetails->local)) {
                                    foreach ($productsDetails->local as $keyLocal => $valLocal) {
                                        $newOrderDetailLocal                    = new OrderDetailLocal;
                                        $newOrderDetailLocal->order_id          = $orderId;
                                        $newOrderDetailLocal->order_details_id  = $orderAttributeDetails->id;
                                        $newOrderDetailLocal->product_id        = $productId;
                                        $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                        $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                        $newOrderDetailLocal->local_description = $valLocal->local_description;
                                        
                                        //for category details
                                        if (count($productsDetails->productCategory->local) > 0) {
                                            if ($productsDetails->productCategory->local[$keyLocal]->lang_code == $valLocal->lang_code) {
                                                $newOrderDetailLocal->local_category_title = $productsDetails->productCategory->local[$keyLocal]->local_title;
                                            }
                                        }
                                        $newOrderDetailLocal->save();
                                    }
                                }

                                $orderAttributeDetails->quantity         = $orderAttributeDetails->quantity + $quantity;
                                $orderAttributeDetails->price            = $dataProductAttribute->price;
                                $orderAttributeDetails->unit_total_price = $dataProductAttribute->price * $orderAttributeDetails->quantity;
                                $orderAttributeDetails->total_price      = $dataProductAttribute->price * $orderAttributeDetails->quantity;
                                $update = $orderAttributeDetails->save();

                                if ($update) {
                                    // Deleting & Insertion into locals (for langauage)
                                    OrderAttributeLocal::where(['order_id' => $orderId, 'order_details_id' => $orderAttributeDetails->id, 'product_id' => $productId, 'attribute_id' => $attributeId])->delete();
                                    if (count($dataProductAttribute->local)) {
                                        foreach ($dataProductAttribute->local as $keyLocal => $valLocal) {
                                            $updateOrderAttributeLocal                         = new OrderAttributeLocal;
                                            $updateOrderAttributeLocal->order_id               = $orderId;
                                            $updateOrderAttributeLocal->order_details_id       = $orderAttributeDetails->id;
                                            $updateOrderAttributeLocal->product_id             = $productId;
                                            $updateOrderAttributeLocal->attribute_id           = $attributeId;
                                            $updateOrderAttributeLocal->lang_code              = $valLocal->lang_code;
                                            $updateOrderAttributeLocal->local_attribute_title  = $valLocal->local_title;
                                            $updateOrderAttributeLocal->save();
                                        }
                                    }
                                }
                            }
                            else {
                                $newOrderDetail = new OrderDetail;
                                $newOrderDetail->order_id           = $orderId;
                                $newOrderDetail->product_id         = $productId;
                                $newOrderDetail->has_attribute      = 'Y';
                                $newOrderDetail->attribute_id       = $attributeId;
                                $newOrderDetail->quantity           = $quantity;
                                $newOrderDetail->price              = $dataProductAttribute->price;
                                $newOrderDetail->unit_total_price   = $dataProductAttribute->price * $quantity;
                                $newOrderDetail->total_price        = $dataProductAttribute->price * $quantity;
                                $saveData = $newOrderDetail->save();

                                if ($saveData) {
                                    // Insertion into locals (for langauage)
                                    if (count($productsDetails->local)) {
                                        foreach ($productsDetails->local as $keyLocal => $valLocal) {
                                            $newOrderDetailLocal                    = new OrderDetailLocal;
                                            $newOrderDetailLocal->order_id          = $orderId;
                                            $newOrderDetailLocal->order_details_id  = $newOrderDetail->id;
                                            $newOrderDetailLocal->product_id        = $productId;
                                            $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                            $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                            $newOrderDetailLocal->local_description = $valLocal->local_description;
                                            
                                            //for category details
                                            if (count($productsDetails->productCategory->local) > 0) {
                                                if ($productsDetails->productCategory->local[$keyLocal]->lang_code == $valLocal->lang_code) {
                                                    $newOrderDetailLocal->local_category_title = $productsDetails->productCategory->local[$keyLocal]->local_title;
                                                }
                                            }
                                            $newOrderDetailLocal->save();
                                        }
                                    }

                                    //Insertion into locals (for langauage)
                                    if (count($dataProductAttribute->local)) {
                                        foreach ($dataProductAttribute->local as $keyLocal => $valLocal) {
                                            $newOrderAttributeLocal                         = new OrderAttributeLocal;
                                            $newOrderAttributeLocal->order_id               = $orderId;
                                            $newOrderAttributeLocal->order_details_id       = $newOrderDetail->id;
                                            $newOrderAttributeLocal->product_id             = $productId;
                                            $newOrderAttributeLocal->attribute_id           = $attributeId;
                                            $newOrderAttributeLocal->lang_code              = $valLocal->lang_code;
                                            $newOrderAttributeLocal->local_attribute_title  = $valLocal->local_title;
                                            $newOrderAttributeLocal->save();
                                        }
                                    }
                                }
                            }
                        }
                        // Attributes table end

                        else {
                            if ($updatedTotalPrice != null) {
                                $updatedTotalPrice += $totalIngredientPrice;
                            } else {
                                $updatedTotalPrice = $totalIngredientPrice;
                            }
                            $updateOrderDetail->total_price   = $updatedTotalPrice;
                            $updateOrderDetail->save();
                        }

                        $title      = trans('custom.success');
                        $message    = trans('custom.update_cart_successful');
                        $type       = 'success';
                    }
                }
            }
            // PRODUCT CLICKED END

            // DRINKS CLICKED START
            if ($drinkId != '') {
                // checking at least one product exist or not
                $checkExistingAtLeastOneProduct = OrderDetail::where(['order_id' => $orderId])->where('product_id', '!=', null)->count();
                if ($checkExistingAtLeastOneProduct > 0) {
                    $checkOrderDetail = OrderDetail::where([
                        'order_id' => $orderId,
                        'drink_id' => $drinkId
                    ])->first();

                    if ($checkOrderDetail == null) { // then insert into order_details table
                        $drinkDetails = Drink::whereNull('deleted_at')->where(['id' => $drinkId, 'status' => '1'])->first();

                        if ($drinkDetails != null) {
                            $quantity = 1;
                            $newOrderDetail = new OrderDetail;
                            $newOrderDetail->order_id           = $orderId;
                            $newOrderDetail->drink_id           = $drinkId;
                            $newOrderDetail->price              = $drinkDetails->price;
                            $newOrderDetail->quantity           = $quantity;
                            $newOrderDetail->unit_total_price   = ($drinkDetails->price != null) ? ($drinkDetails->price) * $quantity : null;
                            $newOrderDetail->total_price        = $newOrderDetail->unit_total_price;
                            $newOrderDetail->save();
                            $OrderDetailId = $newOrderDetail->id;
                    
                            if ($OrderDetailId) {
                                //Insertion into locals (for langauage)
                                if (count($drinkDetails->local)) {
                                    foreach ($drinkDetails->local as $keyLocal => $valLocal) {
                                        $newOrderDetailLocal                    = new OrderDetailLocal;
                                        $newOrderDetailLocal->order_id          = $orderId;
                                        $newOrderDetailLocal->order_details_id  = $OrderDetailId;
                                        $newOrderDetailLocal->drink_id          = $drinkId;
                                        $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                        $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                        $newOrderDetailLocal->local_description = isset($valLocal->local_description) ? $valLocal->local_description : null;
                                        $newOrderDetailLocal->save();
                                    }
                                }
                            }
                            
                            $title                  = trans('custom.success');
                            $message                = trans('custom.add_to_cart_successful');
                            $type                   = 'success';
                        }
                    }
                    else    //update order_details table
                    {
                        $updateOrderDetail = OrderDetail::where([
                                                                'order_id'  => $orderId,
                                                                'drink_id'  => $drinkId
                                                            ])
                                                            ->first();
                        if ($updateOrderDetail != null) {
                            $quantity = 1;
                            $updatedQuantity = ($updateOrderDetail->quantity + $quantity);

                            // Getting special menu details
                            $drinkDetails = Drink::whereNull('deleted_at')->where(['id' => $drinkId,'status' => '1'])->first();

                            $updateOrderDetail->quantity        = $updatedQuantity;
                            $updateOrderDetail->price           = $drinkDetails->price;
                            $updateOrderDetail->unit_total_price= ($drinkDetails->price != null) ? $drinkDetails->price * $updatedQuantity : null;
                            $updateOrderDetail->total_price     = ($drinkDetails->price != null) ? $drinkDetails->price * $updatedQuantity : null;
                            $updateOrderDetail->save();

                            $OrderDetailId = $updateOrderDetail->id;

                            // Deleting & Insertion into locals (for langauage)
                            OrderDetailLocal::where(['order_id' => $orderId, 'order_details_id' => $OrderDetailId, 'drink_id' => $drinkId])->delete();
                            if (count($drinkDetails->local)) {
                                foreach ($drinkDetails->local as $keyLocal => $valLocal) {
                                    $newOrderDetailLocal                    = new OrderDetailLocal;
                                    $newOrderDetailLocal->order_id          = $orderId;
                                    $newOrderDetailLocal->order_details_id  = $OrderDetailId;
                                    $newOrderDetailLocal->drink_id          = $drinkId;
                                    $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                    $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                    $newOrderDetailLocal->local_description = isset($valLocal->local_description) ? $valLocal->local_description : null;
                                    $newOrderDetailLocal->save();
                                }
                            }

                            $title      = trans('custom.success');
                            $message    = trans('custom.update_cart_successful');
                            $type       = 'success';
                        }
                    }
                } else {
                    OrderDetail::where(['order_id' => $orderId])->whereNull('product_id')->where('drink_id', '!=', null)->delete();

                    $title      = trans('custom.success');
                    $message    = trans('custom.minimum_product_exist');
                    $type       = 'success';
                }
            }
            // DRINKS CLICKED END

            // SPECIAL MENU CLICKED START
            if ($specialId != '') {
                $checkOrderDetail = OrderDetail::where([
                    'order_id'          => $orderId,
                    'special_menu_id'   => $specialId
                ])->first();

                if ($checkOrderDetail == null) { // then insert into order_details table
                    $specialMenuDetails = SpecialMenu::whereNull('deleted_at')->where(['id' => $specialId,'status' => '1'])->first();

                    if ($specialMenuDetails != null) {
                        $quantity = 1;
                        $newOrderDetail = new OrderDetail;
                        $newOrderDetail->order_id           = $orderId;
                        $newOrderDetail->special_menu_id    = $specialId;
                        $newOrderDetail->price              = $specialMenuDetails->price;
                        $newOrderDetail->quantity           = $quantity;
                        $newOrderDetail->unit_total_price   = ($specialMenuDetails->price != null) ? ($specialMenuDetails->price) * $quantity : null;
                        $newOrderDetail->total_price        = $newOrderDetail->unit_total_price;
                        $newOrderDetail->save();
                        $OrderDetailId = $newOrderDetail->id;
                
                        if ($OrderDetailId) {
                            //Insertion into locals (for langauage)
                            if (count($specialMenuDetails->local)) {
                                foreach ($specialMenuDetails->local as $keyLocal => $valLocal) {
                                    $newOrderDetailLocal                    = new OrderDetailLocal;
                                    $newOrderDetailLocal->order_id          = $orderId;
                                    $newOrderDetailLocal->order_details_id  = $OrderDetailId;
                                    $newOrderDetailLocal->special_menu_id   = $specialId;
                                    $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                    $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                    $newOrderDetailLocal->local_description = isset($valLocal->local_description) ? $valLocal->local_description : null;
                                    $newOrderDetailLocal->save();
                                }
                            }
                        }
                        
                        $title      = trans('custom.success');
                        $message    = trans('custom.add_to_cart_successful');
                        $type       = 'success';
                    }
                }
                else    //update order_details table
                {
                    $updateOrderDetail = OrderDetail::where([
                                                            'order_id'          => $orderId,
                                                            'special_menu_id'   => $specialId
                                                        ])
                                                        ->first();
                    if ($updateOrderDetail != null) {
                        $quantity = 1;
                        $updatedQuantity = ($updateOrderDetail->quantity + $quantity);

                        // Getting special menu details
                        $specialMenuDetails = SpecialMenu::whereNull('deleted_at')->where(['id' => $specialId,'status' => '1'])->first();

                        $updateOrderDetail->quantity        = $updatedQuantity;
                        $updateOrderDetail->price           = $specialMenuDetails->price;
                        $updateOrderDetail->unit_total_price= ($specialMenuDetails->price != null) ? $specialMenuDetails->price * $updatedQuantity : null;
                        $updateOrderDetail->total_price     = ($specialMenuDetails->price != null) ? $specialMenuDetails->price * $updatedQuantity : null;
                        $updateOrderDetail->save();

                        $OrderDetailId = $updateOrderDetail->id;

                        // Deleting & Insertion into locals (for langauage)
                        OrderDetailLocal::where(['order_id' => $orderId, 'order_details_id' => $OrderDetailId, 'special_menu_id' => $specialId])->delete();
                        if (count($specialMenuDetails->local)) {
                            foreach ($specialMenuDetails->local as $keyLocal => $valLocal) {
                                $newOrderDetailLocal                    = new OrderDetailLocal;
                                $newOrderDetailLocal->order_id          = $orderId;
                                $newOrderDetailLocal->order_details_id  = $OrderDetailId;
                                $newOrderDetailLocal->special_menu_id   = $specialId;
                                $newOrderDetailLocal->lang_code         = $valLocal->lang_code;
                                $newOrderDetailLocal->local_title       = $valLocal->local_title;
                                $newOrderDetailLocal->local_description = isset($valLocal->local_description) ? $valLocal->local_description : null;
                                $newOrderDetailLocal->save();
                            }
                        }

                        $title      = trans('custom.success');
                        $message    = trans('custom.update_cart_successful');
                        $type       = 'success';
                    }
                }
            }
            // SPECIAL MENU CLICKED END
        }

        return json_encode([
            'title'     => $title,
            'message'   => $message,
            'type'      => $type,
        ]);
    }

    /*****************************************************/
    # Function name : getCartDetails
    # Params        : Request $request
    /*****************************************************/
    public function getCartDetails(Request $request)
    {
        $getCartData = Helper::getCartItemDetails();
        $siteSettingsData = Helper::getSiteSettings();

        $minOrderMessageStatus = 1; $remainingToAvoidMinimumOrder = 0;
        if ($getCartData['totalCartPrice'] >= Cookie::get('minimum_order_amount')) {
            $minOrderMessageStatus = 0;            
        } else {
            if ((Cookie::get('minimum_order_amount') - $getCartData['totalCartPrice']) > 0) {
                $remainingToAvoidMinimumOrder = Cookie::get('minimum_order_amount') - $getCartData['totalCartPrice'];
            }

            if (count($getCartData['itemDetails']) == 0) {
                $minOrderMessageStatus = 2;
            }
        }
        
        $returnCartDetails = view('site.cart_details')->with(['cartDetails' => $getCartData])->render();

       
        return response()->json(array(
                                    'success'                       => true,
                                    'productExist'                  => $getCartData['productExist'],
                                    'minOrderMessageStatus'         => $minOrderMessageStatus,
                                    'remainingToAvoidMinimumOrder'  => Helper::formatToTwoDecimalPlaces($remainingToAvoidMinimumOrder),
                                    'totalCartPrice'                => Helper::formatToTwoDecimalPlaces($getCartData['totalCartPrice']),
                                    'html'                          => $returnCartDetails
                                ));
    }

    /*****************************************************/
    # Function name : clearCart
    # Params        : Request $request
    /*****************************************************/
    public function clearCart(Request $request)
    {
        $currentLang = App::getLocale();
        $lang = strtoupper($currentLang);
        $userData = Auth::user();
        
        $title      = trans('custom.error');
        $message    = trans('custom.please_try_again');
        $type       = 'error';
        
        if ($request->isMethod('POST')) {
            $sessionId = Session::get('cartSessionId');

            $conditions = [];
            if (Auth::user()) {
                $userId = Auth::user()->id;
                $conditions = ['user_id' => $userId, 'type' => 'C'];
            } else {
                $userId = null;
                $conditions = ['session_id' => $sessionId, 'type' => 'C'];
            }

            $orderData = Order::where($conditions)->first();
            if ($orderData != null) {
                $orderId = $orderData->id;

                OrderIngredientLocal::where(['order_id' => $orderId])->delete();
                OrderIngredient::where(['order_id' => $orderId])->delete();
                OrderAttributeLocal::where(['order_id' => $orderId])->delete();
                OrderDetailLocal::where(['order_id' => $orderId])->delete();
                OrderDetail::where(['order_id' => $orderId])->delete();
                $orderData->delete();

                $title      = trans('custom.success');
                $message    = trans('custom.cart_clear_successful');
                $type       = 'success';
            }
            $sessionId = Session::put('cartSessionId', '');
        }

        return json_encode([
            'title'     => $title,
            'message'   => $message,
            'type'      => $type,
        ]);
    }

    /*****************************************************/
    # Function name : updateCartItem
    # Params        : Request $request
    /*****************************************************/
    public function updateCartItem(Request $request)
    {
        $currentLang = App::getLocale();
        $lang = strtoupper($currentLang);
        $userData = Auth::user();
        
        $title      = trans('custom.error');
        $message    = trans('custom.please_try_again');
        $type       = 'error';
        
        if ($request->isMethod('POST')) {
            $orderId        = isset($request->orderId) ? Helper::customEncryptionDecryption($request->orderId, 'decrypt') : 0;
            $orderDetailsId = isset($request->orderDetailsId) ? Helper::customEncryptionDecryption($request->orderDetailsId, 'decrypt') : 0;
            $cartStatus     = isset($request->cartStatus) ? Helper::customEncryptionDecryption($request->cartStatus, 'decrypt') : 'increase';
            $quantity       = 1;
            $productTotalPrice = $updatedQuantity = $updatedTotalPrice = $totalIngredientPrice = 0;

            if ($orderId != 0 && $orderDetailsId != 0) {
                $orderDetail = OrderDetail::where(['id' => $orderDetailsId, 'order_id' => $orderId])->first();

                // Increase cart item
                if ($cartStatus == 'increase') {                    
                    if ($orderDetail != null) {
                        // Product
                        if ($orderDetail->product_id != null) {
                            // Only product start
                            if ($orderDetail->has_ingredients == 'N') {
                                $orderDetail->quantity          = $orderDetail->quantity + $quantity;
                                $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                                $orderDetail->total_price       = $orderDetail->price * $orderDetail->quantity;
                                $orderDetail->save();
                            }
                            // Only product end
                            // for Product + ingredients
                            else {
                                $orderDetail->quantity          = $orderDetail->quantity + $quantity;
                                $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                                
                                $productTotalPrice = $orderDetail->price * $orderDetail->quantity;
                                
                                if (count($orderDetail->orderIngredients) > 0) {
                                    foreach ($orderDetail->orderIngredients as $ingredient) {
                                        $updatedQuantity    = $ingredient->quantity + $quantity;
                                        $updatedTotalPrice  = $ingredient->price * $updatedQuantity;

                                        $totalIngredientPrice += $updatedTotalPrice;

                                        OrderIngredient::where([
                                                            'id'    => $ingredient->id
                                                        ])
                                                        ->update([
                                                            'quantity'      => $updatedQuantity,
                                                            'total_price'   => $updatedTotalPrice
                                                        ]);
                                    }
                                }

                                $orderDetail->total_price = $productTotalPrice + $totalIngredientPrice;
                                $orderDetail->save();
                            }
                        }
                        // Drinks
                        else if ($orderDetail->drink_id != null) {
                            $orderDetail->quantity          = $orderDetail->quantity + $quantity;
                            $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                            $orderDetail->total_price       = $orderDetail->price * $orderDetail->quantity;
                            $orderDetail->save();
                        }
                        // Special Menu
                        else if ($orderDetail->special_menu_id != null) {
                            $orderDetail->quantity          = $orderDetail->quantity + $quantity;
                            $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                            $orderDetail->total_price       = $orderDetail->price * $orderDetail->quantity;
                            $orderDetail->save();
                        }

                        $title      = trans('custom.success');
                        $message    = trans('custom.update_cart_successful');
                        $type       = 'success';
                    } else {
                        $title      = trans('custom.error');
                        $message    = trans('custom.something_went_wrong');
                        $type       = 'error';
                    }
                }
                // Decrease cart item
                else {
                    if ($orderDetail != null) {
                        // Product
                        if ($orderDetail->product_id != null) {
                            // Only product start
                            if ($orderDetail->has_ingredients == 'N') {
                                if (($orderDetail->quantity - $quantity) > 0) {
                                    $orderDetail->quantity          = $orderDetail->quantity - $quantity;
                                    $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                                    $orderDetail->total_price       = $orderDetail->price * $orderDetail->quantity;
                                    $orderDetail->save();
                                } else {
                                    OrderDetailLocal::where([
                                                        'order_id'          => $orderId,
                                                        'order_details_id'  => $orderDetailsId,
                                                    ])
                                                    ->delete();

                                    if ($orderDetail->has_attribute == 'Y') {
                                        OrderAttributeLocal::where([
                                                            'order_id'          => $orderId,
                                                            'order_details_id'  => $orderDetailsId,
                                                            'product_id'        => $orderDetail->product_id,
                                                        ])
                                                        ->delete();
                                    }

                                    $orderDetail->delete();
                                }
                            }
                            // Only product end
                            // for Product + ingredients
                            else {
                                if (($orderDetail->quantity - $quantity) > 0) {
                                    $orderDetail->quantity          = $orderDetail->quantity - $quantity;
                                    $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                                    
                                    $productTotalPrice = $orderDetail->price * $orderDetail->quantity;
                                    
                                    if (count($orderDetail->orderIngredients) > 0) {
                                        foreach ($orderDetail->orderIngredients as $ingredient) {
                                            $updatedQuantity    = $ingredient->quantity - $quantity;
                                            $updatedTotalPrice  = $ingredient->price * $updatedQuantity;

                                            $totalIngredientPrice += $updatedTotalPrice;

                                            OrderIngredient::where([
                                                                'id'    => $ingredient->id
                                                            ])
                                                            ->update([
                                                                'quantity'      => $updatedQuantity,
                                                                'total_price'   => $updatedTotalPrice
                                                            ]);
                                        }
                                    }

                                    $orderDetail->total_price = $productTotalPrice + $totalIngredientPrice;
                                    $orderDetail->save();
                                } else {
                                    OrderIngredient::where([
                                                        'order_id'          => $orderId,
                                                        'order_details_id'  => $orderDetailsId,
                                                        'product_id'        => $orderDetail->product_id,
                                                    ])
                                                    ->delete();
                                    OrderIngredientLocal::where([
                                                            'order_id'          => $orderId,
                                                            'order_details_id'  => $orderDetailsId,
                                                            'product_id'        => $orderDetail->product_id,
                                                        ])
                                                        ->delete();
                                    OrderDetailLocal::where([
                                                        'order_id'          => $orderId,
                                                        'order_details_id'  => $orderDetailsId,
                                                    ])
                                                    ->delete();
                                    
                                    $orderDetail->delete();
                                }
                            }

                            //Checking if NO product exist then Drinks
                            $countOneProductExist = OrderDetail::where(['order_id' => $orderId])->where('product_id','!=',null)->count();
                            if ($countOneProductExist == 0) {
                                OrderDetail::where(['order_id' => $orderId])->where('drink_id','!=',null)->delete();
                            }
                        }
                        // Drinks
                        else if ($orderDetail->drink_id != null) {
                            if (($orderDetail->quantity - $quantity) > 0) {
                                $orderDetail->quantity          = $orderDetail->quantity - $quantity;
                                $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                                $orderDetail->total_price       = $orderDetail->price * $orderDetail->quantity;
                                $orderDetail->save();
                            } else {
                                $orderDetail->delete();
                            }
                        }
                        // Special Menu
                        else if ($orderDetail->special_menu_id != null) {
                            if (($orderDetail->quantity - $quantity) > 0) {
                                $orderDetail->quantity          = $orderDetail->quantity - $quantity;
                                $orderDetail->unit_total_price  = $orderDetail->price * $orderDetail->quantity;
                                $orderDetail->total_price       = $orderDetail->price * $orderDetail->quantity;
                                $orderDetail->save();
                            } else {
                                $orderDetail->delete();
                            }                            
                        }

                        //Checking if NO product / drinks / special menu exist then delete main ORDER
                        $countOrderDetails = OrderDetail::where(['order_id' => $orderId])->count();
                        if ($countOrderDetails == 0) {
                            $order = Order::find($orderId);
                            $order->delete();
                        }
                    } else {
                        $title      = trans('custom.error');
                        $message    = trans('custom.something_went_wrong');
                        $type       = 'error';
                    }
                }
            }
        }

        return json_encode([
            'title'     => $title,
            'message'   => $message,
            'type'      => $type,
        ]);
    }

    /*****************************************************/
    # Function name : ingredientsWithProductPrice
    # Params        : Request $request
    /*****************************************************/
    public function ingredientsWithProductPrice(Request $request)
    {
        $currentLang = App::getLocale();
        $lang = strtoupper($currentLang);
        $userData = Auth::user();
        $metaData = Helper::getMetaData();
        $totalPrice = 0;
        
        if ($request->isMethod('POST')) {
            $productId = isset($request->productId) ? Helper::customEncryptionDecryption($request->productId, 'decrypt') : '';
            $selectedIngredients = isset($request->selectedIngredients) ? $request->selectedIngredients : '';

            $totalPrice = 0;
            $productsDetails = Product::whereNull('deleted_at')->where(['id' => $productId,'status' => '1'])->first();
            $totalPrice = $productsDetails->price;
            if (strpos($selectedIngredients, ',') !== false) {
                $ingredientIds = explode(',',$selectedIngredients);
            } else {
                $ingredientIds[] = $selectedIngredients;
            }            

            if (count($ingredientIds) > 0) {
                asort($ingredientIds);
                foreach ($ingredientIds as $ingredient) {
                    if ($ingredient != '') {
                        $dataIngredient = Ingredient::where(['id' => $ingredient, 'status' => '1'])->whereNull('deleted_at')->first();
                        $totalPrice += $dataIngredient->price;
                    }                    
                }
            }
        }
        echo Helper::formatToTwoDecimalPlaces($totalPrice);
        exit(0);
    }
    
    /*****************************************************/
    # Function name : applyCoupon
    # Params        : Request $request
    /*****************************************************/
    public function applyCoupon(Request $request)
    {
        $currentLang = $lang = App::getLocale();
        $response['has_error']  = 1;
        $response['msg']        = trans('custom.please_try_again');
        $response['net_payable_amount'] = 0.00;
        $response['card_amount']        = 0.00;
        if ($request->isMethod('POST')) {
            $couponCode             = isset($request->coupon_code) ? $request->coupon_code : '';
            $deliveryCharge         = isset($request->delivery_charge) ? $request->delivery_charge : 0;
            $paymentMethod          = isset($request->payment_method) ? $request->payment_method : 1;
            $email          = isset($request->email) ? trim($request->email) : ''; 
            $couponDiscountAmount   = $discountAmount = 0;
            $now                    = strtotime(date('Y-m-d H:i'));
            $cartDetails            = Helper::getCartItemDetails();
            Session::put('couponCode', '');
            Session::put('couponDiscountAmount', '');

            $netPayableAmount   = $cartDetails['payableAmount'] + $deliveryCharge;
            // $cardAmount         = (($netPayableAmount * 2.9) / 100) + 0.30;
            //$cardAmount         = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
            $cardAmount             = Helper::paymentCardFee($netPayableAmount);
            
            if ($couponCode != '') {
                $conditions[] = ['status', '1'];
                $conditions[] = ['code', $couponCode];
                $conditions[] = ['start_time', '<=', $now];

                $couponData = Coupon::whereNull('deleted_at')->where($conditions)->first();
                // dd($couponData);
                $process = true;
                if ($couponData != null) {
                     // $orderData = Order::where($conditions)->first();
                     //$email
                     
                    if ($couponData->is_one_time_use_per_user == 'Y' && !Auth::user()) {
                        //$user = User::where('email', $email)->first();
                        $response['has_error'] = 1;
                        $response['msg'] = trans('custom.coupon_invalid_expired');
                    }elseif ($couponData->is_one_time_use == 'Y' && $couponData->is_used == 'Y') {
                        $response['has_error'] = 1;
                        $response['msg'] = trans('custom.message_coupon_already_used');
                    } else {
                        if ($couponData->end_time != null) {
                            if ($now > $couponData->end_time) {
                                $process = false;
                            }
                        }
                        /**check for loggedin user */
                        $invalidCheckOrder=1;  
                        if($process && $couponData->is_one_time_use_per_user == 'Y'){
                            $ccode=$couponData->code;
                            $user_id=Auth::user()->id;
                            $cid=$couponData->id;
                            $likeparam='{"id":'.$cid.','; 
                            $orderData = Order::where('coupon_details', 'like', '%' .$likeparam . '%')->where('type','O')->where('user_id',$user_id)->where('coupon_code',$ccode)->first();
                            if($orderData){
                                $response['has_error'] = 1;
                                $response['msg'] = trans('custom.message_coupon_already_used');
                                $invalidCheckOrder=0;
                            }
                        }
                       if($invalidCheckOrder){
                        // process to apply coupon
                        if ($process) {
                            $payableAmount  = $cartDetails['payableAmount'] + $deliveryCharge;
                            $couponAmount   = isset($couponData->amount) ? $couponData->amount : '';

                            if ($couponAmount != '') {
                                // If discount type = Percentage
                                if ($couponData->discount_type == 'P') {
                                    $discountAmount = (($payableAmount * $couponData->amount) / 100);
                                } else {    // discount type = Flat
                                    $discountAmount = $couponData->amount;
                                }

                                // related to minimum cart
                                if ($couponData->has_minimum_cart_amount == 'Y') {
                                    if ($cartDetails['payableAmount'] >= $couponData->cart_amount) {
                                        // checking discount amount is greater than payable amount or not
                                        if ($discountAmount > $payableAmount) {
                                            $couponDiscountAmount = $discountAmount = $payableAmount;
                                        }

                                        $discountAmount     = $couponDiscountAmount = Helper::formatToTwoDecimalPlaces($discountAmount);
                                        $netPayableAmount   = $payableAmount - $couponDiscountAmount;

                                        // $cardAmount         = (($netPayableAmount * 2.9) / 100) + 0.30;
                                        //$cardAmount         = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
                                        $cardAmount             = Helper::paymentCardFee($netPayableAmount);
                                        Session::put('couponCode', $couponCode);
                                        Session::put('couponDiscountAmount', $discountAmount);

                                        $response['has_error']          = 0;
                                        $response['msg']                = trans('custom.coupon_applied_successful');
                                        $response['discount_amount']    = Helper::formatToTwoDecimalPlaces($discountAmount);
                                    } else {
                                        $response['has_error'] = 1;
                                        $response['msg'] = trans('custom.minimum_cart_error', ['amount' => Helper::formatToTwoDecimalPlaces($couponData->cart_amount)]);
                                        $response['discount_amount']    = Helper::formatToTwoDecimalPlaces(0);
                                    }
                                } else {
                                    // checking discount amount is greater than payable amount or not
                                    if ($discountAmount > $payableAmount) {
                                        $couponDiscountAmount = $discountAmount = $payableAmount;
                                    }

                                    $discountAmount     = $couponDiscountAmount = Helper::formatToTwoDecimalPlaces($discountAmount);
                                    $netPayableAmount   = $payableAmount - $couponDiscountAmount;
                                    
                                    // $cardAmount         = (($netPayableAmount * 2.9) / 100) + 0.30;
                                   // $cardAmount         = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
                                    $cardAmount             = Helper::paymentCardFee($netPayableAmount);
                                    Session::put('couponCode', $couponCode);
                                    Session::put('couponDiscountAmount', $discountAmount);

                                    $response['has_error']          = 0;
                                    $response['msg']                = trans('custom.coupon_applied_successful');
                                    $response['discount_amount']    = Helper::formatToTwoDecimalPlaces($discountAmount);
                                }
                            } else {
                                $response['has_error'] = 1;
                                $response['msg'] = trans('custom.please_try_again');
                            }
                        } else {
                            $response['has_error'] = 1;
                            $response['msg'] = trans('custom.coupon_invalid_expired');
                        }
                       }
                    }
                } else {
                    $response['has_error'] = 1;
                    $response['msg'] = trans('custom.coupon_invalid_expired');
                }
            } else {
                $response['has_error'] = 1;
                $response['msg'] = trans('custom.please_try_again');
            }

            if ($paymentMethod != 2 && $paymentMethod != 4) {
                $cardAmount = 0;
            }

            $netPayableAmount = $netPayableAmount + Helper::formatToTwoDecimalPlaces($cardAmount);

            $netPayableAmount = Helper::priceRoundOff($netPayableAmount);
            $cardAmount       = Helper::priceRoundOff($cardAmount);

            $siteSettings   = Helper::getSiteSettings();
            if (!Auth::user()) {
                $paymentForm    = view('site.elements.guest_payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
            } else {
                $paymentForm    = view('site.elements.payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
            }            

            $response['net_payable_amount'] = Helper::formatToTwoDecimalPlaces($netPayableAmount);
            $response['card_amount']        = Helper::formatToTwoDecimalPlaces($cardAmount);
            $response['payment_form']       = $paymentForm;
        }
        echo json_encode($response);
    }

    /*****************************************************/
    # Function name : removeCoupon
    # Params        : Request $request
    /*****************************************************/
    public function removeCoupon(Request $request)
    {
        $cartDetails            = Helper::getCartItemDetails();
        $deliveryCharge         = isset($request->delivery_charge) ? $request->delivery_charge : 0;
        $couponCode             = isset($request->coupon_code) ? $request->coupon_code : '';
        $paymentMethod          = isset($request->payment_method) ? $request->payment_method : 1;
        $netPayableAmount       = $cartDetails['payableAmount'] + $deliveryCharge;
        // $cardAmount             = (($netPayableAmount * 2.9) / 100) + 0.30;
        //$cardAmount             = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
        $cardAmount             = Helper::paymentCardFee($netPayableAmount);
        
        if ($couponCode != '') {
            Session::put('couponCode', '');
            Session::put('couponDiscountAmount', '');
            Session::put([
                'coupon_code'               => '',
                'calculated_card_amount'    => '',
                'calculated_discount_amount'=> '',
                'coupon_details'            => '',
            ]);
            $response['has_error']          = 0;
            $response['msg']                = trans('custom.coupon_removed_successful');
        } else {
            $response['has_error']          = 1;
            $response['msg']                = trans('custom.please_try_again');
        }
        $response['discount_amount']    = '0.00';
        if ($paymentMethod != 2 && $paymentMethod != 4) {
            $cardAmount = 0;
        }

        $netPayableAmount = $netPayableAmount + Helper::formatToTwoDecimalPlaces($cardAmount);
        $netPayableAmount = Helper::priceRoundOff($netPayableAmount);
        // $cardAmount       = Helper::priceRoundOff($cardAmount);

        $siteSettings   = Helper::getSiteSettings();
        if (!Auth::user()) {
            $paymentForm    = view('site.elements.guest_payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        } else {
            $paymentForm    = view('site.elements.payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        }

        $response['net_payable_amount'] = Helper::formatToTwoDecimalPlaces($netPayableAmount);
        $response['card_amount']        = Helper::formatToTwoDecimalPlaces($cardAmount);
        $response['payment_form']       = $paymentForm;

        echo json_encode($response);
    }
    
    /*****************************************************/
    # Function name : calculateCardAmount
    # Params        : Request $request
    /*****************************************************/
    public function calculateCardAmount(Request $request)
    {
        $cartDetails        = Helper::getCartItemDetails();
        $deliveryCharge     = isset($request->delivery_charge) ? $request->delivery_charge : 0;
        $discountAmount     = isset($request->discount_Amount) ? $request->discount_Amount : 0;
        $netPayableAmount   = $cartDetails['payableAmount'] + $deliveryCharge - $discountAmount;
        // $cardAmount         = (($netPayableAmount * 2.9) / 100) + 0.30;
       // $cardAmount         = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
        // $cardAmount         = Helper::priceRoundOff($cardAmount);
        $cardAmount             = Helper::paymentCardFee($netPayableAmount);
        
        $netPayableAmount   = $netPayableAmount + Helper::formatToTwoDecimalPlaces($cardAmount);

        $netPayableAmount   = Helper::priceRoundOff($netPayableAmount);

        $siteSettings   = Helper::getSiteSettings();
        if (!Auth::user()) {
            $paymentForm    = view('site.elements.guest_payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        } else {
            $paymentForm    = view('site.elements.payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        }

        $response['has_error']          = 0;
        $response['msg']                = trans('custom.coupon_removed_successful');
        $response['net_payable_amount'] = Helper::formatToTwoDecimalPlaces($netPayableAmount);
        $response['card_amount']        = Helper::formatToTwoDecimalPlaces($cardAmount);
        $response['payment_form']       = $paymentForm;

        echo json_encode($response);
    }
    
    /*****************************************************/
    # Function name : regenerateStripeForm
    # Params        : Request $request
    /*****************************************************/
    public function regenerateStripeForm(Request $request)
    {
        $cartDetails        = Helper::getCartItemDetails();
        $deliveryCharge     = isset($request->delivery_charge) ? $request->delivery_charge : 0;
        $discountAmount     = isset($request->discount_Amount) ? $request->discount_Amount : 0;
        $netPayableAmount   = $cartDetails['payableAmount'] + $deliveryCharge - $discountAmount;
        $netPayableAmount   = Helper::priceRoundOff($netPayableAmount);
        
        $siteSettings   = Helper::getSiteSettings();
        if (!Auth::user()) {
            $paymentForm    = view('site.elements.guest_payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        } else {
            $paymentForm    = view('site.elements.payment_form_with_coupon_card_delivery_charge')->with(['netPayableAmount' => $netPayableAmount, 'siteSettings' => $siteSettings])->render();
        }

        $response['has_error']          = 0;
        $response['msg']                = trans('custom.label_success');
        $response['net_payable_amount'] = $netPayableAmount;
        $response['payment_form']       = $paymentForm;

        echo json_encode($response);
    }

}
