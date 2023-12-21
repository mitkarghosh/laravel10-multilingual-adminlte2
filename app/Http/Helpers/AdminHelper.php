<?php
/*****************************************************/
# Globals
# Page/Class name   : Globals
# Purpose           : for global purpose
/*****************************************************/
namespace App\Http\Helpers;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SiteSetting;
use DateInterval;

class AdminHelper
{
   public const WEBITE_LANGUAGES = ['EN', 'DE']; // Admin language array
   
   public const ADMIN_USER_LIMIT    = 15;    // pagination limit for user list in admin panel
   public const ADMIN_LIST_LIMIT = 15;
   
   public const IMAGE_MAX_UPLOAD_SIZE = 5120; // Image upload max size (5mb)
   public const ICON_MAX_UPLOAD_SIZE = 1024; // Image upload max size (1mb)

   public const ADMIN_BANNER_THUMB_IMAGE_WIDTH  = '1920';   // Admin BANNER thumb image width
   public const ADMIN_BANNER_THUMB_IMAGE_HEIGHT = '812';   // Admin BANNER thumb image height
   public const ADMIN_BANNER_MOBILE_THUMB_IMAGE_WIDTH  = '796';   // Admin BANNER Mobile thumb image width
   public const ADMIN_BANNER_MOBILE_THUMB_IMAGE_HEIGHT = '812';   // Admin BANNER Mobile thumb image height

   public const ADMIN_DRINK_THUMB_IMAGE_WIDTH  = '70';   // Admin DRINK thumb image width
   public const ADMIN_DRINK_THUMB_IMAGE_HEIGHT = '80';   // Admin DRINK thumb image height   

   public const ADMIN_TAG_THUMB_IMAGE_WIDTH  = '18';   // Admin TAG thumb image width
   public const ADMIN_TAG_THUMB_IMAGE_HEIGHT = '18';   // Admin TAG thumb image height   

   public const ADMIN_ALLERGEN_THUMB_IMAGE_WIDTH  = '18';   // Admin ALLERGEN thumb image width
   public const ADMIN_ALLERGEN_THUMB_IMAGE_HEIGHT = '18';   // Admin ALLERGEN thumb image height   

   public const ADMIN_PRODUCT_THUMB_IMAGE_WIDTH  = '100';   // Admin PRODUCT thumb image width
   public const ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT = '120';   // Admin PRODUCT thumb image height

   public const ADMIN_SPECIAL_MENU_THUMB_IMAGE_WIDTH  = '70';   // Admin SPECIAL MENU thumb image width
   public const ADMIN_SPECIAL_MENU_THUMB_IMAGE_HEIGHT = '80';   // Admin SPECIAL MENU thumb image height   

   public const ADMIN_AVATAR_THUMB_IMAGE_WIDTH  = '180';   // Admin AVATAR thumb image width
   public const ADMIN_AVATAR_THUMB_IMAGE_HEIGHT = '180';   // Admin AVATAR thumb image height   

   public const ADMIN_CATEGORY_THUMB_IMAGE_WIDTH  = '615';   // Admin CATEGORY thumb image width
   public const ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT = '180';   // Admin CATEGORY thumb image height   

   public const UPLOADED_IMAGE_FILE_TYPES = ['jpeg', 'jpg', 'png', 'svg']; //Uploaded image file types

   
   /*****************************************************/
   # Function name : formatToTwoDecimalPlaces
   # Purpose       : Format data to 2 decimal places
   # Params        : $data
   /*****************************************************/
   public static function formatToTwoDecimalPlaces($data)
   {
      return number_format((float)$data, 2, '.', '');
   }

   /*****************************************************/
   # Function name : paginationMessage
   # Purpose       : Format data to 2 decimal places
   # Params        : $data = null
   /*****************************************************/
   public static function paginationMessage($data = null)
   {
      return 'Records '.$data->firstItem().' - '.$data->lastItem().' of '.$data->total();
   }

