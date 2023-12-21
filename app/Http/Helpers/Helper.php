<?php
/*****************************************************/
# Page/Class name   : Helper
# Purpose           : for global purpose
/*****************************************************/
namespace App\Http\Helpers;
use URL;
use DB;
use Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Helpers\NotificationHelper;
use App\Models\Category;
use App\Models\Cms;
use App\Models\SiteSetting;
use App\Models\PaymentSetting;
use App\Models\Banner;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Allergen;
use App\Models\OrderAttributeLocal;
use App\Models\ProductAddon;
use App\Models\DeliverySlot;
use App\Models\OrderReview;
use App\Models\Notification;
use App\Models\Coupon;
use App\Models\SpecialHour;
use App\Models\User;
use App\Models\OrderIngredient;
use App\Models\OrderIngredientLocal;
use App\Models\OrderDetailLocal;
use App\Models\DeliveryAddress;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Twilio\Rest\Client;

class Helper
{
    public const NO_IMAGE = 'no-image.png'; // No image

    public const WEBSITE_DEFAULT_LANGUAGE = 'en';

    public const WEBITE_LANGUAGES = ['en', 'de']; // Admin language array

    public const UPLOADED_DOC_FILE_TYPES = ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt', 'ods', 'odp', 'odt']; //Uploaded document file types

    public const UPLOADED_IMAGE_FILE_TYPES = ['jpeg', 'jpg', 'png', 'svg']; //Uploaded image file types

    public const PROFILE_IMAGE_MAX_UPLOAD_SIZE = 5120; // profile image upload max size (5mb)

    public const MINIMUM_ORDER_AMOUNT = 20; // Minimum order amount

    public const MY_ORDER_LISTING = 10; // My orders

    /*****************************************************/
    # Function name : getAppName
    # Params        :
    /*****************************************************/
    public static function getAppName()
    {
        //$getAppName = env('APP_NAME');
        $siteSettings = self::getSiteSettings();
        $appName = $siteSettings->website_title;
        return $appName;
    }

    /*****************************************************/
    # Function name : getAppNameFirstLetters
    # Params        :
    /*****************************************************/
    public static function getAppNameFirstLetters()
    {
        $siteSettings = self::getSiteSettings();
        $getAppName = $siteSettings->website_title;
        $explodedAppNamewords = explode(' ', $getAppName);
        $appLetters = '';
        foreach ($explodedAppNamewords as $letter) {
            $appLetters .= $letter[0];
        }
        return $appLetters;
    }

