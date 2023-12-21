<?php
/*****************************************************/
# Page/Class name   : HomeController
/*****************************************************/
namespace App\Http\Controllers\site;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use Helper;
Use Redirect;
use App;
use Hash;
use \Auth;
use \Response;
use \Validator;
use App\Models\SiteSetting;
use App\Models\Cms;
use App\Models\User;
use App\Models\Category;
use App\Models\Allergen;
use App\Models\Ingredient;
use App\Models\Drink;
use App\Models\SpecialMenu;
use App\Models\PinCode;
use App\Models\DeliverySlot;
use App\Models\DeliveryArea;
use App\Models\Help;
use App\Models\Faq;
use App\Models\OrderReview;
use App\Models\SpecialHour;
use Illuminate\Support\Facades\Session;
use Image;
use Carbon\Carbon;
use Cookie;
use Twilio\Rest\Client;

class HomeController extends Controller
{
    /*****************************************************/
    # Function name : index
    # Params        : 
    /*****************************************************/
    public function index()
    {
        $currentLang    = App::getLocale();
        $homeData 		= Helper::getData('cms', '1');        

        $allergenList   = Allergen::where(['status' => '1'])
									->whereNull('deleted_at')
									->with([
                                        'local'=> function($query) use ($currentLang) {
                                            $query->where('lang_code','=', $currentLang);
                                        }
                                    ])
									->orderBy('sort', 'asc')->get();
        $categoryList   = Category::where(['status' => '1'])
									->whereNull('deleted_at')
									->with([
                                        'local' => function($query) use ($currentLang) {
                                            $query->where('lang_code','=', $currentLang);
                                        },
										'products'=> function($mainQuery) use ($currentLang) {
                                            $mainQuery->where('status','=', '1');
											$mainQuery->with([
															'local' => function($subQuery) use ($currentLang) {
																$subQuery->where('lang_code','=', $currentLang);
															},
															'productAttributes' =>  function($subQueryAttribute) use ($currentLang) {
																$subQueryAttribute->with([
                                                                    'local' => function($subQueryAttributeLocal) use ($currentLang) {
                                                                        $subQueryAttributeLocal->where('lang_code','=', $currentLang);
                                                                    }
                                                                ]);
                                                            },
                                                            'productTags' =>  function($subQueryTag) use ($currentLang) {
																$subQueryTag->with([
																		'tagDetails' => function($subQueryTagLocal) use ($currentLang) {
                                                                            $subQueryTagLocal->with([
                                                                                'local' => function($subQueryTagLocalLang) use ($currentLang) {
                                                                                    $subQueryTagLocalLang->where('lang_code','=', $currentLang);
                                                                                },
                                                                            ]);
																		}
																	]);
                                                            },
                                                            'productMenuTitles' =>  function($subQueryMenuTitle) use ($currentLang) {
																$subQueryMenuTitle->with([
                                                                    'local' => function($subQueryMenuTitleLocal) use ($currentLang) {
                                                                        $subQueryMenuTitleLocal->where('lang_code','=', $currentLang);
                                                                    },
                                                                    'menuValues' => function($subQueryMenuValueLocal) use ($currentLang) {
                                                                        $subQueryMenuValueLocal->with([
                                                                            'local' => function($subQueryValueLocalLang) use ($currentLang) {
                                                                                $subQueryValueLocalLang->where('lang_code','=', $currentLang);
                                                                            },
                                                                        ]);
                                                                    }
                                                                ]);                                                                
                                                            },
														]);
											
										}										
                                    ])
									->orderBy('sort', 'asc')
									->get();
									
        $ingredientList = Ingredient::where(['status' => '1'])
									->whereNull('deleted_at')
									->with([
                                        'local'=> function($query) use ($currentLang) {
                                            $query->where('lang_code','=', $currentLang);
                                        }
                                    ])
									->orderBy('sort', 'asc')
									->get();
        $drinkList  = Drink::where(['status' => '1'])
					        ->whereNull('deleted_at')
							->with([
								'local'=> function($query) use ($currentLang) {
									$query->where('lang_code','=', $currentLang);
								}
							])
							->orderBy('sort', 'asc')
							->get();
        $specialMenuList= SpecialMenu::where(['status' => '1'])
										->whereNull('deleted_at')
										->with([
											'local'=> function($query) use ($currentLang) {
												$query->where('lang_code','=', $currentLang);
											}
										])
										->orderBy('sort', 'asc')
                                        ->get();

        return view('site.home',[
            'title'                 => $homeData['name'],
            'keyword'               => $homeData['keyword'],
            'description'           => $homeData['description'],
            'categoryList'          => $categoryList,
            'allergenList'          => $allergenList,
            'ingredientList'        => $ingredientList,
            'drinkList'             => $drinkList,
            'specialMenuList'       => $specialMenuList,
            ]);
    }

