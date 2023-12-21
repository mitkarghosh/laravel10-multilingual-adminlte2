<?php
/*****************************************************/
# Page/Class name   : AuthController
# Purpose           : Admin Login Management
/*****************************************************/
namespace App\Http\Controllers\admin;

use App;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use Validator;
use View;
use AdminHelper;

class AuthController extends Controller
{
    /*****************************************************/
    # Function name : login
    # Params        : Request $request
    /*****************************************************/
    public function login(Request $request)
    {
        $data['page_title']     = 'Login';
        $data['panel_title']    = 'Login';

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.'.\App::getLocale().'.dashboard');
        } else {
            try // Try block of try-catch exception starts
            {
                if ($request->isMethod('POST')) {
                    $validationCondition = array(
                        'email' => 'required|email',
                        'password' => 'required',
                    );
                    $Validator = Validator::make($request->all(), $validationCondition);

                    if ($Validator->fails()) {
                        // If validation error occurs, load the error listing
                        return redirect()->route('admin.'.\App::getLocale().'.login')->withErrors($Validator);
                    } else {
                        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'type' => 'SA', 'status' => '1'])) {
                            if (Auth::guard('admin')->user()->checkRolePermission == null) {
                                Auth::guard('admin')->logout();
                                $request->session()->flash('alert-danger', trans('custom_admin.error_permission_denied'));
                                return redirect()->back();
                            } else {
                                $user  = \Auth::guard('admin')->user();
                                $user->lastlogintime = strtotime(date('Y-m-d H:i:s'));
                                $user->save();
                                return redirect()->route('admin.'.\App::getLocale().'.dashboard');
                            }
                        } else if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'type' => 'A', 'status' => '1'])) {
                            $user  = \Auth::guard('admin')->user();
                            $user->lastlogintime = strtotime(date('Y-m-d H:i:s'));
                            $user->save();
                            return redirect()->route('admin.'.\App::getLocale().'.dashboard');
                            
                        } else {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_invalid_credentials_inactive_user'));
                            return redirect()->back()->with($request->only('email'));
                        }
                    }
                }
                // If admin is not logged in, show the login form //
                return view('admin.auth.login', $data);

            } catch (Exception $e) // catch block of the try-catch exception
            {
                $request->session()->flash('alert-danger', trans('custom_admin.error_invalid_credentials'));
                return redirect()->back();
            }
        }
    }

    /*****************************************************/
    # Function name : logout
    # Params        : Request $request
    /*****************************************************/
    public function logout()
    {
        if (Auth::guard('admin')->logout()) {
            return redirect()->route('admin.'.\App::getLocale().'.login'); // if logout is successful, proceed to login page
        } else {
            return redirect()->route('admin.'.\App::getLocale().'.dashboard'); // if logout fails, redirect tyo dashboard
        }
    }

    /*****************************************************/
    # AuthController
    # Function name : forgetPassword
    # Author        :
    # Created Date  : 31-05-2019
    # Purpose       : forget password, send new password
    # Params        : Request $request
    /*****************************************************/
    public function forgetPassword(Request $request) {
        $data['page_title'] = 'Forget Password';
        $data['panel_title'] = 'Forget Password';

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        } else {
            try // Try block of try-catch exception starts
            {
                if ($request->isMethod('POST')) {
                    $validationCondition = array(
                        'email' => 'required|email'
                    );
                    $validator = Validator::make($request->all(), $validationCondition);
                    if ($validator->fails()) {
                        // If validation error occurs, load the error listing
                        return redirect()->back()->withErrors($validator);
                    } else {
                        $user = User::where(['email' => $request->email])->first();
                        if ($user) {
                            if ($user->type == 'SA' || $user->type == 'A') {
                                $encryptedString    = AdminHelper::customEncryptionDecryption($user->id.'~'.$user->email);
                                $user->auth_token   = $encryptedString;
                                if ($user->save()) {
                                    $siteSetting = AdminHelper::getSiteSettings();                                    
                                    // Mail
                                    \Mail::send('email_templates.admin.reset_password_link',
                                    [
                                        'user'              => $user,
                                        'encryptedString'   => $encryptedString,
                                        'siteSetting'       => $siteSetting,
                                    ], function ($m) use ($user, $siteSetting) {
                                        $m->from($siteSetting->from_email, $siteSetting->website_title);
                                        $m->to($user->email, $user->full_name)->subject(trans('custom_admin.label_reset_password_link').' - '.$siteSetting->website_title);
                                    });
                                    return redirect()->back()->with('alert-success', trans('custom_admin.success_check_your_email_inbox'));
                                }
                            } else {
                                return redirect()->back()->with('alert-danger', trans('custom_admin.error_sufficient_permission'));
                            }
                        } else {
                            return redirect()->back()->with('alert-danger', trans('custom_admin.error_not_registered_with_us'));
                        }                
                    }
                }
                return view('admin.auth.forget_password', $data);
            } catch (Exception $e) {
                $request->session()->flash('alert-danger', trans('custom_admin.error_invalid_credentials'));
                return redirect()->back();
            } catch (\Throwable $e) {
                $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
                return redirect()->back()->withInput();
            }
        }      
    }

    /*****************************************************/
    # Function name : resetPassword
    # Params        : Request $request, $token = null
    /*****************************************************/
    public function resetPassword(Request $request, $token = null)
    {
        $data['page_title']  = trans('custom_admin.label_reset_password');
        $data['panel_title'] = trans('custom_admin.label_reset_password');

        try {
            if ($token == null) {
                return redirect()->route('admin.'.\App::getLocale().'.login');
                $request->session()->flash('alert-danger', trans('custom_admin.error_invalid_url'));
                return redirect()->back();
            }
            
            $data['token'] = $token;
            
            if ($request->isMethod('POST')) {
                $validationCondition = array(
                    'password'          => 'required',
                    'confirm_password'  => 'required|same:password',
                ); 
                $validationMessages = array(
                    'password.required'         => trans('custom_admin.error_enter_password'),
                    'confirm_password.required' => trans('custom_admin.error_enter_confirm_password'),
                    'confirm_password.same'     => trans('custom_admin.error_same_password'),
                );

                $validator = \Validator::make($request->all(), $validationCondition,$validationMessages);
                if ($validator->fails()) {
                    // If validation error occurs, load the error listing
                    return redirect()->back()->withErrors($validator);
                } else {
                    if ($token) {
                        $decryptToken = AdminHelper::customEncryptionDecryption($token, 'decrypt');
                        $explodedToken = explode('~',$decryptToken);
                        $checkingUserData = User::where(['id' => $explodedToken[0], 'email' => $explodedToken[1], 'auth_token' => $token])->whereNotNull('auth_token')->first();
                        if ($checkingUserData != null) {
                            $checkingUserData->password = $request->password;
                            $checkingUserData->auth_token = null;
                            if ($checkingUserData->save()) {
                                return redirect()->route('admin.'.\App::getLocale().'.login')->with('alert-success', trans('custom_admin.message_password_changed_success'));
                            }
                        } else {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_invalid_url'));
                            return redirect()->back();
                        }
                    } else {
                        abort(404);
                    }
                }
            }            
            return view('admin.auth.reset_password', $data);

        } catch (Exception $e) {
            $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
            return redirect()->back();
        } catch (\Throwable $e) {
            $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
            return redirect()->back()->withInput();
        }
    }

    /*****************************************************/
    # Function name : activeGuard
    # Purpose       : Getting active Guard
    # Params        :
    /*****************************************************/
    public function activeGuard()
    {
        foreach(array_keys(config('auth.guards')) as $guard) {      
            if(auth()->guard($guard)->check()) return $guard;      
        }
        return null;
    }
}