    /*****************************************************/
    # Function name : generateUniqueSlug
    # Params        : $model, $slug (name/title), $id
    /*****************************************************/
    public static function generateUniqueSlug($model, $slug, $id = null)
    {
        $slug = Str::slug($slug);
        $currentSlug = '';
        if ($id) {
            $currentSlug = $model->where('id', '=', $id)->value('slug');
        }

        if ($currentSlug && $currentSlug === $slug) {
            return $slug;
        } else {
            $slugList = $model->where('slug', 'LIKE', $slug . '%')->pluck('slug');
            if ($slugList->count() > 0) {
                $slugList = $slugList->toArray();
                if (!in_array($slug, $slugList)) {
                    return $slug;
                }
                $newSlug = '';
                for ($i = 1; $i <= count($slugList); $i++) {
                    $newSlug = $slug . '-' . $i;
                    if (!in_array($newSlug, $slugList)) {
                        return $newSlug;
                    }
                }
                return $newSlug;
            } else {
                return $slug;
            }
        }
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

    /*****************************************************/
    # Function name : getBaseUrl
    # Params        :
    /*****************************************************/
    public static function getBaseUrl()
    {
        $baseUrl = url('/');
        return $baseUrl;
    }

    /*****************************************************/
    # Function name : formattedDate
    # Params        : $getDate
    /*****************************************************/
    public static function formattedDate($getDate = null)
    {
        $formattedDate = date('dS M, Y');
        if ($getDate != null) {
            $formattedDate = date('dS M, Y', strtotime($getDate));
        }
        return $formattedDate;
    }

    /*****************************************************/
    # Function name : formattedDateTime
    # Params        : $getDateTime = unix timestamp
    /*****************************************************/
    public static function formattedDateTime($getDateTime = null)
    {
        $formattedDateTime = '';
        if ($getDateTime != null) {
            $formattedDateTime = date('dS M, Y H:i', $getDateTime);
        }
        return $formattedDateTime;
    }

    /*****************************************************/
    # Function name : formattedDatefromTimestamp
    # Params        : $getDateTime = unix timestamp
    /*****************************************************/
    public static function formattedDatefromTimestamp($getDateTime = null)
    {
        $formattedDateTime = '';
        if ($getDateTime != null) {
            $formattedDateTime = date('dS M, Y', $getDateTime);
        }
        return $formattedDateTime;
    }

    /*****************************************************/
    # Function name : formattedTimestamp
    # Params        : $getDateTime = unix timestamp
    /*****************************************************/
    public static function formattedTimestamp($getDateTime = null)
    {
        $timestamp = '';
        if ($getDateTime != null) {
            $timestamp = \Carbon\Carbon::createFromFormat('m/d/Y', $getDateTime)->timestamp;
        }
        return $timestamp;
    }

    /*****************************************************/
    # Function name : formattedTimestampBid
    # Params        : $getDateTime = unix timestamp
    /*****************************************************/
    public static function formattedTimestampBid($getDateTime = null)
    {
        $timestamp = '';
        if ($getDateTime != null) {
            $timestamp = date('Y-m-d H:i:s', $getDateTime);
        }
        return $timestamp;
    }

    /*****************************************************/
    # Function name : differnceBtnTimestampDateFrmCurrentDateInDays
    # Params        : $getDate = null
    /*****************************************************/
    public static function differnceBtnTimestampDateFrmCurrentDateInDays($getDate = null)
    {
        $days = '';
        if ($getDate != null) {
            $currentDate = date('Y-m-d');
            $diff = abs($getDate - strtotime($currentDate));
            $years = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

            if ($getDate < strtotime($currentDate)) {
                $days = '-' . $days;
            } else {
                $days = '+' . $days;
            }
        }
        return $days;
    }

    /*****************************************************/
    # Function name : getData
    # Params        :
    /*****************************************************/
    public static function getData($table = 'SiteSetting', $where = '')
    {
        if ($table == 'cms') {
            $metaData = Cms::where('id', $where)->first();
        } else {
            $metaData = SiteSetting::first();
        }
        return $metaData;
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
        $encrypt_method = 'AES-256-CBC';
        $key = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', $secretVal), 0, 16);

        if ($action == 'encrypt') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /*****************************************************/
    # Function name : formatToTwoDecimalPlaces
    # Params        : $data
    /*****************************************************/
    public static function formatToTwoDecimalPlaces($data)
    {
        return number_format((float) $data, 2, '.', '');
    }

    /*****************************************************/
    # Function name : generateCsv
    # Params        :
    /*****************************************************/
    public static function generateCsv($columnNames, $dataToPrint, $fileName)
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $callback = function () use ($columnNames, $dataToPrint) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnNames);
            foreach ($dataToPrint as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /*****************************************************/
    # Function name : cleanString
    # Params        : $content
    /*****************************************************/
    public static function cleanString($content)
    {
        $content = preg_replace('/&#?[a-z0-9]+;/i', '', $content);
        $content = preg_replace("/[\n\r]/", '', $content);
        $content = strip_tags($content);
        return $content;
    }

    /*****************************************************/
    # Function name : getMetaData
    # Params        :
    /*****************************************************/
    public static function getMetaData($table = 'SiteSetting', $where = '')
    {
        $currentLang = \App::getLocale();
        if ($table == 'cms') {
            $metaData = Cms::where('slug', $where)
                ->with([
                    'local' => function ($query) use ($currentLang) {
                        $query->where('lang_code', '=', $currentLang);
                    },
                ])
                ->first();
            $return['title'] = $metaData->local[0]->title;
            $return['keyword'] = $metaData['meta_keyword'];
            $return['description'] = $metaData['meta_description'];
            $return['local_description'] = $metaData->local[0]->description;
            return $return;
        } else {
            $metaData = SiteSetting::select('default_meta_title', 'default_meta_keywords', 'default_meta_description')->first();
            $return['title'] = $metaData['default_meta_title'];
            $return['keyword'] = $metaData['default_meta_keywords'];
            $return['description'] = $metaData['default_meta_description'];
            return $return;
        }
    }

    /*****************************************************/
    # Function name : generateUniquesOrderId
    # Params        :
    /*****************************************************/
    public static function generateUniquesOrderId()
    {
        $timeNow = date('his');
        $randNumber = strtoupper(substr(sha1(time()), 0, 4));
        return $unique = 'CBX' . $timeNow . $randNumber;
    }

    /*****************************************************/
    # Function name : getCartItemDetails
    # Params        :
    /*****************************************************/
    public static function getCartItemDetails()
    {
        $cartSessionId = '';
        if (Session::get('cartSessionId') != '') {
            $cartSessionId = Session::get('cartSessionId');
        }

        $cartUserId = 0;
        $totalCartCount = 0;
        $getCartData = [];
        if (Auth::user()) {
            $cartUserId = Auth::user()->id;
            $cartConditions = ['user_id' => $cartUserId, 'type' => 'C'];
        } else {
            $cartConditions = ['session_id' => $cartSessionId, 'type' => 'C'];
        }

        $getOrderDetails = Order::where($cartConditions)->first();

        $cartArray = [];
        $totalCartPrice = $cartOrderId = $paymentMethod = $productExistFlag = $deliveryCharges = 0;

        if ($getOrderDetails != null) {
            $cartOrderId = $getOrderDetails->id;
            $paymentMethod = $getOrderDetails->payment_method;
            $deliveryCharges = $getOrderDetails->delivery_charge;
            // Main Cart array
            if (isset($getOrderDetails->orderDetails) && count($getOrderDetails->orderDetails) > 0) {
                $i = 0;
                foreach ($getOrderDetails->orderDetails as $orderDetails) {
                    $productImage = '';
                    $vatAmount = 0;
                    $cartArray[$i]['id'] = $orderDetails->id;
                    $cartArray[$i]['order_id'] = $orderDetails->order_id;
                    $cartArray[$i]['product_id'] = $orderDetails->product_id;
                    $cartArray[$i]['drink_id'] = $orderDetails->drink_id;
                    $cartArray[$i]['special_menu_id'] = $orderDetails->special_menu_id;
                    $cartArray[$i]['has_ingredients'] = $orderDetails->has_ingredients;
                    $cartArray[$i]['has_attribute'] = $orderDetails->has_attribute;
                    $cartArray[$i]['attribute_id'] = $orderDetails->attribute_id;
                    $cartArray[$i]['is_menu'] = $orderDetails->is_menu;
                    $cartArray[$i]['quantity'] = $orderDetails->quantity;
                    $cartArray[$i]['price'] = $orderDetails->price;
                    $cartArray[$i]['total_price'] = $orderDetails->total_price;

                    // order product locals
                    if (count($orderDetails->orderDetailLocals) > 0) {
                        foreach ($orderDetails->orderDetailLocals as $detailLocal) {
                            $cartArray[$i]['local_details'][$detailLocal->lang_code]['local_title'] = $detailLocal->local_title;
                            if ($orderDetails->is_menu == 'Y') {
                                $menuValueDetails = json_decode($detailLocal->local_drop_down_menu_title_value, true);
                                if (!empty($menuValueDetails)) {
                                    foreach ($menuValueDetails as $keyMenuDtls => $menuValDtls) {
                                        $cartArray[$i]['menu_title_value_local_details'][$detailLocal->lang_code][$keyMenuDtls]['menu_local_title'] = $menuValDtls['menu_title'];
                                        $cartArray[$i]['menu_title_value_local_details'][$detailLocal->lang_code][$keyMenuDtls]['menu_local_value'] = implode('#', $menuValDtls['menu_value']);
                                    }
                                } else {
                                    $cartArray[$i]['menu_title_value_local_details'] = [];
                                }
                            } else {
                                $cartArray[$i]['menu_title_value_local_details'] = [];
                            }
                        }
                    } else {
                        $cartArray[$i]['local_details'] = [];
                        $cartArray[$i]['menu_title_value_local_details'] = [];
                    }

                    // ingredients locals
                    if (count($orderDetails->orderIngredients) > 0) {
                        foreach ($orderDetails->orderIngredients as $key => $orderIngredient) {
                            $cartArray[$i]['ingredient_local_details'][$key]['order_ingredient_id'] = $orderIngredient->id;
                            $cartArray[$i]['ingredient_local_details'][$key]['ingredient_id'] = $orderIngredient->ingredient_id;
                            $cartArray[$i]['ingredient_local_details'][$key]['product_id'] = $orderIngredient->product_id;
                            $cartArray[$i]['ingredient_local_details'][$key]['quantity'] = $orderIngredient->quantity;
                            $cartArray[$i]['ingredient_local_details'][$key]['price'] = $orderIngredient->price;
                            $cartArray[$i]['ingredient_local_details'][$key]['total_price'] = $orderIngredient->total_price;
                            foreach ($orderIngredient->orderIngredientLocals as $detailIngredientLocal) {
                                $cartArray[$i]['ingredient_local_details'][$key][$detailIngredientLocal->lang_code]['local_title'] = $detailIngredientLocal->local_ingredient_title;
                            }
                        }
                    } else {
                        $cartArray[$i]['ingredient_local_details'] = [];
                    }

                    // attributes locals
                    if ($orderDetails->has_attribute == 'Y') {
                        $orderAttributeLocalDetails = OrderAttributeLocal::where([
                            'order_id' => $orderDetails->order_id,
                            'order_details_id' => $orderDetails->id,
                            'product_id' => $orderDetails->product_id,
                            'attribute_id' => $orderDetails->attribute_id,
                        ])->get();

                        foreach ($orderAttributeLocalDetails as $key => $detailAttributeLocal) {
                            $cartArray[$i]['attribute_local_details'][$detailAttributeLocal->lang_code]['local_title'] = $detailAttributeLocal->local_attribute_title;
                        }
                    } else {
                        $cartArray[$i]['attribute_local_details'] = [];
                    }

                    if ($orderDetails->product_id != '') {
                        $productExistFlag = 1;
                    }

                    //Total price
                    $totalCartPrice += $orderDetails->total_price;
                    $i++;
                }
            }
        }

        // dd($cartArray);

        $totalCartCount = count($cartArray);
        $cartDetailArray = [
            'cartOrderId' => $cartOrderId,
            'productExist' => $productExistFlag,
            'itemDetails' => $cartArray,
            'totalItem' => $totalCartCount,
            'totalCartPrice' => (float) self::formatToTwoDecimalPlaces($totalCartPrice),
            'payableAmount' => (float) self::formatToTwoDecimalPlaces($totalCartPrice),
            'deliveryCharges' => (float) self::formatToTwoDecimalPlaces($deliveryCharges),
            'paymentMethod' => $paymentMethod,
        ];

        return $cartDetailArray;
    }

    /*****************************************************/
    # Function name : getCartAmount
    # Params        :
    /*****************************************************/
    public static function getCartAmount()
    {
        $cartSessionId = '';
        if (Session::get('cartSessionId') != '') {
            $cartSessionId = Session::get('cartSessionId');
        }

        $cartUserId = 0;
        $totalCartCount = 0;
        $getCartData = [];
        if (Auth::user()) {
            $cartUserId = Auth::user()->id;
            $cartConditions = ['user_id' => $cartUserId, 'type' => 'C'];
        } else {
            $cartConditions = ['session_id' => $cartSessionId, 'type' => 'C'];
        }

        $getOrderDetails = Order::where($cartConditions)->first();

        $totalCartPrice = 0;
        if ($getOrderDetails != null) {
            // Main Cart array
            if (isset($getOrderDetails->orderDetails) && count($getOrderDetails->orderDetails) > 0) {
                foreach ($getOrderDetails->orderDetails as $orderDetails) {
                    $totalCartPrice += $orderDetails->total_price;
                }
            }
        }
        $totalCartPrice = (float) self::formatToTwoDecimalPlaces($totalCartPrice);
        return $totalCartPrice;
    }

    /*****************************************************/
    # Function name : getAllergenList
    # Params        :
    /*****************************************************/
    public static function getAllergenList()
    {
        $currentLang = \App::getLocale();
        $allergenList = Allergen::where(['status' => '1'])
            ->whereNull('deleted_at')
            ->with([
                'local' => function ($query) use ($currentLang) {
                    $query->where('lang_code', '=', $currentLang);
                },
            ])
            ->orderBy('sort', 'asc')
            ->get();

        return $allergenList;
    }

    /**
     * date in Y-m-d shanti info
     */
    public static function dateInYmd($date)
    {
        // $date = "25-Mar-1989";
        $format = 'd/m/Y';
        $res = date_create_from_format($format, $date);
        return date_format($res, 'Y-m-d');
    }
    /*****************************************************/
    # Function name : generateDeliverySlot
    # Params        :
    /*****************************************************/
    public static function generateDeliverySlot()
    {
        $siteSettings = self::getSiteSettings();
        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
        $currentDate = date('Y-m-d');
        $today = date('l');

        // Start :: special hour
        $shopOpenCloseTimeAccordingToDay = SpecialHour::where('special_date', $currentDate)->first();
        if ($shopOpenCloseTimeAccordingToDay == null) {
            $shopOpenCloseTimeAccordingToDay = DeliverySlot::where('day_title', $today)->first();
        }
        // End :: special hour

        $slots = [];

        // If not holiday
        if ($shopOpenCloseTimeAccordingToDay->holiday == 0) {
            $currentTimeStamp = strtotime(date('H:i'));
            // current time + minimum delivery delay = Delivery start time
            $deliveryStartTime = strtotime('+' . $minimumDeliveryDelayTime . ' minutes', $currentTimeStamp);

            // Shop open and close time
            $shopOpenTime = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
            $shopCloseTime = strtotime($shopOpenCloseTimeAccordingToDay->end_time);

            // Shop open Hour & minute
            $shopOpenTimeInHour = date('H', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
            $shopOpenTimeInMinute = date('i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));

            $remainder = $shopOpenTimeInMinute % 15;

            $remainingFromMinute = $shopOpenTimeInMinute - $remainder;

            if ($remainingFromMinute / 15 == 0) {
                $slotStartTime = strtotime($shopOpenTimeInHour . ':15');
            } elseif ($remainingFromMinute / 15 == 1) {
                $slotStartTime = strtotime($shopOpenTimeInHour . ':30');
            } elseif ($remainingFromMinute / 15 == 2) {
                $slotStartTime = strtotime($shopOpenTimeInHour . ':45');
            } elseif ($remainingFromMinute / 15 == 3) {
                $slotStartTime = strtotime($shopOpenTimeInHour + 1 . ':00');
            }

            // slot break up
            if ($slotStartTime < $shopCloseTime) {
                // start :: added 01.06.2021
                $firstSlotStartTime = $shopOpenCloseTimeAccordingToDay->start_time;
                if ($slotStartTime > strtotime($firstSlotStartTime)) {
                    $slotStartTime = strtotime($firstSlotStartTime);
                }
                // end :: added 01.06.2021

                for ($slotStartTime; $slotStartTime <= $shopCloseTime; ) {
                    if ($slotStartTime > $deliveryStartTime) {
                        $slots[] = date('H:i', $slotStartTime);
                    }

                    $slotStartTime = strtotime('+15 minutes', $slotStartTime);
                }
            }
            // start :: added 01.06.2021
            if (count($slots)) {
                if (strtotime($slots[count($slots) - 1]) != $shopCloseTime && $deliveryStartTime <= $shopCloseTime) {
                    $slots[] = date('H:i', $shopCloseTime);
                }
            }
            // end :: added 01.06.2021

            // If slot 2 exist START
            if ($shopOpenCloseTimeAccordingToDay->start_time2 != null && $shopOpenCloseTimeAccordingToDay->end_time2 != null) {
                // Shop open and close time
                $shopOpenTime2 = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                $shopCloseTime2 = strtotime($shopOpenCloseTimeAccordingToDay->end_time2);

                // Shop open Hour & minute
                $shopOpenTimeInHour2 = date('H', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                $shopOpenTimeInMinute2 = date('i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));

                $remainder2 = $shopOpenTimeInMinute2 % 15;

                $remainingFromMinute2 = $shopOpenTimeInMinute2 - $remainder2;

                if ($remainingFromMinute2 / 15 == 0) {
                    $slotStartTime2 = strtotime($shopOpenTimeInHour2 . ':15');
                } elseif ($remainingFromMinute2 / 15 == 1) {
                    $slotStartTime2 = strtotime($shopOpenTimeInHour2 . ':30');
                } elseif ($remainingFromMinute2 / 15 == 2) {
                    $slotStartTime2 = strtotime($shopOpenTimeInHour2 . ':45');
                } elseif ($remainingFromMinute2 / 15 == 3) {
                    $slotStartTime2 = strtotime($shopOpenTimeInHour2 + 1 . ':00');
                }

                // slot break up
                if ($slotStartTime2 < $shopCloseTime2) {
                    // start :: added 01.06.2021
                    $secondSlotStartTime = $shopOpenCloseTimeAccordingToDay->start_time2;
                    if ($slotStartTime2 > strtotime($secondSlotStartTime)) {
                        $slotStartTime2 = strtotime($secondSlotStartTime);
                    }
                    // end :: added 01.06.2021

                    for ($slotStartTime2; $slotStartTime2 <= $shopCloseTime2; ) {
                        if ($slotStartTime2 > $deliveryStartTime) {
                            $slots[] = date('H:i', $slotStartTime2);
                        }

                        $slotStartTime2 = strtotime('+15 minutes', $slotStartTime2);
                    }
                }

                // start :: added 01.06.2021
                if (count($slots)) {
                    if (strtotime($slots[count($slots) - 1]) != $shopCloseTime2 && $deliveryStartTime <= $shopCloseTime2) {
                        $slots[] = date('H:i', $shopCloseTime2);
                    }
                }
                // end :: added 01.06.2021
            }
            // If slot 2 exist END
        }

        return $slots;
    }

    /*****************************************************/
    # Function name : getOrderDetails
    # Params        :
    /*****************************************************/
    public static function getOrderDetails($orderId, $userId)
    {
        $lang = \App::getLocale();
        $totalAmount = 0;
        $orderVal = [];
        $cartConditions = ['user_id' => $userId, 'type' => 'O', 'id' => $orderId];

        $getOrderDetails = Order::where($cartConditions)
            ->with([
                'orderDetails' => function ($query) use ($lang) {
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
                                },
                            ]);
                        },
                    ]);
                },
            ])
            ->first();
        if ($getOrderDetails->orderDetails) {
            foreach ($getOrderDetails->orderDetails as $keyOrdDtls => $valOrdDtls) {
                $orderVal['product_details'][$keyOrdDtls]['title'] = $valOrdDtls->orderDetailLocals[0]->local_title;
                $orderVal['product_details'][$keyOrdDtls]['quantity'] = $valOrdDtls->quantity;
                $orderVal['product_details'][$keyOrdDtls]['price'] = $valOrdDtls->price;
                $orderVal['product_details'][$keyOrdDtls]['unit_total_price'] = $valOrdDtls->unit_total_price;
                $orderVal['product_details'][$keyOrdDtls]['total_price'] = $valOrdDtls->total_price;

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

        $orderVal['delivery_type'] = $getOrderDetails['delivery_type'];
        $orderVal['delivery_charge'] = Helper::formatToTwoDecimalPlaces($getOrderDetails['delivery_charge']);
        $orderVal['total_price'] = Helper::formatToTwoDecimalPlaces($totalAmount);
        $orderVal['payment_method'] = $getOrderDetails['payment_method'];
        $orderVal['card_payment_amount'] = Helper::formatToTwoDecimalPlaces($getOrderDetails['card_payment_amount']);
        $orderVal['coupon_code'] = $getOrderDetails['coupon_code'];
        $orderVal['discount_amount'] = Helper::formatToTwoDecimalPlaces($getOrderDetails['discount_amount']);

        return $orderVal;
    }

    /*****************************************************/
    # Function name : gettingReviews
    # Params        :
    /*****************************************************/
    public static function gettingReviews()
    {
        $getAllRating = new OrderReview();
        $allData = $getAllRating
            ->limit(5)
            ->orderBy('created_at', 'desc')
            ->get();
        $userReviewList = [];
        if ($allData->count() > 0) {
            foreach ($allData as $key => $data) {
                $userReviewList[$key]['first_name'] = $data->userDetails->first_name;
                $starRating = 0.0;
                if ($data->avg_rating == 0.5 || $data->avg_rating == 1.5 || $data->avg_rating == 2.5 || $data->avg_rating == 3.5 || $data->avg_rating == 4.5) {
                    $starRating = $data->avg_rating;
                } else {
                    $starRating = (float) number_format($data->avg_rating, 1);
                }
                $userReviewList[$key]['avg_star_rating'] = $starRating;
                $userReviewList[$key]['short_review'] = $data->short_review;
                $userReviewList[$key]['reviewed_on'] = date('d.m.Y H:i', strtotime($data->created_at));
            }
        }

        // All rating
        $sumOfAllRating = $getAllRating->sum('avg_rating');
        $countOfAllRating = $getAllRating->count();

        $avgAllRating = $starAvgAllRating = 0.0;
        if ($countOfAllRating > 0) {
            $avgAllRating = $sumOfAllRating / $countOfAllRating;
            $starAvgAllRating = $avgAllRating;
            if ($avgAllRating == 0.5 || $avgAllRating == 1.5 || $avgAllRating == 2.5 || $avgAllRating == 3.5 || $avgAllRating == 4.5) {
                $staravgAllRating = $avgAllRating;
            } else {
                $starAvgAllRating = round($avgAllRating);
                $avgOverallRating = (float) number_format($avgAllRating, 1);
            }
        }
        $rating['starAvgAllRating'] = $starAvgAllRating;
        $rating['avgAllRating'] = $avgAllRating;

        // Overall rating
        $getOverallRating = OrderReview::whereDate('created_at', '>', Carbon::now()->subDays(90));
        $sumOfRating = $getOverallRating->sum('avg_rating');
        $countOfOverallRating = $getOverallRating->count();

        $avgOverallRating = $starAvgOverallRating = 0.0;
        if ($countOfOverallRating > 0) {
            $avgOverallRating = $sumOfRating / $countOfOverallRating;
            $starAvgOverallRating = $avgOverallRating;
            if ($avgOverallRating == 0.5 || $avgOverallRating == 1.5 || $avgOverallRating == 2.5 || $avgOverallRating == 3.5 || $avgOverallRating == 4.5) {
                $starAvgOverallRating = $avgOverallRating;
            } else {
                $starAvgOverallRating = round($avgOverallRating);
                $avgOverallRating = (float) number_format($avgOverallRating, 1);
            }
        }
        $rating['starAvgOverallRating'] = $starAvgOverallRating;
        $rating['avgOverallRating'] = $avgOverallRating;

        // Below section calculation
        $totalReviews = OrderReview::get();

        $total5Star = $total4Star = $total3Star = $total2Star = $total1Star = 0;
        $total5StarPercent = $total4StarPercent = $total3StarPercent = $total2StarPercent = $total1StarPercent = 0;
        $totalFoodQualityRating = $totalDeliveryTimeRating = $totalDriverFriendlinessRating = 0;

        $rating['starAvgFoodDeliveryRating'] = $rating['avgFoodDeliveryRating'] = $rating['starAvgDeliveryTimeRating'] = $rating['avgDeliveryTimeRating'] = $rating['starAvgDriverFriendlinessRating'] = $rating['avgDriverFriendlinessRating'] = 0;

        if ($totalReviews->count() > 0) {
            foreach ($totalReviews as $review) {
                if ($review->avg_rating == 5) {
                    $total5Star++;
                }
                if ($review->avg_rating == 4) {
                    $total4Star++;
                }
                if ($review->avg_rating == 3) {
                    $total3Star++;
                }
                if ($review->avg_rating == 2) {
                    $total2Star++;
                }
                if ($review->avg_rating == 1) {
                    $total1Star++;
                }

                $totalFoodQualityRating += $review->food_quality;
                $totalDeliveryTimeRating += $review->delivery_time;
                $totalDriverFriendlinessRating += $review->driver_friendliness;
            }

            // Percent calculation
            if ($total5Star != 0) {
                $total5StarPercent = round(($total5Star / $totalReviews->count()) * 100);
            }
            if ($total4Star != 0) {
                $total4StarPercent = round(($total4Star / $totalReviews->count()) * 100);
            }
            if ($total3Star != 0) {
                $total3StarPercent = round(($total3Star / $totalReviews->count()) * 100);
            }
            if ($total2Star != 0) {
                $total2StarPercent = round(($total2Star / $totalReviews->count()) * 100);
            }
            if ($total1Star != 0) {
                $total1StarPercent = round(($total1Star / $totalReviews->count()) * 100);
            }

            // For individual type
            $avgFoodDeliveryRating = $avgDeliveryTimeRating = $avgDriverFriendlinessRating = $starAvgFoodDeliveryRating = $starAvgDeliveryTimeRating = $starAvgDriverFriendlinessRating = 0.0;
            if ($totalReviews->count() > 0) {
                // Food quality
                $avgFoodDeliveryRating = $totalFoodQualityRating / $totalReviews->count();
                $starAvgFoodDeliveryRating = $avgFoodDeliveryRating;
                if ($avgFoodDeliveryRating == 0.5 || $avgFoodDeliveryRating == 1.5 || $avgFoodDeliveryRating == 2.5 || $avgFoodDeliveryRating == 3.5 || $avgOverallRating == 4.5) {
                    $starAvgFoodDeliveryRating = $avgFoodDeliveryRating;
                } else {
                    $starAvgFoodDeliveryRating = round($avgFoodDeliveryRating);
                    $avgFoodDeliveryRating = (float) number_format($avgFoodDeliveryRating, 1);
                }
                $rating['starAvgFoodDeliveryRating'] = $starAvgFoodDeliveryRating;
                $rating['avgFoodDeliveryRating'] = $avgFoodDeliveryRating;

                // Delivery Time
                $avgDeliveryTimeRating = $totalDeliveryTimeRating / $totalReviews->count();
                $starAvgDeliveryTimeRating = $avgDeliveryTimeRating;
                if ($avgDeliveryTimeRating == 0.5 || $avgDeliveryTimeRating == 1.5 || $avgDeliveryTimeRating == 2.5 || $avgDeliveryTimeRating == 3.5 || $avgOverallRating == 4.5) {
                    $starAvgDeliveryTimeRating = $avgDeliveryTimeRating;
                } else {
                    $starAvgDeliveryTimeRating = round($avgDeliveryTimeRating);
                    $avgDeliveryTimeRating = (float) number_format($avgDeliveryTimeRating, 1);
                }
                $rating['starAvgDeliveryTimeRating'] = $starAvgDeliveryTimeRating;
                $rating['avgDeliveryTimeRating'] = $avgDeliveryTimeRating;

                // Driver Friendliness
                $avgDriverFriendlinessRating = $totalDriverFriendlinessRating / $totalReviews->count();
                $starAvgDriverFriendlinessRating = $avgDriverFriendlinessRating;
                if ($avgDriverFriendlinessRating == 0.5 || $avgDriverFriendlinessRating == 1.5 || $avgDriverFriendlinessRating == 2.5 || $avgDriverFriendlinessRating == 3.5 || $avgOverallRating == 4.5) {
                    $starAvgDriverFriendlinessRating = $avgDriverFriendlinessRating;
                } else {
                    $starAvgDriverFriendlinessRating = round($avgDriverFriendlinessRating);
                    $avgDriverFriendlinessRating = (float) number_format($avgDriverFriendlinessRating, 1);
                }
                $rating['starAvgDriverFriendlinessRating'] = $starAvgDriverFriendlinessRating;
                $rating['avgDriverFriendlinessRating'] = $avgDriverFriendlinessRating;
            }
        }

        $rating['totalReviews'] = $totalReviews;
        $rating['total5StarPercent'] = $total5StarPercent;
        $rating['total4StarPercent'] = $total4StarPercent;
        $rating['total3StarPercent'] = $total3StarPercent;
        $rating['total2StarPercent'] = $total2StarPercent;
        $rating['total1StarPercent'] = $total1StarPercent;
        $rating['userReviewList'] = $userReviewList;

        return $rating;
    }

    /*****************************************************/
    # Function name : gettingShopStatus
    # Params        :
    /*****************************************************/
    public static function gettingShopStatusFlagPlaceOrder()
    {
        $dayName = date('l');
        $siteSettings = self::getSiteSettings();
        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay_display) ? $siteSettings->min_delivery_delay_display : 0;
        $currentTimeStamp = !empty(env('CTIME')) ? strtotime(env('CTIME')) : strtotime(date('H:i'));
        $currentTimeStamp = date('H:i', strtotime("+$minimumDeliveryDelayTime minutes", $currentTimeStamp));
        $currentTimeStamp = strtotime($currentTimeStamp);
        $currentDate = date('Y-m-d');

        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
        $deliveryStartTime = $currentTimeStamp;
        $shopStatus = 0;

        // Start :: special hour
        $availability = SpecialHour::where('special_date', $currentDate)->first();
        if ($availability == null) {
            $availability = DeliverySlot::where('day_title', $dayName)->first();
        }
        // End :: special hour

        if ($availability != null) {
            if ($availability->holiday == '0') {
                if ($availability->start_time2 != null && $availability->end_time2 != null) {
                    if (($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) || ($deliveryStartTime >= strtotime($availability->start_time2) && $deliveryStartTime <= strtotime($availability->end_time2))) {
                        $shopStatus = 1;
                    }
                } else {
                    if ($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) {
                        $shopStatus = 1;
                    }
                }
            }
        }
        return $shopStatus;
    }

    public static function gettingShopStatus()
    {
        $dayName = date('l');
        $currentTimeStamp = strtotime(date('H:i'));
        $currentDate = date('Y-m-d');
        $siteSettings = self::getSiteSettings();

        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
        // Current time + minimum delivery delay = Delivery start time
        // $deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);
        $deliveryStartTime = $currentTimeStamp;
        $shop = trans('custom.label_close');

        // Start :: special hour
        $availability = SpecialHour::where('special_date', $currentDate)->first();
        if ($availability == null) {
            $availability = DeliverySlot::where('day_title', $dayName)->first();
        }
        // End :: special hour

        if ($availability != null) {
            if ($availability->holiday == '0') {
                if ($availability->start_time2 != null && $availability->end_time2 != null) {
                    if (($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) || ($deliveryStartTime >= strtotime($availability->start_time2) && $deliveryStartTime <= strtotime($availability->end_time2))) {
                        $shop = trans('custom.label_open');
                    }
                } else {
                    if ($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) {
                        $shop = trans('custom.label_open');
                    }
                }
            }
        }
        return $shop;
    }

    /*****************************************************/
    # Function name : gettingShopStatusFlag
    # Params        :
    /*****************************************************/
    public static function gettingShopStatusFlag()
    {
        $dayName = date('l');
        $currentTimeStamp = !empty(env('CTIME')) ? strtotime(env('CTIME')) : strtotime(date('H:i'));
        $currentDate = date('Y-m-d');
        $siteSettings = self::getSiteSettings();

        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
        $deliveryStartTime = $currentTimeStamp;
        $shopStatus = 0;

        // Start :: special hour
        $availability = SpecialHour::where('special_date', $currentDate)->first();
        if ($availability == null) {
            $availability = DeliverySlot::where('day_title', $dayName)->first();
        }
        // End :: special hour

        if ($availability != null) {
            if ($availability->holiday == '0') {
                if ($availability->start_time2 != null && $availability->end_time2 != null) {
                    if (($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) || ($deliveryStartTime >= strtotime($availability->start_time2) && $deliveryStartTime <= strtotime($availability->end_time2))) {
                        $shopStatus = 1;
                    }
                } else {
                    if ($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time)) {
                        $shopStatus = 1;
                    }
                }
            }
        }
        return $shopStatus;
    }
    public static function gettingShopStatusFlagOld()
    {
        $dayName = date('l');
        $currentTimeStamp = strtotime(date('H:i'));
        $currentTimeStamp = !empty(env('CTIME')) ? strtotime(env('CTIME')) : $currentTimeStamp;
        $currentDate = date('Y-m-d');
        $siteSettings = self::getSiteSettings();

        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay_display) ? $siteSettings->min_delivery_delay_display : 0;
        $deliveryStartTime = $currentTimeStamp;