   /*****************************************************/
   # Function name : getCategories
   # Purpose       : Getting categories
   # Params        :
   /************************************-*****************/
   public static function getCategories()
   {
      $categoryList = array();
      $categoryListing = Category::where(['status' => '1'])->whereNull('deleted_at')->orderBy('sort', 'asc')->get();
      if ($categoryListing->count() > 0) {
         foreach ($categoryListing as $keyCategory => $valueCategory) {
            $categoryList[$valueCategory['id']] = $valueCategory['title'];
         }
      }
      return $categoryList;
   }

   /*****************************************************/
   # Function name : getCategoriesLocal
   # Purpose       : Getting categories
   # Params        :
   /************************************-*****************/
   public static function getCategoriesLocal()
   {
      $currentLang = \App::getLocale();
      $categoryList = array();
      $categoryListing = Category::where(['status' => '1'])
                                    ->whereNull('deleted_at')
                                    ->with([
                                       'local'=> function($query) use ($currentLang) {
                                           $query->where('lang_code','=', $currentLang);
                                       }
                                    ])
                                    ->orderBy('sort', 'asc')
                                    ->get();
      if ($categoryListing->count() > 0) {
         foreach ($categoryListing as $keyCategory => $valueCategory) {
            $categoryList[$valueCategory['id']] = $valueCategory->local[0]['local_title'];
         }
      }
      return $categoryList;
   }

   /*****************************************************/
   # Function name : getTags
   # Purpose       : Getting Tags
   # Params        :
   /************************************-*****************/
   public static function getTags()
   {
      $tagList = array();
      $tagListing = Tag::where(['status' => '1'])->whereNull('deleted_at')->get();
      if ($tagListing->count() > 0) {
         foreach ($tagListing as $keyTag => $valueTag) {
            $tagList[$valueTag['id']] = $valueTag['title'];
         }
      }
      return $tagList;
   }

   /*****************************************************/
   # Function name : getOrderDetails
   # Params        :
   /*****************************************************/
   public static function getOrderDetails($orderId)
   {
      $lang = \App::getLocale();
      $totalAmount = 0;
      $orderVal = [];
      $cartConditions = ['id' => $orderId];

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
      if ($getOrderDetails->orderDetails) {
         foreach ($getOrderDetails->orderDetails as $keyOrdDtls => $valOrdDtls) {
               $orderVal['product_details'][$keyOrdDtls]['title']              = $valOrdDtls->orderDetailLocals[0]->local_title;
               $orderVal['product_details'][$keyOrdDtls]['quantity']           = $valOrdDtls->quantity;
               $orderVal['product_details'][$keyOrdDtls]['price']              = $valOrdDtls->price;
               $orderVal['product_details'][$keyOrdDtls]['unit_total_price']   = $valOrdDtls->unit_total_price;
               $orderVal['product_details'][$keyOrdDtls]['total_price']        = $valOrdDtls->total_price;

               // Ingredients
               if ($valOrdDtls->has_ingredients == 'Y') {
                  foreach ($valOrdDtls->orderIngredients as $keyOrderIngredient => $valOrderIngredient) {
                     $orderVal['product_details'][$keyOrdDtls]['ingredients'][$keyOrderIngredient]['title'] = $valOrderIngredient->orderIngredientLocals[0]->local_ingredient_title;
                     $orderVal['product_details'][$keyOrdDtls]['ingredients'][$keyOrderIngredient]['quantity'] = $valOrderIngredient->quantity;
                     $orderVal['product_details'][$keyOrdDtls]['ingredients'][$keyOrderIngredient]['price'] = Helper::formatToTwoDecimalPlaces($valOrderIngredient->price);
                  }
               } else {
                  $orderVal['product_details'][$keyOrdDtls]['ingredients'] = [];
               }

               // Attributes
               if ($valOrdDtls->has_attribute == 'Y') {
                  $orderVal['product_details'][$keyOrdDtls]['attribute'] = $valOrdDtls->orderAttributeLocalDetails[0]->local_attribute_title;
               } else {
                  $orderVal['product_details'][$keyOrdDtls]['attribute'] = '';
               }

               // Drop down Menu details
               if ($valOrdDtls->is_menu == 'Y' && $valOrdDtls->orderDetailLocals[0]->local_drop_down_menu_title_value != null) {
                  $orderVal['product_details'][$keyOrdDtls]['menu'] = json_decode($valOrdDtls->orderDetailLocals[0]->local_drop_down_menu_title_value, true);
               } else {
                  $orderVal['product_details'][$keyOrdDtls]['menu'] = [];
               }

               $totalAmount += $valOrdDtls->total_price;
         }
      }

      $orderVal['total_price'] = Helper::formatToTwoDecimalPlaces($totalAmount);
      
      return $orderVal;
   }

