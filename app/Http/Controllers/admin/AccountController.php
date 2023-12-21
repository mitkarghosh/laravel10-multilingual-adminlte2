<?php
/*****************************************************/
# Page/Class name   : AccountController
# Purpose           : Admin Account Management
/*****************************************************/
namespace App\Http\Controllers\admin;
use DB;
use App;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Product;
use App\Models\DeliverySlot;
use App\Models\Order;
use App\Models\PaymentSetting;
use App\Models\Drink;
use App\Models\SpecialMenu;
use App\Models\Category;
use App\Models\Tag;
use Helper;
use Image;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Redirect;
use Validator;
use View;
class AccountController extends Controller {
    /*****************************************************/
    # Function name : dashboard
    # Purpose       : After login admin will see dashboard page
    /*****************************************************/
    public function dashboard() {
        $data['page_title'] = 'Dashboard';
        $data['panel_title'] = 'Dashboard';
        $data['totalUser'] = User::where(['status' => '1', 'type' => 'C'])->whereNull('role_id')->whereNull('deleted_at')->count();
        $data['totalProducts'] = Product::where('status', '1')->whereNull('deleted_at')->count();
        $data['totalOrders'] = Order::where('order_status', 'O')->count();
        $data['totalOrdersDelivered'] = Order::where(['order_status' => 'O', 'status' => 'D'])->count();
        $data['totalNewOrders'] = Order::where(['order_status' => 'O', 'status' => 'P', 'is_print' => '0'])->count();
        $data['totalOrdersProcessing'] = Order::where(['order_status' => 'O', 'status' => 'P', 'is_print' => '1'])->count();
        $data['newOrdersListing'] = Order::where(['order_status' => 'O', 'status' => 'P', 'is_print' => '0'])->orderBy('created_at', 'desc')->limit(9)->get();
        $data['processingOrdersListing'] = Order::where(['order_status' => 'O', 'status' => 'P', 'is_print' => '1'])->orderBy('created_at', 'desc')->limit(9)->get();
        $data['newUsers'] = User::where(['status' => '1', 'type' => 'C'])->orderBy('created_at', 'desc')->whereNull('deleted_at')->limit(15)->get();
        $data['toatlActiveDrinks'] = Drink::where(['status' => '1'])->whereNull('deleted_at')->count();
        $data['toatlInactiveDrinks'] = Drink::where(['status' => '0'])->whereNull('deleted_at')->count();
        $data['toatlActiveSpecials'] = SpecialMenu::where(['status' => '1'])->whereNull('deleted_at')->count();
        $data['toatlInactiveSpecials'] = SpecialMenu::where(['status' => '0'])->whereNull('deleted_at')->count();
        $data['toatlActiveCategories'] = Category::where(['status' => '1'])->whereNull('deleted_at')->count();
        $data['toatlInactiveCategories'] = Category::where(['status' => '0'])->whereNull('deleted_at')->count();
        $data['toatlActiveTags'] = Tag::where(['status' => '1'])->whereNull('deleted_at')->count();
        $data['toatlInactiveTags'] = Tag::where(['status' => '0'])->whereNull('deleted_at')->count();
        return view('admin.account.dashboard', $data);
    }
    /*****************************************************/
    # Function name : editProfile
    # Params        : Request $request
    /*****************************************************/
    public function editProfile(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_edit_profile');
        $data['panel_title'] = trans('custom_admin.lab_edit_profile');
        try {
            $adminDetail = Auth::guard('admin')->user();
            $data['adminDetail'] = $adminDetail;
            if ($request->isMethod('POST')) {
                // Checking validation
                $validationCondition = array('first_name' => 'required', 'last_name' => 'required', 'email' => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
                // 'phone_no' => 'required|regex:/^(?:[+]9)?[0-9]+$/|unique:' . (new User)->getTable() . ',phone_no,' . $adminDetail->id,
                'phone_no' => 'required',); // validation condition
                $validationMessages = array('first_name.required' => trans('custom_admin.error_enter_first_name'), 'last_name.required' => trans('custom_admin.error_enter_last_name'), 'email.required' => trans('custom_admin.error_enter_email_address'), 'email.regex' => trans('custom_admin.error_enter_email_regex'), 'phone_no.required' => trans('custom_admin.error_enter_phone_no'),);
                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->route('admin.' . \App::getLocale() . '.edit-profile')->withErrors($Validator);
                } else {
                    $validationEmailMessages = array();
                    $validationFlag = false;
                    // Unique Email validation for User type "Admin"
                    $userEmailExistCheck = User::where('id', '<>', $adminDetail->id)->where(['email' => $request->email])->count();
                    if ($userEmailExistCheck > 0) {
                        $validationFlag = true;
                    }
                    if (!$validationFlag) {
                        $updateAdminData = array('first_name' => $request->first_name, 'last_name' => $request->last_name, 'full_name' => $request->first_name . ' ' . $request->last_name, 'email' => $request->email, 'phone_no' => $request->phone_no,);
                        $saveAdminData = User::where('id', $adminDetail->id)->update($updateAdminData);
                        if ($saveAdminData) {
                            $request->session()->flash('alert-success', trans('custom_admin.success_profile_updated'));
                            return redirect()->back();
                        } else {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                            return redirect()->back();
                        }
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_email_taken'));
                        return redirect()->back();
                    }
                }
            }
            return view('admin.account.edit_profile', $data);
        }
        catch(Exception $e) {
            return redirect()->route('admin.' . \App::getLocale() . '.edit-profile')->with('error', $e->getMessage());
        }
    }
    /*****************************************************/
    # Function name : changePassword
    # Params        : Request $request
    /*****************************************************/
    public function changePassword(Request $request) {
        $data['page_title'] = trans('custom.lab_change_password');
        $data['panel_title'] = trans('custom.lab_change_password');
        try {
            if ($request->isMethod('POST')) {
                $validationCondition = array(
                // 'current_password'  => 'required|min:8',
                // 'password'          => 'required|regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                // 'confirm_password'  => 'required|regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/|same:password',
                'current_password' => 'required', 'password' => 'required', 'confirm_password' => 'required|same:password',);
                $validationMessages = array('current_password.required' => trans('custom_admin.error_enter_current_password'), 'password.required' => trans('custom_admin.error_enter_password'),
                // 'password.regex'            => trans('custom_admin.error_enter_password_regex'),
                'confirm_password.required' => trans('custom_admin.error_enter_confirm_password'),
                // 'confirm_password.regex'    => trans('custom_admin.error_enter_password_regex'),
                'confirm_password.same' => trans('custom_admin.error_same_password'),);
                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->route('admin.' . \App::getLocale() . '.change-password')->withErrors($Validator);
                } else {
                    $adminDetail = Auth::guard('admin')->user();
                    $user_id = Auth::guard('admin')->user()->id;
                    $hashed_password = $adminDetail->password;
                    // check if current password matches with the saved password
                    if (Hash::check($request->current_password, $hashed_password)) {
                        $adminDetail->password = $request->password;
                        $updatePassword = $adminDetail->save();
                        if ($updatePassword) {
                            $request->session()->flash('alert-success', trans('custom_admin.success_password_updated'));
                            return redirect()->back();
                        } else {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                            return redirect()->back();
                        }
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_current_password'));
                        return redirect()->back();
                    }
                }
            }
            return view('admin.account.change_password', $data);
        }
        catch(Exception $e) {
            return redirect()->route('admin.' . \App::getLocale() . '.change-password')->with('error', $e->getMessage());
        }
    }
    /*****************************************************/
    # Function name : siteSettings
    # Params        : Request $request
    /*****************************************************/
    public function siteSettings(Request $request) {
        try {
            if ($request->isMethod('POST')) {
                $validationCondition = array('from_email' => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', 'to_email' => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', 'website_title' => 'required|min:2|max:255', 'website_link' => 'required',
                //'uploaded_logo'          => 'required',
                // 'minimum_order_amount'  => 'required|regex:/^[1-9]\d*(\.\d+)?$/',
                'footer_address' => 'required', 'min_delivery_delay_display' => 'required',
                //'pincode_expiry_time'   => 'required',
                );
                $validationMessages = array('from_email.required' => trans('custom_admin.error_from_email'), 'from_email.regex' => trans('custom_admin.error_valid_email'),
                //  'uploaded_logo.regex'              => trans('custom_admin.error_valid_logo'),
                'to_email.required' => trans('custom_admin.error_to_email'), 'to_email.regex' => trans('custom_admin.error_valid_email'), 'website_title.required' => trans('custom_admin.error_website_title'), 'website_title.min' => trans('custom_admin.error_website_title_minimum'), 'website_title.max' => trans('custom_admin.error_website_title_maximum'), 'website_link.required' => trans('custom_admin.error_website_link'),
                // 'minimum_order_amount.required' => trans('custom_admin.error_minimum_order_amount'),
                // 'minimum_order_amount.regex'    => trans('custom_admin.error_valid_amount'),
                'footer_address.required' => trans('custom_admin.error_footer_address'), 'min_delivery_delay_display.required' => trans('custom_admin.error_minimum_delivery_delay'), 'pincode_expiry_time.required' => trans('custom_admin.error_pincode_expiry_time'),);
                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->route('admin.' . \App::getLocale() . '.site-settings')->withErrors($Validator);
                } else {
                    if (strlen($request->pincode_expiry_time) == 0) {
                        $request->pincode_expiry_time = 0;
                    }
                    $siteSettings = SiteSetting::first();
                    $image = $request->file('logo');
                    $logo = $request->uploaded_logo;
                    if ($image != '') {
                        $originalFileNameCat = $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'logo_' . strtotime(date('Y-m-d H:i:s')) . '.' . $extension;
                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/site/logo/' . $filename));
                        // $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // });
                        //$image_resize->save(public_path('uploads/site/logo/' . $filename));
                        $logo = $filename;
                    }

                    $imagePng = $request->file('logo_png');
                    $logo_png = $request->uploaded_png_logo;
                    if ($imagePng != '') {
                        $originalFileNameCat = $imagePng->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'logo_' . strtotime(date('Y-m-d H:i:s')) . '.' . $extension;
                        $image_resize = Image::make($imagePng->getRealPath());
                        $image_resize->save(public_path('uploads/site/logo/' . $filename));
                        // $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // });
                        //$image_resize->save(public_path('uploads/site/logo/' . $filename));
                        $logo_png = $filename;
                    }

                    

                    $image = $request->file('header_picture');
                    $header_picture = $request->uploaded_header_picture;
                    if ($image != '') {
                        $originalFileNameCat = $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'header_' . strtotime(date('Y-m-d H:i:s')) . '.' . $extension;
                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/site/header/' . $filename));
                        // $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // });
                        // $image_resize->save(public_path('uploads/site/header/' . $filename));
                        $header_picture = $filename;
                    }
                    // if (empty($header_picture)) {
                    //     $request->session()->flash('alert-danger', trans('custom_admin.error_valid_header_picture'));
                    //     return redirect()->back();
                    // }

                    $advertisement_banner_de = $request->file('advertisement_banner_de');
                    $advertisement_banner_de_img = $request->advertisement_banner_de_updated;
                    if ($advertisement_banner_de != '') {
                        $originalFileNameCat = $advertisement_banner_de->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'header_' . strtotime(date('Y-m-d H:i:s')) . '.' . $extension;
                        $image_resize = Image::make($advertisement_banner_de->getRealPath());
                        $image_resize->save(public_path('uploads/advertisement/de/' . $filename));
                        // $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // });
                        // $image_resize->save(public_path('uploads/site/header/' . $filename));
                        $advertisement_banner_de_img = $filename;
                    }

                    $advertisement_banner_en = $request->file('advertisement_banner_en');
                    $advertisement_banner_en_img = $request->advertisement_banner_en_updated;
                    if ($advertisement_banner_en != '') {
                        $originalFileNameCat = $advertisement_banner_en->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'header_' . strtotime(date('Y-m-d H:i:s')) . '.' . $extension;
                        $image_resize = Image::make($advertisement_banner_en->getRealPath());
                        $image_resize->save(public_path('uploads/advertisement/en/' . $filename));
                        // $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // });
                        // $image_resize->save(public_path('uploads/site/header/' . $filename));
                        $advertisement_banner_en_img = $filename;
                    }

                    if ($siteSettings == null) {
                      
                        $newSiteSetting = new SiteSetting;
                        $newSiteSetting->from_email = $request->from_email;
                        $newSiteSetting->to_email = $request->to_email;
                        $newSiteSetting->website_title = $request->website_title;
                        $newSiteSetting->website_link = $request->website_link;
                        $newSiteSetting->facebook_link = $request->facebook_link;
                        $newSiteSetting->linkedin_link = $request->linkedin_link;
                        $newSiteSetting->youtube_link = $request->youtube_link;
                        $newSiteSetting->googleplus_link = $request->googleplus_link;
                        $newSiteSetting->twitter_link = $request->twitter_link;
                        $newSiteSetting->rss_link = $request->rss_link;
                        $newSiteSetting->pinterest_link = $request->pinterest_link;
                        $newSiteSetting->instagram_link = $request->instagram_link;
                        $newSiteSetting->default_meta_title = $newSiteSetting->default_meta_title;
                        $newSiteSetting->default_meta_keywords = $newSiteSetting->default_meta_keywords;
                        $newSiteSetting->default_meta_description = $newSiteSetting->default_meta_description;
                        $newSiteSetting->address = $request->address;
                        $newSiteSetting->phone_no = $request->phone_no;
                        // $newSiteSetting->minimum_order_amount     = $request->minimum_order_amount;
                        $newSiteSetting->map = $request->map;
                        $newSiteSetting->footer_address = $request->footer_address;
                        $newSiteSetting->min_delivery_delay_display = $request->min_delivery_delay_display;
                        $newSiteSetting->is_shop_close = isset($request->is_shop_close) ? $request->is_shop_close : 'N';
                        $newSiteSetting->pincode_expiry_time = $request->pincode_expiry_time;
                        $newSiteSetting->restaurant_speciality = $request->restaurant_speciality;
                        $newSiteSetting->mwst_number = $request->mwst_number;
                        $newSiteSetting->app_store_link = $request->app_store_link;
                        $newSiteSetting->play_store_link = $request->play_store_link;
                        //sp2
                        $newSiteSetting->logo = $logo;
                        $newSiteSetting->logo_png = $logo_png;
                        $newSiteSetting->header_picture = $header_picture;

                        $newSiteSetting->advertisement_banner_de=$advertisement_banner_de_img;
                        $newSiteSetting->advertisement_banner_en=$advertisement_banner_en_img;

                        $saveData = $newSiteSetting->save();
                        if ($saveData) {
                            $request->session()->flash('alert-success', trans('custom_admin.success_site_settings_updated'));
                        } else {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        }
                        return redirect()->back();
                    } else {
                        $updateData = array('advertisement_banner_en'=>$advertisement_banner_en_img,'advertisement_banner_de'=>$advertisement_banner_de_img,'logo_png'=>$logo_png,'logo' => $logo, 'header_picture' => $header_picture, 'from_email' => $request->from_email, 'to_email' => $request->to_email, 'website_title' => $request->website_title, 'website_link' => $request->website_link, 'facebook_link' => $request->facebook_link, 'linkedin_link' => $request->linkedin_link, 'youtube_link' => $request->youtube_link, 'googleplus_link' => $request->googleplus_link, 'twitter_link' => $request->twitter_link, 'rss_link' => $request->rss_link, 'pinterest_link' => $request->pinterest_link, 'instagram_link' => $request->instagram_link, 'default_meta_title' => $request->default_meta_title, 'default_meta_keywords' => $request->default_meta_keywords, 'default_meta_description' => $request->default_meta_description, 'address' => $request->address, 'phone_no' => $request->phone_no,
                        // 'minimum_order_amount'     => $request->minimum_order_amount,
                        'map' => $request->map, 'footer_address' => $request->footer_address, 'min_delivery_delay_display' => $request->min_delivery_delay_display, 'is_shop_close' => isset($request->is_shop_close) ? $request->is_shop_close : 'N', 'pincode_expiry_time' => $request->pincode_expiry_time, 'restaurant_speciality' => $request->restaurant_speciality, 'mwst_number' => $request->mwst_number, 'app_store_link' => $request->app_store_link, 'play_store_link' => $request->play_store_link,);
                        $save = SiteSetting::where('id', $siteSettings->id)->update($updateData);
                        $request->session()->flash('alert-success', trans('custom_admin.success_site_settings_updated'));
                        return redirect()->back();
                    }
                }
            }
            $data = ['page_title' => trans('custom_admin.lab_site_settings'), 'panel_title' => trans('custom_admin.lab_site_settings'), 'from_email' => '', 'to_email' => '', 'website_title' => '', 'website_link' => '', 'facebook_link' => '', 'linkedin_link' => '', 'youtube_link' => '', 'googleplus_link' => '', 'twitter_link' => '', 'rss_link' => '', 'pinterest_link' => '', 'instagram_link' => '', 'default_meta_title' => '', 'default_meta_keywords' => '', 'default_meta_description' => '', 'address' => '', 'phone_no' => '',
            // 'minimum_order_amount'     => '',
            'map' => '', 'footer_address' => '', 'min_delivery_delay_display' => '', 'is_shop_close' => '', 'pincode_expiry_time' => '', 'restaurant_speciality' => '', 'mwst_number' => '', 'app_store_link' => '', 'play_store_link' => '', 'logo_url' => '', 'header_picture' => '', 'header_picture_url' => ''];
            $siteSettings = SiteSetting::first();
            if ($siteSettings != null) {
                $data['from_email'] = $siteSettings->from_email;
                $data['to_email'] = $siteSettings->to_email;
                $data['website_title'] = $siteSettings->website_title;
                $data['website_link'] = $siteSettings->website_link;
                $data['facebook_link'] = $siteSettings->facebook_link;
                $data['linkedin_link'] = $siteSettings->linkedin_link;
                $data['youtube_link'] = $siteSettings->youtube_link;
                $data['googleplus_link'] = $siteSettings->googleplus_link;
                $data['twitter_link'] = $siteSettings->twitter_link;
                $data['rss_link'] = $siteSettings->rss_link;
                $data['pinterest_link'] = $siteSettings->pinterest_link;
                $data['instagram_link'] = $siteSettings->instagram_link;
                $data['default_meta_title'] = $siteSettings->default_meta_title;
                $data['default_meta_keywords'] = $siteSettings->default_meta_keywords;
                $data['default_meta_description'] = $siteSettings->default_meta_description;
                $data['address'] = $siteSettings->address;
                $data['phone_no'] = $siteSettings->phone_no;
                // $data['minimum_order_amount']     = $siteSettings->minimum_order_amount;
                $data['map'] = $siteSettings->map;
                $data['footer_address'] = $siteSettings->footer_address;
                $data['min_delivery_delay_display'] = $siteSettings->min_delivery_delay_display;
                $data['is_shop_close'] = $siteSettings->is_shop_close;
                $data['pincode_expiry_time'] = $siteSettings->pincode_expiry_time;
                $data['restaurant_speciality'] = $siteSettings->restaurant_speciality;
                $data['mwst_number'] = $siteSettings->mwst_number;
                $data['app_store_link'] = $siteSettings->app_store_link;
                $data['play_store_link'] = $siteSettings->play_store_link;
                $data['logo'] = $siteSettings->logo;
                $data['header_picture'] = $siteSettings->header_picture;
                $data['header_picture_url'] = Helper::getSettingImage('header','admin');
                $data['logo_url'] = Helper::getSettingImage('logo','admin');
                $data['png_logo_url'] = Helper::getSettingImage('png_logo','admin');
                $data['logo_png'] = $siteSettings->logo_png;

                $data['advertisement_banner_en_url'] = Helper::getSettingImage('adv_en');
                $data['advertisement_banner_en'] = $siteSettings->advertisement_banner_en;

                $data['advertisement_banner_de_url'] = Helper::getSettingImage('adv_de');
                $data['advertisement_banner_de'] = $siteSettings->advertisement_banner_de;
            }
            return view('admin.account.site_settings')->with(['siteSettings' => $siteSettings, 'data' => $data]);
        }
        catch(Exception $e) {
            return redirect()->route('admin.' . \App::getLocale() . '.site-settings')->with('error', $e->getMessage());
        }
    }
    //sp2
    public function deletePaymentStatus(Request $request, $id = null, $type = null) {
        $payment = PaymentSetting::first();
        $updateCoupon = '';
        // if ($type == 'door') {
        //     $payment->door_active = 0;
        //     $payment->door_method = 'N';
        //     $updateCoupon = $payment->save();
        // }
        // if ($type == 'cash') {
        //     $payment->cash_active = 0;
        //     $payment->cash_method = 'N';
        //     $updateCoupon = $payment->save();
        // }
        if ($type == 'stripe') { 
            $payment->stripe_active =0;
            $payment->stripe_method = 'N';
            $updateCoupon = $payment->save();
        }
        if ($type == 'payrexx') {
            $payment->payrexx_active = 0;
            $payment->payrexx_method = 'N';
            $updateCoupon = $payment->save();
        }
        if ($updateCoupon) {
            $request->session()->flash('alert-success', trans('custom_admin.success_data_deleted_successfully'));
            return redirect()->back();
        } else {
            //$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
            return redirect()->back();
        }
    }
    public function changePaymentStatus(Request $request, $id = null, $type = null) {
        $payment = PaymentSetting::first();
        $updateCoupon = '';
        if ($type == 'door') {
            $status = 'N';
            if ($payment->door_method == 'N') {
                $status = 'Y';
            }
            $payment->door_method = $status;
            $updateCoupon = $payment->save();
        }
        if ($type == 'cash') {
            $status = 'N';
            if ($payment->cash_method == 'N') {
                $status = 'Y';
            }
            $payment->cash_method = $status;
            $updateCoupon = $payment->save();
        }
        if ($type == 'stripe') {
            $status = 'N';
            if ($payment->stripe_method == 'N') {
                $status = 'Y';
                $payment->payrexx_method = 'N';
            }
            $payment->stripe_method = $status;
            $updateCoupon = $payment->save();
        }
        if ($type == 'payrexx') {
            $status = 'N';
            if ($payment->payrexx_method == 'N') {
                $status = 'Y';
                $payment->stripe_method = 'N';
            }
            $payment->payrexx_method = $status;
            $updateCoupon = $payment->save();
        }
        if ($updateCoupon) {
            $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
            return redirect()->back();
        } else {
            //$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
            return redirect()->back();
        }
    }
    public function paymentSettings(Request $request) {
        //lab_new_payment_setting
        if ($request->isMethod('POST')) {
            // $payment                           = new PaymentSetting;
            $payment = PaymentSetting::first();
            if (empty($payment)) {
                $payment = new PaymentSetting;
            }
            $payment->cash_method = !empty($request->is_cash_close) ? 'Y' : 'N';
            $payment->door_method = !empty($request->is_door_close) ? 'Y' : 'N';
            $payment->stripe_method = !empty($request->is_stripe_close) ? 'Y' : 'N';
            $payment->payrexx_method = !empty($request->is_payrexx_close) ? 'Y' : 'N';
            $payment->is_stripe_fee = !empty($request->is_stripe_fee) ? 'Y' : 'N';
            $stripefee=!empty($request->is_stripe_fee) ? 'Y' : 'N';
            if($stripefee=='Y'){
                $payment->stripe_fee_amount_per = !empty($request->stripe_fee_amount_per) ? $request->stripe_fee_amount_per : 0;
                $payment->stripe_fee_amount = !empty($request->stripe_fee_amount) ? $request->stripe_fee_amount : 0;
            }
            $payment->is_payrexx_fee = !empty($request->is_payrexx_fee) ? 'Y' : 'N';
            $payrexxfee = !empty($request->is_payrexx_fee) ? 'Y' : 'N';
            if($payrexxfee=='Y'){
                $payment->payrexx_fee_amount_per = !empty($request->payrexx_fee_amount_per) ? $request->payrexx_fee_amount_per : 0;
                $payment->payrexx_fee_amount = !empty($request->payrexx_fee_amount) ? $request->payrexx_fee_amount : 0;
            }
            $stripe = !empty($request->is_stripe_close) ? 'Y' : 'N';
            $payrexx = !empty($request->is_payrexx_close) ? 'Y' : 'N';
            if ($stripe == 'Y') {
                $payment->stripe_publish_key = !empty($request->stripe_publish_key) ? $request->stripe_publish_key : '';
                $payment->stripe_secret_key = !empty($request->stripe_secret_key) ? $request->stripe_secret_key : '';
                //$payment->payrexx_method='N';
            }
            if ($payrexx == 'Y') {
                $payrexul = !empty($request->payrexx_instance) ? $request->payrexx_instance : '';
                if ($payrexul) {
                    $payrexul = rtrim($payrexul, "/");
                }
                $payment->payrexx_instance = $payrexul; 
                $payment->payrexx_secret_key = !empty($request->payrexx_secret_key)?$request->payrexx_secret_key:''; 
            }
            $type = $request->gateway_type;
            if ($type == 'cod') {
                $payment->cash_active = 1;
            }
            if ($type == 'door') {
                $payment->door_active = 1;
            }
            if ($type == 'stripe') {
                $payment->stripe_active = 1;
                if ($stripe == 'Y') {
                    $payment->payrexx_method='N';
                 }
                 $payment->payrexx_active='0';
            }
            if ($type == 'payrexx') {
                $payment->payrexx_active = 1;
                if ($payrexx == 'Y') {
                    $payment->stripe_method='N';
                 }
                 $payment->stripe_active='0';
            }
            $payment->save();
            $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
        }
        $data['payment_setting'] = PaymentSetting::first();
        $data['page_title'] = trans('custom_admin.lab_new_payment_setting');
        $data['panel_title'] = trans('custom_admin.lab_new_payment_setting');
        return view('admin.account.payment_settings')->with(['data' => $data]);
    }
    /*****************************************************/
    # Function name : deliverySlots
    # Params        : Request $request
    /*****************************************************/
    public function deliverySlots(Request $request) {
        try {
            $deliverySlots = DeliverySlot::get();
            /**
             * New Sloat
             */
            if ($request->isMethod('POST')) {
                //DB::table('delivery_slots_final')->truncate();
                foreach ($request->delivery['id'] as $keySlot => $valSlot) {
                    $holiday = isset($request->delivery['holiday'][$keySlot]) ? $request->delivery['holiday'][$keySlot] : '0';
                    $allsloat1 = 0;
                    DeliverySlot::where('id', $valSlot)->update(['holiday' => $holiday]);
                    foreach ($request->delivery['slot'][$keySlot]['start_time'] as $keySlotTime => $valSlotTime) {
                        $starttime = $request->delivery['slot'][$keySlot]['start_time'][$allsloat1];
                        $end_time = $request->delivery['slot'][$keySlot]['end_time'][$allsloat1];
                        if (empty($starttime) || empty($end_time)) {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_overlap_some_records'));
                            return redirect()->back();
                            exit();
                        }
                        $allsloat1++;
                    }
                    DB::table('delivery_slots_final')->where('day', $keySlot + 1)->delete();
                    $allsloat = 0;
                    foreach ($request->delivery['slot'][$keySlot]['start_time'] as $keySlotTime => $valSlotTime) {
                        // DeliverySlot::where('id', $valSlot)->update([
                        //     'holiday'       => $holiday,
                        //     'start_time'    => $actualStartTime1,
                        //     'end_time'      => $actualEndTime1,
                        //     'start_time2'   => $actualStartTime2,
                        //     'end_time2'     => $actualEndTime2,
                        // ]);
                        $starttime = $request->delivery['slot'][$keySlot]['start_time'][$allsloat];
                        $end_time = $request->delivery['slot'][$keySlot]['end_time'][$allsloat];
                        $insertdata = ['start_time' => $starttime, 'end_time' => $end_time, 'day' => $valSlot];
                        DB::table('delivery_slots_final')->insert($insertdata);
                        $allsloat++;
                    }
                }
                $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                return redirect()->back();
            }
            // if ($request->isMethod('POST')) {
            //     $errorStatus = 0; $overlapping = 0;
            //     // dd($request);
            //     foreach ($request->delivery['id'] as $keySlot => $valSlot) {
            //         $holiday = isset($request->delivery['holiday'][$keySlot]) ? $request->delivery['holiday'][$keySlot] : '0';
            //         if (count($request->delivery['slot'][$keySlot]['start_time']) > 1) {
            //             $betweenTime = 0;
            //             foreach ($request->delivery['slot'][$keySlot]['start_time'] as $keySlotTime => $valSlotTime) {
            //                 if ($keySlotTime == 0) {
            //                     $startTime1  = strtotime($valSlotTime);
            //                     $endTime1    = strtotime($request->delivery['slot'][$keySlot]['end_time'][$keySlotTime]);
            //                     $actualStartTime1  = $valSlotTime;
            //                     $actualEndTime1    = $request->delivery['slot'][$keySlot]['end_time'][$keySlotTime];
            //                 } else {
            //                     $startTime2  = strtotime($valSlotTime);
            //                     $endTime2    = strtotime($request->delivery['slot'][$keySlot]['end_time'][$keySlotTime]);
            //                     $actualStartTime2  = $valSlotTime;
            //                     $actualEndTime2    = $request->delivery['slot'][$keySlot]['end_time'][$keySlotTime];
            //                 }
            //             }
            //             if ( ($startTime1 < $endTime1)) {
            //                 // checking overlapping time && ($startTime2 < $endTime2)
            //                 if ($startTime2 >= $startTime1 && $startTime2 <= $endTime1) {
            //                     $betweenTime = 1;
            //                     $overlapping++;
            //                 }
            //                 else if ($endTime2 >= $startTime1 && $endTime2 <= $endTime1) {
            //                     $betweenTime = 1;
            //                     $overlapping++;
            //                 }
            //                 else if ($startTime1 >= $startTime2  && $startTime1 <= $endTime2) {
            //                     $betweenTime = 1;
            //                     $overlapping++;
            //                 }
            //                 else if ($endTime1 >= $startTime2 && $endTime1 <= $endTime2) {
            //                     $betweenTime = 1;
            //                     $overlapping++;
            //                 }
            //                 // If all are different time and not with the 2 times
            //                 if ($betweenTime == 0) {
            //                     DeliverySlot::where('id', $valSlot)->update([
            //                         'holiday'       => $holiday,
            //                         'start_time'    => $actualStartTime1,
            //                         'end_time'      => $actualEndTime1,
            //                         'start_time2'   => $actualStartTime2,
            //                         'end_time2'     => $actualEndTime2,
            //                     ]);
            //                 }
            //             } else {
            //                 $errorStatus = 1;
            //             }
            //         } else {
            //             $startTime  = strtotime($request->delivery['slot'][$keySlot]['start_time'][0]);
            //             $endTime    = strtotime($request->delivery['slot'][$keySlot]['end_time'][0]);
            //             $actualStartTime1  = $request->delivery['slot'][$keySlot]['start_time'][0];
            //             $actualEndTime1    = $request->delivery['slot'][$keySlot]['end_time'][0];
            //             if ($startTime < $endTime) {
            //                 DeliverySlot::where('id', $valSlot)->update([
            //                                                         'holiday'       => $holiday,
            //                                                         'start_time'    => $actualStartTime1,
            //                                                         'end_time'      => $actualEndTime1,
            //                                                     ]);
            //             } else {
            //                 $errorStatus = 1;
            //             }
            //         }
            //     }
            //     if ($errorStatus == 0 && $overlapping == 0) {
            //         $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
            //         return redirect()->back();
            //     } else if ($overlapping > 0) {
            //         $request->session()->flash('alert-danger', trans('custom_admin.error_overlap_some_records'));
            //         return redirect()->back();
            //     } else {
            //         $request->session()->flash('alert-danger', trans('custom_admin.error_some_records'));
            //         return redirect()->back();
            //     }
            // }
            $data = ['page_title' => trans('custom_admin.lab_delivery_slots'), 'panel_title' => trans('custom_admin.lab_delivery_slots'), ];
            return view('admin.account.delivery_slot')->with(['deliverySlots' => $deliverySlots, 'data' => $data]);
        }
        catch(Exception $e) {
            return redirect()->route('admin.' . \App::getLocale() . '.delivery-slots')->with('error', $e->getMessage());
        }
    }
    /*****************************************************/
    # Function name : deliverySlotDelete
    # Params        : Request $request, $id
    /*****************************************************/
    public function deliverySlotDelete(Request $request, $id = null) {
        try {
            if ($id == null) {
                return redirect()->route('admin.' . \App::getLocale() . '.delivery-slot');
            }
            $details = DeliverySlot::where('id', $id)->first();
            if ($details != null) {
                $details->start_time2 = null;
                $details->end_time2 = null;
                $details->save();
                $request->session()->flash('alert-success', trans('custom_admin.success_data_deleted_successfully'));
                return redirect()->back();
            } else {
                return redirect()->route('admin.' . \App::getLocale() . '.delivery-slot')->with('error', trans('custom_admin.error_invalid'));
            }
        }
        catch(Exception $e) {
            return redirect()->route('admin.' . \App::getLocale() . '.delivery-slot')->with('error', $e->getMessage());
        }
    }
    /*****************************************************/
    # Function name : updateShopStatus
    # Params        : Request $request
    /*****************************************************/
    public function updateShopStatus(Request $request) {
        $title = trans('custom_admin.message_error');
        $message = trans('custom_admin.error_something_went_wrong');
        $type = 'error';
        if ($request->isMethod('POST')) {
            $type=!empty($request->type)?$request->type:'';  
            $siteSettings = SiteSetting::first();
            if($type=='shop'){
                $dateclose=date('Y-m-d');
                if ($siteSettings->is_shop_close == 'N') {
                    $siteSettings->is_shop_close = 'Y'; 
                    $siteSettings->is_delivery_close = 'Y'; 
                    $siteSettings->is_pickup_close = 'Y'; 
                    $siteSettings->delivery_close_date =$dateclose;
                    $siteSettings->pickup_close_date =$dateclose;
                } else {
                    $siteSettings->is_shop_close = 'N';
                    $dateclose=NULL;
                    $siteSettings->is_delivery_close = 'N'; 
                    $siteSettings->is_pickup_close = 'N'; 
                    $siteSettings->delivery_close_date =NULL;
                    $siteSettings->pickup_close_date =NULL;
                }
                $siteSettings->shop_close_date =$dateclose;
            }
            if($type=='delivery'){
                $dateclose=date('Y-m-d');
                $deliveryclosestatus='';
                if ($siteSettings->is_delivery_close == 'N') {
                    $siteSettings->is_delivery_close = 'Y'; 
                    $deliveryclosestatus=1; 
                } else {
                    $siteSettings->is_delivery_close = 'N';
                    $dateclose=NULL;
                    $siteSettings->is_shop_close = 'N';
                    $siteSettings->shop_close_date =NULL;
                }
                $siteSettings->delivery_close_date =$dateclose;
                if($deliveryclosestatus){
                    if($siteSettings->is_pickup_close == 'Y'){
                         $siteSettings->is_shop_close = 'Y';
                         $siteSettings->shop_close_date =date('Y-m-d');
                    }
                }
                if(empty($deliveryclosestatus) && $siteSettings->is_pickup_close == 'N'){
                    $siteSettings->is_shop_close = 'N';
                    $siteSettings->shop_close_date =NULL;
                }
            }
            if($type=='pickup'){
                $dateclose=date('Y-m-d');
                $deliveryclosestatus='';
                if ($siteSettings->is_pickup_close == 'N') {
                    $siteSettings->is_pickup_close = 'Y';
                    $deliveryclosestatus=1; 
                } else {
                    $siteSettings->is_pickup_close = 'N';
                    $dateclose=NULL;
                    $siteSettings->is_shop_close = 'N';
                    $siteSettings->shop_close_date =NULL;
                }
                $siteSettings->pickup_close_date =$dateclose;
                if($deliveryclosestatus){
                    if($siteSettings->is_delivery_close == 'Y'){
                         $siteSettings->is_shop_close = 'Y';
                         $siteSettings->shop_close_date =date('Y-m-d');
                    }
                }
                if(empty($deliveryclosestatus) && $siteSettings->is_delivery_close == 'N'){
                    $siteSettings->is_shop_close = 'N';
                    $siteSettings->shop_close_date =NULL;
                }
            }
            $siteSettings->save();
            $title = trans('custom_admin.message_success');
            $message = trans('custom_admin.success_data_updated_successfully');
            $type = 'success';
        }
        return json_encode(['title' => $title, 'message' => $message, 'type' => $type]);
    }
}