    /*****************************************************/
    # Function name : pinCodeAvailability
    # Params        : Request $request
    /*****************************************************/
    public function pinCodeAvailability(Request $request)
    {
        $title              = trans('custom.error');
        $message            = trans('custom.error_unavailability');
        $type               = 'error';
        $sessionPinCodeId   = '';
        
        if ($request->isMethod('POST')) {
            $pinCode = isset($request->pinCode) ? $request->pinCode : '';            

            if ($pinCode != '') {
                $siteSettings = Helper::getSiteSettings();
                // if ($siteSettings->is_shop_close == 'N') {   // blocked on 06.04.2021
                    $exist = PinCode::where('code', $pinCode)->first();
                    $statusCheck=!empty($exist->status)?$exist->status:'';
                    if ($exist != null && $statusCheck) {
                        // $sessionPinCodeId = Session::put('pincode', $pinCode);
                        // Session::put('minimum_order_amount', $exist->minimum_order_amount);
                        if($siteSettings->pincode_expiry_time==0 || empty($siteSettings->pincode_expiry_time)){
                             $pinCodeExpiryTime =time() + (10 * 365 * 24 * 60 * 60);
                        }else{
                            $pinCodeExpiryTime = isset($siteSettings->pincode_expiry_time) ? $siteSettings->pincode_expiry_time : env('COOKIE_EXPIRY_TIME');
                        }
                       
                        Cookie::queue('pincode', $pinCode, $pinCodeExpiryTime);
                        Cookie::queue('minimum_order_amount', $exist->minimum_order_amount, $pinCodeExpiryTime);
                        Cookie::queue('delivery_charge', $exist->delivery_charge, $pinCodeExpiryTime);
                        
                        $title      = trans('custom.success');
                        $message    = trans('custom.success_pin_code_available');
                        $type       = 'success';
                    }
                /*} else {  // blocked on 06.04.2021
                    $title      = trans('custom.error');
                    $message    = trans('custom.message_we_are_not_accepting_order_now');
                    $type       = 'error';
                }*/
            } else {
                $title      = trans('custom.error');
                $message    = trans('custom.error_enter_pin_code');
                $type       = 'error';
            }
        }

        return json_encode([
            'title'             => $title,
            'message'           => $message,
            'type'              => $type,
            'sessionPinCodeId'  => $sessionPinCodeId,
        ]);
    }

    /*****************************************************/
    # Function name : info
    # Params        : 
    /*****************************************************/
    public function info()
    {
		$currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','info');

        $specialHour    = SpecialHour::where(['special_date' => date('Y-m-d')])->first();
        $availableList  = DeliverySlot::orderBy('id', 'asc')->get(); 

      
        $pinCodeList    = PinCode::where(['status' => '1'])
                                    ->orderBy('code', 'asc')
                                    ->get();
        $getCartData = Helper::getCartItemDetails();
		
		return view('site.info',[
            'title'             => $metaData['title'],
            'keyword'           => $metaData['keyword'],
            'description'       => $metaData['description'],
            'cmsData'           => $cmsData,
            'availableList'     => $availableList,
            'pinCodeList'       => $pinCodeList,
            'specialHour'       => $specialHour,
            'getCartData'       => $getCartData,
        ]);
    }

    /*****************************************************/
    # Function name : help
    # Params        : 
    /*****************************************************/
    public function help()
    {
		$currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','help');

        $faqList  = Faq::where(['status' => '1'])
                        ->whereNull('deleted_at')
                        ->with([
                            'local'=> function($query) use ($currentLang) {
                                $query->where('lang_code','=', $currentLang);
                            }
                        ])
                        ->orderBy('sort', 'desc')->get();
        $helpList = Help::where(['status' => '1'])
                        ->whereNull('deleted_at')
                        ->with([
                            'local'=> function($query) use ($currentLang) {
                                $query->where('lang_code','=', $currentLang);
                            }
                        ])
                        ->orderBy('sort', 'asc')->get();

        $siteSettings = Helper::getSiteSettings();

        return view('site.help',[
            'title'         => $metaData['title'],
            'keyword'       => $metaData['keyword'],
            'description'   => $metaData['description'],
            'cmsData'       => $cmsData,
            'faqList'       => $faqList,
            'helpList'      => $helpList,
            'siteSettings'  => $siteSettings,
            ]);
    }