        //$minimumDeliveryDelayTime=45;
        // current time + minimum delivery delay = Delivery start time
        //$deliveryStartTime = strtotime("+".$minimumDeliveryDelayTime." minutes", $currentTimeStamp);

        $shopStatus = 0;

        // Start :: special hour
        $availability = SpecialHour::where('special_date', $currentDate)->first();
        if ($availability == null) {
            $availability = DeliverySlot::where('day_title', $dayName)->first();
        }
        // End :: special hour
        $deliveryStartTime1 = strtotime('+' . $minimumDeliveryDelayTime . ' minutes', $currentTimeStamp);
        //echo date('H:i',$deliveryStartTime); die;
        if ($availability != null) {
            if ($availability->holiday == '0') {
                if ($availability->start_time2 != null && $availability->end_time2 != null) {
                    if (($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time) && $deliveryStartTime1 <= strtotime($availability->end_time)) || ($deliveryStartTime >= strtotime($availability->start_time2) && $deliveryStartTime <= strtotime($availability->end_time2))) {
                        if (self::diffinminut(strtotime($availability->end_time)) == 0) {
                            $shopStatus = 0;
                        } else {
                            $shopStatus = 1;
                        }
                    }
                } else {
                    if ($deliveryStartTime >= strtotime($availability->start_time) && $deliveryStartTime <= strtotime($availability->end_time) && $deliveryStartTime1 <= strtotime($availability->end_time)) {
                        $shopStatus = 1;
                    }
                }
            }
        }
        return $shopStatus;
    }

    /*****************************************************/
    # Function name : insertNotification
    # Params        : $userId
    /*****************************************************/
    public static function insertNotification($userId = null)
    {
        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->order_update = '1';
        $notification->rate_your_meal = '1';
        $notification->sms = '1';
        $notification->save();

        return true;
    }

    /*****************************************************/
    # Function name : getCartItemDetailsInCoupon
    # Params        :
    /*****************************************************/
    public static function getCartItemDetailsInCoupon()
    {
        $cartSessionId = '';
        if (Session::get('cartSessionId') != '') {
            $cartSessionId = Session::get('cartSessionId');
        }

        $cartUserId = 0;
        $totalCartCount = 0;
        $getCartData = [];
        if (Auth::user()) {
            $cartUserId = Auth::user()->id;
            $cartConditions = ['user_id' => $cartUserId, 'type' => 'C'];
        } else {
            $cartConditions = ['session_id' => $cartSessionId, 'type' => 'C'];
        }

        $getOrderDetails = Order::where($cartConditions)->first();

        $cartArray = [];
        $totalCartPrice = $cartOrderId = $paymentMethod = $productExistFlag = $deliveryCharges = 0;

        if ($getOrderDetails != null) {
            $cartOrderId = $getOrderDetails->id;
            $paymentMethod = $getOrderDetails->payment_method;
            $deliveryCharges = $getOrderDetails->delivery_charge;
            // Main Cart array
            if (isset($getOrderDetails->orderDetails) && count($getOrderDetails->orderDetails) > 0) {
                $i = 0;
                foreach ($getOrderDetails->orderDetails as $orderDetails) {
                    $productImage = '';
                    $vatAmount = 0;
                    $cartArray[$i]['id'] = $orderDetails->id;
                    $cartArray[$i]['order_id'] = $orderDetails->order_id;
                    $cartArray[$i]['product_id'] = $orderDetails->product_id;
                    $cartArray[$i]['drink_id'] = $orderDetails->drink_id;
                    $cartArray[$i]['special_menu_id'] = $orderDetails->special_menu_id;
                    $cartArray[$i]['has_ingredients'] = $orderDetails->has_ingredients;
                    $cartArray[$i]['has_attribute'] = $orderDetails->has_attribute;
                    $cartArray[$i]['attribute_id'] = $orderDetails->attribute_id;
                    $cartArray[$i]['is_menu'] = $orderDetails->is_menu;
                    $cartArray[$i]['quantity'] = $orderDetails->quantity;
                    $cartArray[$i]['price'] = $orderDetails->price;
                    $cartArray[$i]['total_price'] = $orderDetails->total_price;

                    // order product locals
                    if (count($orderDetails->orderDetailLocals) > 0) {
                        foreach ($orderDetails->orderDetailLocals as $detailLocal) {
                            $cartArray[$i]['local_details'][$detailLocal->lang_code]['local_title'] = $detailLocal->local_title;
                            if ($orderDetails->is_menu == 'Y') {
                                $menuValueDetails = json_decode($detailLocal->local_drop_down_menu_title_value, true);
                                if (!empty($menuValueDetails)) {
                                    foreach ($menuValueDetails as $keyMenuDtls => $menuValDtls) {
                                        $cartArray[$i]['menu_title_value_local_details'][$detailLocal->lang_code][$keyMenuDtls]['menu_local_title'] = $menuValDtls['menu_title'];
                                        $cartArray[$i]['menu_title_value_local_details'][$detailLocal->lang_code][$keyMenuDtls]['menu_local_value'] = implode('#', $menuValDtls['menu_value']);
                                    }
                                } else {
                                    $cartArray[$i]['menu_title_value_local_details'] = [];
                                }
                            } else {
                                $cartArray[$i]['menu_title_value_local_details'] = [];
                            }
                        }
                    } else {
                        $cartArray[$i]['local_details'] = [];
                        $cartArray[$i]['menu_title_value_local_details'] = [];
                    }

                    // ingredients locals
                    if (count($orderDetails->orderIngredients) > 0) {
                        foreach ($orderDetails->orderIngredients as $key => $orderIngredient) {
                            $cartArray[$i]['ingredient_local_details'][$key]['order_ingredient_id'] = $orderIngredient->id;
                            $cartArray[$i]['ingredient_local_details'][$key]['ingredient_id'] = $orderIngredient->ingredient_id;
                            $cartArray[$i]['ingredient_local_details'][$key]['product_id'] = $orderIngredient->product_id;
                            $cartArray[$i]['ingredient_local_details'][$key]['quantity'] = $orderIngredient->quantity;
                            $cartArray[$i]['ingredient_local_details'][$key]['price'] = $orderIngredient->price;
                            $cartArray[$i]['ingredient_local_details'][$key]['total_price'] = $orderIngredient->total_price;
                            foreach ($orderIngredient->orderIngredientLocals as $detailIngredientLocal) {
                                $cartArray[$i]['ingredient_local_details'][$key][$detailIngredientLocal->lang_code]['local_title'] = $detailIngredientLocal->local_ingredient_title;
                            }
                        }
                    } else {
                        $cartArray[$i]['ingredient_local_details'] = [];
                    }

                    // attributes locals
                    if ($orderDetails->has_attribute == 'Y') {
                        $orderAttributeLocalDetails = OrderAttributeLocal::where([
                            'order_id' => $orderDetails->order_id,
                            'order_details_id' => $orderDetails->id,
                            'product_id' => $orderDetails->product_id,
                            'attribute_id' => $orderDetails->attribute_id,
                        ])->get();

                        foreach ($orderAttributeLocalDetails as $key => $detailAttributeLocal) {
                            $cartArray[$i]['attribute_local_details'][$detailAttributeLocal->lang_code]['local_title'] = $detailAttributeLocal->local_attribute_title;
                        }
                    } else {
                        $cartArray[$i]['attribute_local_details'] = [];
                    }

                    if ($orderDetails->product_id != '') {
                        $productExistFlag = 1;
                    }

                    //Total price
                    $totalCartPrice += $orderDetails->total_price;
                    $i++;
                }
            }
        }

        $totalCartCount = count($cartArray);
        $cartDetailArrayCoupon = [
            'cartOrderId' => $cartOrderId,
            'productExist' => $productExistFlag,
            'itemDetails' => $cartArray,
            'totalItem' => $totalCartCount,
            'totalCartPrice' => (float) self::formatToTwoDecimalPlaces($totalCartPrice),
            'payableAmount' => (float) self::formatToTwoDecimalPlaces($totalCartPrice),
            'deliveryCharges' => (float) self::formatToTwoDecimalPlaces($deliveryCharges),
            'paymentMethod' => $paymentMethod,
        ];

        return $cartDetailArrayCoupon;
    }

    /*****************************************************/
    # Function name : couponCalculation
    # Params        : $userId
    /*****************************************************/
    public static function couponCalculation($currentTime = null, $appliedCouponCode = null, $deliveryChargeAmount = 0, $cardPayAmountStatus = false)
    {
        $couponCode = $appliedCouponCode;
        $deliveryCharge = $deliveryChargeAmount;
        $couponDiscountAmount = $discountAmount = 0;
        $now = $currentTime;
        $cartDetails = self::getCartItemDetailsInCoupon();
        $couponDetails = null;
        $response['coupon_details'] = [];

        $netPayableAmount = $cartDetails['payableAmount'] + $deliveryCharge;
        // $cardAmount             = (($netPayableAmount * 2.9) / 100) + 0.30;
        //$cardAmount             = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
        $cardAmount = self::paymentCardFee($netPayableAmount);

        if ($couponCode != null) {
            $conditions[] = ['status', '1'];
            $conditions[] = ['code', $couponCode];
            $conditions[] = ['start_time', '<=', $now];

            $couponDetails = $couponData = Coupon::whereNull('deleted_at')
                ->where($conditions)
                ->first();
            // dd($couponData);
            $process = true;
            if ($couponData != null) {
                // coupon details
                $response['coupon_details']['id'] = $couponDetails->id;
                $response['coupon_details']['code'] = $couponDetails->code;
                $response['coupon_details']['has_minimum_cart_amount'] = $couponDetails->has_minimum_cart_amount;
                $response['coupon_details']['cart_amount'] = $couponDetails->cart_amount;
                $response['coupon_details']['discount_type'] = $couponDetails->discount_type;
                $response['coupon_details']['amount'] = $couponDetails->amount;
                $response['coupon_details']['start_time'] = date('Y-m-d H:i', $couponDetails->start_time);
                $response['coupon_details']['end_time'] = $couponDetails->end_time != null ? date('Y-m-d H:i', $couponDetails->end_time) : null;

                if ($couponData->end_time != null) {
                    if ($now > $couponData->end_time) {
                        $process = false;
                    }
                }
                // process to apply coupon
                if ($process) {
                    $payableAmount = $cartDetails['payableAmount'] + $deliveryCharge;
                    $couponAmount = isset($couponData->amount) ? $couponData->amount : '';

                    if ($couponAmount != '') {
                        // If discount type = Percentage
                        if ($couponData->discount_type == 'P') {
                            $discountAmount = ($payableAmount * $couponData->amount) / 100;
                        } else {
                            // discount type = Flat
                            $discountAmount = $couponData->amount;
                        }

                        // related to minimum cart
                        if ($couponData->has_minimum_cart_amount == 'Y') {
                            if ($cartDetails['payableAmount'] > $couponData->cart_amount) {
                                // checking discount amount is greater than payable amount or not
                                if ($discountAmount > $payableAmount) {
                                    $couponDiscountAmount = $discountAmount = $payableAmount;
                                }

                                $discountAmount = Helper::formatToTwoDecimalPlaces($discountAmount);
                                $couponDiscountAmount = Helper::formatToTwoDecimalPlaces($discountAmount);
                                $netPayableAmount = $payableAmount - $couponDiscountAmount;

                                // $cardAmount             = (($netPayableAmount * 2.9) / 100) + 0.30;
                                //  $cardAmount             = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
                                $cardAmount = self::paymentCardFee($netPayableAmount);
                            }
                        } else {
                            // checking discount amount is greater than payable amount or not
                            if ($discountAmount > $payableAmount) {
                                $couponDiscountAmount = $discountAmount = $payableAmount;
                            }

                            $discountAmount = Helper::formatToTwoDecimalPlaces($discountAmount);
                            $couponDiscountAmount = Helper::formatToTwoDecimalPlaces($discountAmount);
                            $netPayableAmount = $payableAmount - $couponDiscountAmount;

                            // $cardAmount             = (($netPayableAmount * 2.9) / 100) + 0.30;
                            // $cardAmount             = (($netPayableAmount + 0.30) / 0.971) - $netPayableAmount;
                            $cardAmount = self::paymentCardFee($netPayableAmount);
                        }
                    }
                }
            }
        }

        if ($cardPayAmountStatus == false) {
            $cardAmount = 0;
        }

        $response['calculated_card_amount'] = Helper::formatToTwoDecimalPlaces($cardAmount);
        $response['calculated_discount_amount'] = Helper::formatToTwoDecimalPlaces($discountAmount);

        return $response;
    }

    /*****************************************************/
    # Function name : priceRoundOff
    # Params        : $price
    /*****************************************************/
    public static function priceRoundOff(float $price)
    {
        $price = number_format((float) $price, 2, '.', '');

        $priceArr = explode('.', $price);
        $beforeDecimal = $priceArr[0];
        $afterDecimal = $priceArr[1];
        $lastDigit = substr($afterDecimal, -1);
        $lastDigit1 = substr($afterDecimal, -1);
        $firstDigit = substr($afterDecimal, 0, 1);

        if ($lastDigit >= 3 && $lastDigit <= 7) {
            $lastDigit = 5;
            $price = $beforeDecimal . '.' . $firstDigit . $lastDigit;
        } else {
            $price = number_format((float) $price, 1, '.', '');
            $price = number_format((float) $price, 2, '.', '');
        }
        return $price;
    }

    /*****************************************************/
    # Function name : specialHourCalculation
    # Params        : $receivingDate
    /*****************************************************/
    public static function specialHourCalculation($receivingDate)
    {
        $specialHour = SpecialHour::where(['special_date' => $receivingDate])->first();
        return $specialHour;
    }

    public static function diffinminut($lasttime)
    {
        $start = strtotime(date('H:i'));
        $current_time = !empty(env('CTIME')) ? strtotime(env('CTIME')) : $start;
        $end = $lasttime;
        $mins = ($end - $current_time) / 60;
        return $mins;
    }

    /**
     * function
     */
    public static function cehckTimeOncheckout($timecheck, $date)
    {
        $ar = self::generateDeliverySlotNew('sloat', $date);
        if ($ar) {
            if (in_array($timecheck, $ar)) {
                return true;
            }
        }
        return false;
        exit();
    }

    /**
     * Shanti info
     */
    public static function SplitTime($StartTime, $EndTime, $Duration = '15', $ccdate = '')
    {
        $ctime = !empty(env('CTIME')) ? env('CTIME') : date('H:i');
        //  $min=30;
        $siteSettings = self::getSiteSettings();
        $min = isset($siteSettings->min_delivery_delay_display) ? $siteSettings->min_delivery_delay_display : 0;
        $opentime = $StartTime;
        if ($EndTime == '23:59') {
            $EndTime = '24:00:00';
        }
        $closetime = $EndTime;
        $ReturnArray = $options = []; // Define output
        $StartTime = strtotime($StartTime);

        $EndTime = strtotime($EndTime);
        $AddMins = $Duration * 60;
        $greater = date('H:i', strtotime("+$min minutes", strtotime($ctime)));
        $assson = '';
        $k = 0;
        $tdate = date('Y-m-d');
        $ccdate = !empty($ccdate) ? $ccdate : $tdate;
        while ($StartTime <= $EndTime) {
            //Run loop
            if ($ccdate == $tdate && $StartTime >= strtotime($greater)) {
                if (strtotime($opentime) <= strtotime($ctime) && strtotime($ctime) <= strtotime($closetime) && $k < 1) {
                    $ReturnArray[] = date('H:i', $StartTime);
                    $selected = 'selected';
                    $values = trans('custom.label_as_soon_as_possible');
                    $asSoon = 'Y';
                    $options[] = "<option data-assoon='Y' value='" . date('H:i', $StartTime) . "' $selected>$values</option>";
                }
                if (strtotime($greater) != $StartTime) {
                    $ReturnArray[] = date('H:i', $StartTime);
                    $options[] = '<option>' . date('H:i', $StartTime) . '</option>';
                }
                $k++;
            } elseif ($ccdate != $tdate) {
                $ReturnArray[] = date('H:i', $StartTime);
                $options[] = '<option>' . date('H:i', $StartTime) . '</option>';
                $k++;
            }
            $StartTime += $AddMins; //Endtime check
        }
        $res['sloat'] = array_unique($ReturnArray);
        $res['options'] = array_unique($options);
        return $res;
    }

    /**
     * Get Sloats New function
     */
    public static function generateDeliverySlotNew($type = 'sloat', $cdates = '')
    {
        //echo env('ORDER_EMAIL'); die;
        $siteSettings = self::getSiteSettings();

        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
        $currentDate = !empty($cdates) ? $cdates : date('Y-m-d');
        $today = !empty($cdates) ? date('l', strtotime($cdates)) : date('l');
        $todayno = !empty($cdates) ? date('D', strtotime($cdates)) : date('d');
        // Start :: special hour
        $shopOpenCloseTimeAccordingToDay = SpecialHour::where('special_date', $currentDate)->first();
        if ($shopOpenCloseTimeAccordingToDay == null) {
            $shopOpenCloseTimeAccordingToDay = DeliverySlot::where('day_title', $today)->first();
        }
        // End :: special hour
        $slots = $option = [];
        // If not holiday

        if ($siteSettings->is_shop_close == 'Y' && $siteSettings->shop_close_date == $currentDate) {
            return '';
        }

        if ($shopOpenCloseTimeAccordingToDay->holiday == 0) {
            $dataall = DB::table('delivery_slots_final')
                ->where('day', $shopOpenCloseTimeAccordingToDay->id)
                ->get();
            if ($dataall) {
                $nextday = 7;
                if ($shopOpenCloseTimeAccordingToDay->id > 1) {
                    $nextday = $shopOpenCloseTimeAccordingToDay->id - 1;
                }
                $nextDetails = DB::table('delivery_slots_final')
                    ->where('day', $nextday)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($nextDetails) {
                    $nextendtime = $nextDetails->end_time;
                    $nextstarttime = $nextDetails->start_time;
                    if (strtotime($nextendtime) < strtotime($nextstarttime)) {
                        $exclosetime = date('H:i', strtotime($nextendtime));
                        $exopentime = date('H:i', strtotime('00:00'));
                        $DataTimes = self::SplitTime("$exopentime", "$exclosetime", '15', $cdates);
                        $slotss1 = !empty($DataTimes['sloat']) ? $DataTimes['sloat'] : [];
                        $optionss = !empty($DataTimes['options']) ? $DataTimes['options'] : [];
                        if ($slotss1) {
                            $slots = array_merge($slots, $slotss1);
                            $option = array_merge($option, $optionss);
                        }
                    }
                }
                foreach ($dataall as $list) {
                    // $shopCloseTime      = date('H:i', strtotime($list->end_time));
                    // $shopOpenTime     = date('H:i', strtotime($list->start_time));
                    if (strtotime($list->end_time) < strtotime($list->start_time)) {
                        $list->end_time = '23:59:59';
                    }
                    $shopCloseTime = date('H:i', strtotime($list->end_time));
                    $shopOpenTime = date('H:i', strtotime($list->start_time));
                    $DataTimes = self::SplitTime("$shopOpenTime", "$shopCloseTime", '15', $cdates);
                    $slotss = !empty($DataTimes['sloat']) ? $DataTimes['sloat'] : [];
                    $options = !empty($DataTimes['options']) ? $DataTimes['options'] : [];
                    if ($slotss) {
                        $slots = array_merge($slots, $slotss);
                        $option = array_merge($option, $options);
                    }
                }
            }
        }
        if ($type == 'sloat') {
            $slots = array_unique($slots);
            return $slots;
        }
        if ($option) {
            $option = array_unique($option);
            echo implode('', $option);
        }
        return '';
    }
    public static function generateDeliverySlotNew_old($type = 'sloat', $cdates = '')
    {
        $siteSettings = self::getSiteSettings();

        $minimumDeliveryDelayTime = isset($siteSettings->min_delivery_delay) ? $siteSettings->min_delivery_delay : 0;
        $currentDate = !empty($cdates) ? $cdates : date('Y-m-d');
        $today = !empty($cdates) ? date('l', strtotime($cdates)) : date('l');
        // Start :: special hour
        $shopOpenCloseTimeAccordingToDay = SpecialHour::where('special_date', $currentDate)->first();
        if ($shopOpenCloseTimeAccordingToDay == null) {
            $shopOpenCloseTimeAccordingToDay = DeliverySlot::where('day_title', $today)->first();
        }
        // End :: special hour
        $slots = $option = [];
        // If not holiday

        if ($shopOpenCloseTimeAccordingToDay->holiday == 0 && $siteSettings->is_shop_close != 'Y') {
            /**
             * sp2
             */
            $next = !empty($cdates) ? $cdates : date('Y-m-d');
            $next = date('Y-m-d', strtotime('-1 day', strtotime($next)));
            $nextd = !empty($next) ? date('l', strtotime($next)) : date('l');
            $shopOpenCloseTimeAccordingToDay_ex = SpecialHour::where('special_date', $next)->first();
            if ($shopOpenCloseTimeAccordingToDay_ex == null) {
                $shopOpenCloseTimeAccordingToDay_ex = DeliverySlot::where('day_title', $nextd)->first();
            }

            if (strtotime($shopOpenCloseTimeAccordingToDay_ex->end_time2) < strtotime($shopOpenCloseTimeAccordingToDay_ex->start_time2)) {
                $shopCloseTime1 = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay_ex->end_time2));
                $shopOpenTime1 = date('H:i', strtotime('00:00'));
                $DataTimes = self::SplitTime("$shopOpenTime1", "$shopCloseTime1", '15', $cdates);
                $slotss1 = !empty($DataTimes['sloat']) ? $DataTimes['sloat'] : [];
                $optionss = !empty($DataTimes['options']) ? $DataTimes['options'] : [];
                if ($slotss1) {
                    $slots = array_merge($slots, $slotss1);
                    $option = array_merge($option, $optionss);
                }
            }

            $realend = '';
            if ($shopOpenCloseTimeAccordingToDay->start_time2 != null) {
                if (strtotime($shopOpenCloseTimeAccordingToDay->start_time2) > strtotime($shopOpenCloseTimeAccordingToDay->end_time2)) {
                    $realend = $shopOpenCloseTimeAccordingToDay->end_time2;
                    $shopOpenCloseTimeAccordingToDay->end_time2 = '23:59:59';
                }
            }

            $shopCloseTime = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->end_time));
            $shopOpenTime = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time));
            $DataTimes = self::SplitTime("$shopOpenTime", "$shopCloseTime", '15', $cdates);
            $slotss = !empty($DataTimes['sloat']) ? $DataTimes['sloat'] : [];
            $options = !empty($DataTimes['options']) ? $DataTimes['options'] : [];
            if ($slotss) {
                $slots = array_merge($slots, $slotss);
                $option = array_merge($option, $options);
            }
            //print_r($slots); die;
            if ($shopOpenCloseTimeAccordingToDay->start_time2 != null && $shopOpenCloseTimeAccordingToDay->end_time2 != null) {
                $shopCloseTime = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->end_time2));
                $shopOpenTime = date('H:i', strtotime($shopOpenCloseTimeAccordingToDay->start_time2));
                $DataTimes = self::SplitTime("$shopOpenTime", "$shopCloseTime", '15', $cdates);
                $slotss = !empty($DataTimes['sloat']) ? $DataTimes['sloat'] : [];
                $options = !empty($DataTimes['options']) ? $DataTimes['options'] : [];
                if ($slotss) {
                    $slots = array_merge($slots, $slotss);
                    $option = array_merge($option, $options);
                }
            }
        }
        if ($type == 'sloat') {
            return $slots;
        }
        if ($option) {
            echo implode('', $option);
        }
        return '';
    }

    /**
     * Splite Extra Time (Shanti Info)
     */
    public static function SplitTimeext($StartTime, $EndTime, $Duration = '60', $type = '')
    {
        $ReturnArray = $options = []; // Define output
        $StartTime = strtotime($StartTime); //Get Timestamp
        $EndTime = strtotime($EndTime); //Get Timestamp
        $AddMins = $Duration * 60;
        while ($StartTime <= $EndTime) {
            //Run loop
            if ($type) {
                $time = strtotime(date('H:i'));
                $stdate = strtotime(date('H:i', $StartTime));
                if ($time < $stdate) {
                    $ReturnArray[] = date('H:i', $StartTime);
                    $options[] = '<option>' . date('H:i', $StartTime) . '</option>';
                    // $StartTime += $AddMins; //Endtime check
                }
            } else {
                $ReturnArray[] = date('H:i', $StartTime);
                $options[] = '<option>' . date('H:i', $StartTime) . '</option>';
                //Endtime check
            }
            $StartTime += $AddMins;
        }
        $res['sloat'] = $ReturnArray;
        $res['options'] = $options;
        return $res;
        //return $ReturnArray;
    }

    /**
     * Get setting Image (Shanti Info)
     */
    public static function getSettingImage($type = 'logo', $site = '')
    {
        $siteSettings = SiteSetting::first();
        self::autoActiveShop($siteSettings);
        if ($type == 'logo') {
            $image = $siteSettings->logo;
            $imagePath = public_path('uploads/site/logo/') . $image;
            if ($image) {
                $imagePath = URL::to('/') . '/uploads/site/logo/' . $image;
                return $imagePath;
            }
            if ($site == 'admin') {
                return '';
            }
            return URL::to('/') . '/images/site/logo.png';
        }
        if ($type == 'png_logo') {
            $image = $siteSettings->logo_png;
            $imagePath = public_path('uploads/site/logo/') . $image;
            if ($image) {
                $imagePath = URL::to('/') . '/uploads/site/logo/' . $image;
                return $imagePath;
            }
            if ($site == 'admin') {
                return '';
            }
            return URL::to('/') . '/images/site/logo_top.png';
        }
        if ($type == 'header') {
            $image = $siteSettings->header_picture;
            $imagePath = public_path('uploads/site/header/') . $image;
            if ($image) {
                $imagePath = URL::to('/') . '/uploads/site/header/' . $image;
                return $imagePath;
            }
            if ($site == 'admin') {
                return '';
            }
            return URL::to('/') . '/images/site/sample/banner.jpg';
        }
        if ($type == 'adv_en') {
            $image = $siteSettings->advertisement_banner_en;
            $imagePath = public_path('uploads/advertisement/en/') . $image;
            if ($image) {
                $imagePath = URL::to('/') . '/uploads/advertisement/en/' . $image;
                return $imagePath;
            }
        }

        if ($type == 'adv_de') {
            $image = $siteSettings->advertisement_banner_de;
            $imagePath = public_path('uploads/advertisement/de/') . $image;
            if ($image) {
                $imagePath = URL::to('/') . '/uploads/advertisement/de/' . $image;
                return $imagePath;
            }
        }
    }

    /**
     * Auto Active Payment Gateway
     */
    public static function autoActiveShop($siteSettings)
    {
        $is_shop_close = !empty($siteSettings->is_shop_close) ? $siteSettings->is_shop_close : '';
        if ($is_shop_close == 'Y') {
            $date = $siteSettings->shop_close_date;
            $todaydate = date('Y-m-d');
            if ($todaydate != $date) {
                $siteSettings = SiteSetting::first();
                $siteSettings->is_shop_close = 'N';
                $siteSettings->shop_close_date = null;
                $siteSettings->save();
            }
        }
        $is_pickup_close = !empty($siteSettings->is_pickup_close) ? $siteSettings->is_pickup_close : '';
        if ($is_pickup_close == 'Y') {
            $date = $siteSettings->pickup_close_date;
            $todaydate = date('Y-m-d');
            if ($todaydate != $date) {
                $siteSettings = SiteSetting::first();
                $siteSettings->is_pickup_close = 'N';
                $siteSettings->pickup_close_date = null;
                $siteSettings->save();
            }
        }
        $is_delivery_close = !empty($siteSettings->is_delivery_close) ? $siteSettings->is_delivery_close : '';
        if ($is_delivery_close == 'Y') {
            $date = $siteSettings->delivery_close_date;
            $todaydate = date('Y-m-d');
            if ($todaydate != $date) {
                $siteSettings = SiteSetting::first();
                $siteSettings->is_delivery_close = 'N';
                $siteSettings->delivery_close_date = null;
                $siteSettings->save();
            }
        }
    }
    /**
     * Payment setting (Shanti Info)
     */
    public static function getPaymentSettings()
    {
        $siteSettingData = PaymentSetting::first();
        return $siteSettingData;
    }

    /**
     * get all new sloat by day id
     */
    public static function getSloatByDayId($nextday = 0)
    {
        return DB::table('delivery_slots_final')
            ->where('day', $nextday)
            ->get();
    }
    /**
     * Check addon
     */
    public static function addoStatusCheck($id = 0)
    {
        $ProductAddon = ProductAddon::where('id', $id)->first();
        if ($ProductAddon) {
            $status = 0;
            if ($ProductAddon->status) {
                $status = 1;
            }
            return $status;
        } else {
            return 1;
        }
    }
    /**
     * Check addon
     */
    public static function paymentCardFee($netamount = 0)
    {
        $fee = 0;
        $payment = self::getPaymentSettings();
        $stripeactive = !empty($payment->stripe_active) ? $payment->stripe_active : '';
        $payrexx_active = !empty($payment->payrexx_active) ? $payment->payrexx_active : '';
        if ($stripeactive) {
            if ($payment->stripe_method == 'Y' && $payment->is_stripe_fee == 'Y') {
                $fee_per = !empty($payment->stripe_fee_amount_per) ? $payment->stripe_fee_amount_per : 0;
                if ($fee_per) {
                    $fee = ($netamount * $fee_per) / 100;
                }
                $fee_flat = !empty($payment->stripe_fee_amount) ? $payment->stripe_fee_amount : 0;
                if ($fee_flat) {
                    $fee = $fee + $fee_flat;
                }
            }
        }
        if ($payrexx_active) {
            if ($payment->payrexx_method == 'Y' && $payment->is_payrexx_fee == 'Y') {
                $fee_per = !empty($payment->payrexx_fee_amount_per) ? $payment->payrexx_fee_amount_per : 0;
                if ($fee_per) {
                    $fee = ($netamount * $fee_per) / 100;
                }
                $fee_flat = !empty($payment->payrexx_fee_amount) ? $payment->payrexx_fee_amount : 0;
                if ($fee_flat) {
                    $fee = $fee + $fee_flat;
                }
            }
        }
        return number_format($fee, 2, '.', '');
    }

    /*****************************************************/
    # Function name : getRandomPassword
    # Params        :
    /*****************************************************/
    public static function getRandomPassword($stringLength = 8)
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
    public static function sendOrderMessage($deliveryPhone, $uniqueOrderId)
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

    /**
     * Update database according to the payrexx payment gateway success
     **/
    public static function webhookSuccessByPayrexUpdateDatabase($payrexxResponse)
    {
        $siteSetting    = $siteSettings = self::getSiteSettings();
        $currentLang    = $lang = \App::getLocale();
        $mailMessage    = trans('custom.error_transaction_not_completed_properly');

        $encodedData = json_decode($payrexxResponse, true);

        if (array_key_exists('transaction', $encodedData)) {
            $transactionResponse = $encodedData['transaction'];

            // Retrieving language from transation response custom field
            $currentLang= $transactionResponse['invoice']['custom_fields'][6] ? $transactionResponse['invoice']['custom_fields'][6]['value'] : \App::getLocale();
            \App::setLocale($currentLang);
            $currentLang    = $lang = \App::getLocale();

            // Order id
            $cids           = !empty($transactionResponse['referenceId']) ? $transactionResponse['referenceId'] : $transactionResponse['invoice']['referenceId'];
            
            // Condition to get record from order table
            $conditions     = ['id' => $cids, 'type' => 'C'];
            $orderUpdate    = Order::where($conditions)->first();
            $invid          = $transactionResponse['invoice']['paymentRequestId'] ? $transactionResponse['invoice']['paymentRequestId'] : $orderUpdate->payrexx_id;

            // Transaction status
            $status         = !empty($transactionResponse['status']) ? $transactionResponse['status'] : '';

            if ($status == 'confirmed') {
                $getOrderData   = Order::where(['id' => $cids, 'type' => 'C', 'order_status' => 'IC'])->first();
                // User details from custom field 1
                $orderedUser    = $transactionResponse['invoice']['custom_fields'][5] ? $transactionResponse['invoice']['custom_fields'][5]['value'] : '';

                if ($orderedUser != '') {
                    if (strpos($orderedUser, '~') !== false) {
                        $explodedOrderedUser = explode('~', $orderedUser);

                        $first_name = $explodedOrderedUser[1] ? $explodedOrderedUser[1] : null;
                        $last_name  = $explodedOrderedUser[2] ? $explodedOrderedUser[2] : null;
                        $email      = $explodedOrderedUser[3] ? $explodedOrderedUser[3] : null;
                        $phone      = $explodedOrderedUser[4] ? $explodedOrderedUser[4] : null;

                        $userPassword = self::getRandomPassword();

                        $guestEmailExist = User::where(['email' => $email, 'status' => '1', 'type' => 'C'])->whereNull('deleted_at')->first();

                        if ($explodedOrderedUser[0] == 0) {         // If guest user
                            // Guest user to make registration
                            $guestUserId = '';
                            if ($guestEmailExist == null) {
                                $newUser = new User();
                                $newUser->first_name    = $first_name;
                                $newUser->last_name     = $last_name;
                                $newUser->full_name     = $first_name.' '.$last_name;
                                $newUser->email         = $email;
                                $newUser->phone_no      = $phone;
                                $newUser->password      = $userPassword;
                                $newUser->agree         = 1;
                                $newUser->status        = '1';
                                $newUser->save();

                                $guestUserId = $newUser->id;

                                // Insert into notification
                                self::insertNotification($newUser->id);
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

                            // Update address table
                            $addressData = DeliveryAddress::where('session_id', $getOrderData->session_id)->first();
                            if ($addressData != null) {
                                $addressData->session_id    = null;
                                $addressData->user_id       = $guestUserId;
                                $addressData->save();
                            }

                            // Update order table
                            $getOrderData->session_id = null;
                            $getOrderData->user_id    = $guestUserId;
                            $getOrderData->save();

                            $orderedUserId = $guestUserId;
                        } else {                                    // If logged in user
                            $orderedUserId = $guestEmailExist->id;
                        }

                        $userData = User::where('id', $orderedUserId)->first();
                        
                        if ($getOrderData != null) {
                            $transactionResponseToInsert['id']                  = $invid;
                            $transactionResponseToInsert['object']              = '';
                            $transactionResponseToInsert['balance_transaction'] = !empty($transactionResponse['amount']) ? $transactionResponse['amount']/100 : '';
                            $transactionResponseToInsert['payment_method']      = !empty($transactionResponse['psp']) ? $transactionResponse['psp']: '';
                            $transactionResponseToInsert['pspId']               = !empty($transactionResponse['pspId']) ? $transactionResponse['pspId']: '';
                            $transactionResponseToInsert['transaction_id']      = !empty($transactionResponse['id']) ? $transactionResponse['id']: '';
                            $transactionResponseToInsert['reference_id']        = !empty($transactionResponse['referenceId']) ? $transactionResponse['referenceId'] : $transactionResponse['invoice']['referenceId'];
                            $transactionResponseToInsert['payment_request_id']  = !empty($transactionResponse['paymentRequestId']) ? $transactionResponseToInsert['paymentRequestId']: '';
                            $transactionResponseToInsert['receipt_url']         = '';
                            $transactionResponseToInsert['status']              = !empty($transactionResponse['status']) ? $transactionResponse['status'] : '';

                            // Coupon details from custom field 2,3 & 4 & Order update
                            $couponDetails = [];
                            $couponCode = null;
                            $calculatedCardAmount = $calculatedDiscountAmount = 0;
                            $couponJsonData = '';

                            if (array_key_exists('7', $transactionResponse['invoice']['custom_fields'])) {
                                $calculatedCardAmount = $transactionResponse['invoice']['custom_fields'][7] ? $transactionResponse['invoice']['custom_fields'][7]['value'] : 0;
                            }
                            if (array_key_exists('8', $transactionResponse['invoice']['custom_fields'])) {
                                $couponJsonData = $transactionResponse['invoice']['custom_fields'][8] ? $transactionResponse['invoice']['custom_fields'][8]['value'] : '';
                            }
                            if (array_key_exists('9', $transactionResponse['invoice']['custom_fields'])) {
                                $calculatedDiscountAmount= $transactionResponse['invoice']['custom_fields'][9] ? $transactionResponse['invoice']['custom_fields'][9]['value'] : 0;
                            }                            
                            
                            if ($couponJsonData != '') {
                                $couponDetails  = json_decode($couponJsonData, true);

                                $couponCode     = $couponDetails['code'] ? $couponDetails['code'] : null;
                            }

                            $getOrderData->coupon_code                  = $couponCode;
                            $getOrderData->card_payment_amount          = $calculatedCardAmount;
                            $getOrderData->discount_amount              = $calculatedDiscountAmount;
                            $getOrderData->coupon_details               = $couponJsonData ? $couponJsonData : null;
                            $getOrderData->payment_status               = 'C';
                            $getOrderData->type                         = 'O';
                            $getOrderData->payment_method               = '2';
                            $getOrderData->order_status                 = 'O';
                            $getOrderData->transaction_id               = $invid;
                            $getOrderData->transaction_response         = json_encode($transactionResponseToInsert);
                            $getOrderData->save();

                            // Coupon table update
                            Coupon::where(['code' => $getOrderData->coupon_code, 'is_one_time_use' => 'Y'])->update(['is_used' => 'Y']);

                            // Update Order Details
                            OrderDetail::where('order_id', $getOrderData->id)->update(['order_status' => 'O']);

                            // Order details
                            $orderDetails = self::getOrderDetails($getOrderData->id, $orderedUserId);

                            // Start :: Blocked on 30.07.2023
                            // if ($userData->userNotification != null) {
                            //     // Mail to customer
                            //     if ($userData->userNotification->order_update == '1') {
                            //         /* 06.04.2021
                            //         \Mail::send('email_templates.site.order_details_to_customer',
                            //             [
                            //                 'user'          => $userData,
                            //                 'siteSetting'   => $siteSettings,
                            //                 'orderDetails'  => $orderDetails,
                            //                 'getOrderData'  => $getOrderData,
                            //                 'app_config'    => [
                            //                     'appname'       => $siteSettings->website_title,
                            //                     'appLink'       => self::getBaseUrl(),
                            //                     'currentLang'   => $currentLang,
                            //                 ],
                            //             ], function ($m) use ($userData) {
                            //                 $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                            //             });
                            //             06.04.2021 */
                            //     }

                            //     // SMS to customer
                            //     // if ($userData->userNotification->sms == '1') {
                            //         // $sendSms = self::sendOrderMessage($getOrderData->delivery_phone_no, $getOrderData->unique_order_id);
                            //     // }
                            // }
                            // End :: Blocked on 30.07.2023
                            
                            if ($explodedOrderedUser[0] == 0) {
                                // Registration mail to customer (guest user who registered now)
                                $guestUserExist = User::where(['email' => $email, 'status' => '1', 'type' => 'C'])->whereNull('deleted_at')->first();
                                if ($guestUserExist != null) {
                                    // Notificaion to user
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
                                                    'appLink'       => self::getBaseUrl(),
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
                                            'appLink'       => self::getBaseUrl(),
                                            'currentLang'   => $currentLang,
                                        ],
                                    ], function ($m) use ($userData) {
                                        $m->to($userData->email, $userData->full_name)->subject(trans('custom.message_order_placed_successfully').' - '.trans('custom.label_web_site_title'));
                                    });
                                06.04.2021 */
                            }

                            // Mail to admin
                            \Mail::send('email_templates.site.order_details_to_admin',
                            [
                                'user'          => $userData,
                                'siteSetting'   => $siteSettings,
                                'orderDetails'  => $orderDetails,
                                'getOrderData'  => $getOrderData,
                                'app_config'    => [
                                    'appname'       => $siteSettings->website_title,
                                    'appLink'       => self::getBaseUrl(),
                                    'currentLang'   => $currentLang,
                                ],
                            ], function ($m) use ($siteSettings, $userData) {
                                $ordertoemail = !empty($siteSettings->to_email) ? $siteSettings->to_email : env('ORDER_EMAIL');
                                $m->to($ordertoemail, $siteSettings->website_title)->replyTo($userData->email)->subject(trans('custom.message_new_order_placed').' - '.trans('custom.label_web_site_title'));
                            });

                            $mailMessage    = trans('custom.message_transaction_completed_successfully');

                        } else {
                            $mailMessage    = trans('custom.message_empty_order');
                        }
                    }
                } else {
                    $mailMessage    = trans('custom.message_ordered_user_come_empty_from_transaction');    
                }
            } else {
                $mailMessage    = trans('custom.message_order_not_confirmed');
            }
        }

        \Mail::send(
            'email_templates.site.order_test1',
            [
                'response'      => $encodedData,
                'mailMessage'   => $mailMessage,
                'siteSetting'   => $siteSetting,
                'app_config'    => [
                                        'appname' => $siteSetting->website_title,
                                        'appLink' => self::getBaseUrl(),
                                        'currentLang' => $currentLang,
                                    ],
            ],
            function ($m) {
                $m->to('sukanta.info2@gmail.com')->subject('Test Webhook Email - ' . trans('custom.label_web_site_title'));
            },
        );
    }
}