   /*****************************************************/
   # Function name : getOrderDetailsForInvoice
   # Params        :
   /*****************************************************/
   public static function getOrderDetailsForInvoice($orderId)
   {
      $lang = \App::getLocale();
      $totalAmount = 0;
      $orderVal = [];
      $cartConditions = ['id' => $orderId];

		$categoryProductIds = $sortedProductIds = $categoryIds = $productIds = $orderDetailIds = [];
		$getDetails = Order::where($cartConditions)->first();
		if ($getDetails->orderDetails) {
         foreach ($getDetails->orderDetails as $keyDtls => $valDtls) {
				$categoryProductIds[$valDtls->orderProduct->category_id][$keyDtls]['product_id'] = $valDtls->product_id;
            $categoryProductIds[$valDtls->orderProduct->category_id][$keyDtls]['order_details_id'] = $valDtls->id;
				$categoryIds[$valDtls->orderProduct->category_id] = $valDtls->orderProduct->category_id;
            $orderDetailIds[] = $valDtls->id;
			}
		}
      // dd($categoryProductIds);

		$categoryList = Category::whereIn('id', $categoryIds)->orderBy('sort', 'asc')->get();
		if ($categoryList) {
			foreach ($categoryList as $keyCategory => $valCategory) {
				$sortedProductIds[] = $categoryProductIds[$valCategory->id];
			}
		}
      // dd($sortedProductIds);

		if (count($sortedProductIds)) {
      $keyOrdDtls = 0;
			foreach ($sortedProductIds as $valProduct) {
				foreach ($valProduct as $keyPro => $valPro) {
          $valOrdDtls = OrderDetail::where([
                                       'id' => $valPro['order_details_id'],
                                       'order_id' => $orderId,
                                       'product_id' => $valPro['product_id']])
                                       ->with([
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
                                       ])
                                       ->first();
          $categoryDetails = Category::where('id', $valOrdDtls->orderProduct->category_id)->with([
                                                        'local' => function ($query) use ($lang) {
                                                          $query->where('lang_code', '=', $lang);
                                                        }
                                                      ])
                                                      ->first();

            $orderVal['product_details'][$keyOrdDtls]['details_id']              	  = $valOrdDtls->id;
            $orderVal['product_details'][$keyOrdDtls]['menu_option_ids']              	  = $valOrdDtls->menu_option_ids;
          $orderVal['product_details'][$keyOrdDtls]['id']              	  = $valOrdDtls->product_id;
					$orderVal['product_details'][$keyOrdDtls]['title']              = $valOrdDtls->orderDetailLocals[0]->local_title;
          $orderVal['product_details'][$keyOrdDtls]['category_title']     = isset($categoryDetails->local[0]->local_title) ? $categoryDetails->local[0]->local_title : '';
					$orderVal['product_details'][$keyOrdDtls]['quantity']           = $valOrdDtls->quantity;
					$orderVal['product_details'][$keyOrdDtls]['price']              = $valOrdDtls->price;
					$orderVal['product_details'][$keyOrdDtls]['unit_total_price']   = $valOrdDtls->unit_total_price;
					$orderVal['product_details'][$keyOrdDtls]['total_price']        = $valOrdDtls->total_price;

					// Ingredients
					if ($valOrdDtls->has_ingredients == 'Y') {
						foreach ($valOrdDtls->orderIngredients as $keyOrderIngredient => $valOrderIngredient) {
							$orderVal['product_details'][$keyOrdDtls]['ingredients'][$keyOrderIngredient]['title'] = $valOrderIngredient->orderIngredientLocals[0]->local_ingredient_title;
							$orderVal['product_details'][$keyOrdDtls]['ingredients'][$keyOrderIngredient]['quantity'] = $valOrderIngredient->quantity;
							$orderVal['product_details'][$keyOrdDtls]['ingredients'][$keyOrderIngredient]['price'] = Helper::formatToTwoDecimalPlaces($valOrderIngredient->price);
						}
					} else {
						$orderVal['product_details'][$keyOrdDtls]['ingredients'] = [];
					}

					// Attributes
					if ($valOrdDtls->has_attribute == 'Y') {
						$orderVal['product_details'][$keyOrdDtls]['attribute'] = $valOrdDtls->orderAttributeLocalDetails[0]->local_attribute_title;
					} else {
						$orderVal['product_details'][$keyOrdDtls]['attribute'] = '';
					}

					// Drop down Menu details
					if ($valOrdDtls->is_menu == 'Y' && $valOrdDtls->orderDetailLocals[0]->local_drop_down_menu_title_value != null) {
						$orderVal['product_details'][$keyOrdDtls]['menu'] = json_decode($valOrdDtls->orderDetailLocals[0]->local_drop_down_menu_title_value, true);
					} else {
						$orderVal['product_details'][$keyOrdDtls]['menu'] = [];
					}

					$totalAmount += $valOrdDtls->total_price;
						
					$keyOrdDtls++;
				}
			}
		}

      $orderVal['total_price'] = Helper::formatToTwoDecimalPlaces($totalAmount);
      
      return $orderVal;
   }