    /*****************************************************/
    # Function name : helpDetails
    # Params        : 
    /*****************************************************/
    public function helpDetails($id)
    {
		$currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','help');

        $helpDetails = Help::where(['id' => Helper::customEncryptionDecryption($id,'decrypt')])
                        ->with([
                            'local'=> function($query) use ($currentLang) {
                                $query->where('lang_code','=', $currentLang);
                            }
                        ])
                        ->first();
        
        return view('site.help_details',[
            'title'         => $metaData['title'],
            'keyword'       => $metaData['keyword'],
            'description'   => $metaData['description'],
            'cmsData'       => $cmsData,
            'helpDetails'   => $helpDetails,
            ]);
    }

    /*****************************************************/
    # Function name : reservation
    # Params        : 
    /*****************************************************/
    public function reservation(Request $request)
    {
		$currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','reservation');
        $getCartData = Helper::getCartItemDetails();

        if ($request->isMethod('POST')) {
            $validationCondition = array(
                'reservation_date'  => 'required',
                'delivery_time'     => 'required',
                'people'            => 'required',
                'name'              => 'required',
                'email'             => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
                'phone'             => 'required',
            );
            $validationMessages = array(
                'reservation_date.required' => trans('custom.error_please_reservation_date'),
                'delivery_time.required'    => trans('custom.error_please_delivery_time'),
                'people.required'           => trans('custom.error_please_people'),
                'name.required'             => trans('custom.error_please_name'),
                'email.required'            => trans('custom.please_enter_email'),
                'email.regex'               => trans('custom.please_enter_valid_email'),
                'phone.required'            => trans('custom.error_please_phone'),
            );
            $validator = Validator::make($request->all(), $validationCondition,$validationMessages);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                $siteSetting = Helper::getSiteSettings();
                // Mail to admin
                \Mail::send('email_templates.site.reservation_details_to_admin',
                [
                    'reservation'   => $request->all(),
                    'siteSetting'   => $siteSetting,
                    'app_config'    => [
                        'appname'       => $siteSetting->website_title,
                        'appLink'       => Helper::getBaseUrl(),
                    ],
                ], function ($m) use ($siteSetting,$request) {
                    $m->to(env('RESERVATION_EMAIL'), $siteSetting->website_title)->replyTo($request->email)->subject(trans('custom.label_reservation').' - '.$siteSetting->website_title);
                });
                
                $request->session()->flash('alert-success', trans('custom.message_form_submitted_successfully'));
                return redirect()->back();
            }
        }
		
		return view('site.reservation',[
            'title'             => $metaData['title'],
            'keyword'           => $metaData['keyword'],
            'description'       => $metaData['description'],
            'cmsData'           => $cmsData,
            'getCartData'       => $getCartData,
        ]);
    }

    /*****************************************************/
    # Function name : reviews
    # Params        : 
    /*****************************************************/
    public function reviews()
    {
        $getAllReviewDetails = Helper::gettingReviews();

        $currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','review');

        return view('site.reviews',[
            'title'                 => $metaData['title'],
            'keyword'               => $metaData['keyword'],
            'description'           => $metaData['description'],
            'cmsData'               => $cmsData,
            'getAllReviewDetails'   => $getAllReviewDetails,
            ]);
    }

    /*****************************************************/
    # Function name : privacyPolicy
    # Params        : 
    /*****************************************************/
    public function privacyPolicy()
    {
		$currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','privacy-policy');

        return view('site.privacy_policy',[
            'title'         => $metaData['title'],
            'keyword'       => $metaData['keyword'],
            'description'   => $metaData['description'],
            'cmsData'       => $cmsData,
            ]);
    }
    
    /*****************************************************/
    # Function name : colofon
    # Params        : 
    /*****************************************************/
    public function colofon()
    {
		$currentLang = $lang = App::getLocale();
        $cmsData = $metaData = Helper::getMetaData('cms','imprint');

        return view('site.colofon',[
            'title'         => $metaData['title'],
            'keyword'       => $metaData['keyword'],
            'description'   => $metaData['description'],
            'cmsData'       => $cmsData,
            ]);
    }
   
}