   /*****************************************************/
   # Function name : getUserRoleSpecificRoutes
   # Params        : 
   /*****************************************************/
   public static function getUserRoleSpecificRoutes()
   {
      $existingRoutes = [];
      $userExistingRoles = \Auth::guard('admin')->user()->userRoles;
      if ($userExistingRoles) {
         foreach ($userExistingRoles as $role) {
            if ($role->rolePermissionToRolePage) {
               foreach ($role->rolePermissionToRolePage as $permission) {
                  $existingRoutes[] = $permission['routeName'];
               }
            }
         }
      }
      return $existingRoutes;
   }

   /*****************************************************/
    # Function name : customEncryptionDecryption
    # Params        :
    /*****************************************************/
    public static function customEncryptionDecryption($string, $action = 'encrypt')
    {
        $secretKey = 'c7tpe291z';
        $secretVal = 'GfY7r512';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', $secretVal), 0, 16);

        if ($action == 'encrypt') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /*****************************************************/
    # Function name : getSiteSettings
    # Params        :
    /*****************************************************/
    public static function getSiteSettings()
    {
        $siteSettingData = SiteSetting::first();
        return $siteSettingData;
    }

     //sp2
    /**
     * Create dropdown for addon
     */
    public static function addonDropDown($data){
           $option="<option value='' data-parent='0'>Select</option>";
           if($data){
               $currentLang = \App::getLocale();
               foreach($data as $list){
                     if($list->parent_id<1){
                         $title=($currentLang=='de')?$list->de_title:$list->en_title;
                         $option.="<option data-id='$list->id' value='$list->id' data-price='$list->price' data-en='$list->en_title' data-de='$list->de_title'>$title</option>";
                     }else{
                        $title=($currentLang=='de')?$list->de_value:$list->en_value;
                        $option.="<option  data-id='$list->id' value='$list->id' data-price='$list->price' data-parent='$list->parent_id' data-en='$list->en_value' data-de='$list->de_value'>$title</option>";
                      }
               }
           }
           return $option;
   }  

   
}
