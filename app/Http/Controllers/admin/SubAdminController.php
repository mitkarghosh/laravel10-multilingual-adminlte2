<?php
/*****************************************************/
# Page/Class name   : SubAdminController
# Purpose           : SubAdmin management related functions
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use Mail;
use Config;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Helper;
use AdminHelper;
use Illuminate\Support\Facades\Validator;

class SubAdminController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_subadmin_list');
        $data['panel_title']= trans('custom_admin.lab_subadmin_list');

        try
        {
            $page_no = $request->input('page');
            Session::put('page_no',$page_no);

            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';

            $query = User::where([
                                'type' => 'A',
                            ])
                            ->where('id','<>', '1')
                            ->where('id','<>', Auth::guard('admin')->user()->id)
                            ->orderBy($data['order_by'], $data['order']);
            
            //Search section
            $searchData['profileName']  = $profileName  = isset($request->profile_name)?$request->profile_name : '';
            $searchData['mobileNo']     = $mobileNo     = isset($request->mobile_no)?$request->mobile_no : '';
            $searchData['email']        = $email        = isset($request->email)?$request->email : '';
            $searchData['roleIds']      = $roleIds      = isset($request->role_id)?$request->role_id : [];
            $data['searchData']         = $searchData;

            if ($profileName || $mobileNo || $email || $roleIds) {
                if ($profileName) {
                    $query->where(function ($subQuery) use ($profileName) {
                        $subQuery->where('first_name', 'LIKE', '%' . $profileName . '%');
                        $subQuery->orWhere('last_name', 'LIKE', '%' . $profileName . '%');
                    });
                }
                if ($mobileNo) {
                    $query->where(function ($subQuery) use ($mobileNo) {
                        $subQuery->where('phone_no', $mobileNo);
                    });
                }
                if ($email) {
                    $query->where(function ($subQuery) use ($email) {
                        $subQuery->where('email', 'LIKE', '%' . $email . '%');
                    });
                }
                if ($roleIds) {
                    $query->whereHas('userRoles' , function ($subQuery) use ($roleIds) {
                        $subQuery->whereIn('role_id', $roleIds);
                    });
                }
            }
            
            $data['list'] = $query->paginate(AdminHelper::ADMIN_LIST_LIMIT);

            $data['userDropdown'] = User::where('type','A')
                                        ->where('id','<>', '1')
                                        ->select('id','email','phone_no')
                                        ->orderBy('email', 'asc')
                                        ->get();
            $data['roleList'] = Role::where('id', '<>', '1')
                                    ->where("is_admin","1")
                                    ->select('id','name','slug','is_admin')
                                    ->get();

            return view('admin.subadmin.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.subadmin.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_subadmin_list');
        $data['panel_title']= trans('custom_admin.lab_subadmin_list');

        try
        {
            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';

            $query = User::where([
                                'type' => 'A',
                            ])
                            ->where('id','<>', '1')
                            ->where('id','<>', Auth::guard('admin')->user()->id)
                            ->orderBy($data['order_by'], $data['order']);
            
            //Search section
            $searchData['profileName']  = $profileName  = isset($request->profile_name)?$request->profile_name : '';
            $searchData['mobileNo']     = $mobileNo     = isset($request->mobile_no)?$request->mobile_no : '';
            $searchData['email']        = $email        = isset($request->email)?$request->email : '';
            $searchData['roleIds']      = $roleIds      = isset($request->role_id)?$request->role_id : [];
            $data['searchData']         = $searchData;

            if ($profileName || $mobileNo || $email || $roleIds) {
                if ($profileName) {
                    $query->where(function ($subQuery) use ($profileName) {
                        $subQuery->where('first_name', 'LIKE', '%' . $profileName . '%');
                        $subQuery->orWhere('last_name', 'LIKE', '%' . $profileName . '%');
                    });
                }
                if ($mobileNo) {
                    $query->where(function ($subQuery) use ($mobileNo) {
                        $subQuery->where('phone_no', $mobileNo);
                    });
                }
                if ($email) {
                    $query->where(function ($subQuery) use ($email) {
                        $subQuery->where('email', 'LIKE', '%' . $email . '%');
                    });
                }
                if ($roleIds) {
                    $query->whereHas('userRoles' , function ($subQuery) use ($roleIds) {
                        $subQuery->whereIn('role_id', $roleIds);
                    });
                }
            }
            
            $data['list'] = $query->get();

            $data['userDropdown'] = User::where('type','A')
                                        ->where('id','<>', '1')
                                        ->select('id','email','phone_no')
                                        ->orderBy('email', 'asc')
                                        ->get();
            $data['roleList'] = Role::where('id', '<>', '1')
                                    ->where("is_admin","1")
                                    ->select('id','name','slug','is_admin')
                                    ->get();

            return view('admin.subadmin.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.subadmin.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : add
    # Params        :
    /*****************************************************/
    public function add(Request $request) {
        $data['page_title']     = trans('custom_admin.lab_add_subadmin');
        $data['panel_title']    = trans('custom_admin.lab_add_subadmin');
        $currentLang = \App::getLocale();
    
        try
        {
        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    'first_name'    => 'required|min:2|max:255',
                    'last_name'     => 'required|min:2|max:255',
                    'email'         => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
                    'phone_no'      => 'required',
				);
				$validationMessages = array(
                    'first_name.required'   => trans('custom_admin.error_enter_first_name'),
					'first_name.min'        => trans('custom_admin.error_enter_first_minimum'),
                    'first_name.max'        => trans('custom_admin.error_enter_first_maximum'),
                    'last_name.required'    => trans('custom_admin.error_enter_last_name'),
                    'last_name.min'         => trans('custom_admin.error_enter_last_minimum'),
                    'last_name.max'         => trans('custom_admin.error_enter_last_maximum'),
                    'email.required'        => trans('custom_admin.error_enter_email_address'),
                    'email.regex'           => trans('custom_admin.error_enter_email_regex'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.subAdmin.add')->withErrors($Validator)->withInput();
				} else {
                    $validationEmailMobileMessages = array();
                    $validationFlag = false;
                    
                    // Unique Email validation for User type "Admin"
                    $userEmailExistCheck = User::where(['email' => $request->email, 'type' => 'A'])->count();
                    if ($userEmailExistCheck > 0) {
                        $validationFlag = true;
                        $validationEmailMobileMessages['email'] = trans('custom_admin.error_email_taken');
                    }
                    
                    if (!$validationFlag) {
                        $randomString = $this->getRandomKey();
                        $password = $randomString;
                        $profileName = $request->first_name.' '.$request->last_name;
            
                        $userData               = new User;
                        $userData->first_name   = isset($request->first_name)? $request->first_name : '';
                        $userData->last_name    = isset($request->last_name)? $request->last_name : '';
                        $userData->full_name    = $profileName;
                        $userData->email        = isset($request->email)? $request->email : '';
                        $userData->password     = $password;
                        $userData->phone_no     = isset($request->phone_no)? $request->phone_no : '';
                        $userData->agree        = '1';
                        $userData->type         = 'A';
                        $userData->status       = '1';
                        $userData->save();
                        
                        if($userData->id) {
                            /*----------- Inserting data to user_roles table ----------*/
                            if ($request->role) {
                                foreach ($request->role as $valRole) {
                                    $userRoleData           = new UserRole;
                                    $userRoleData->user_id  = $userData->id;
                                    $userRoleData->role_id  = $valRole;
                                    $userRoleData->save();
                                }
                            }
                        }
                        
                        //============mail code start============//
                        $siteSetting = Helper::getSiteSettings();

                        $userModel = User::findOrFail($userData->id);
                        $roleArray = [];
                        if (count($userModel->userRoles) > 0) {
                            foreach ($userModel->userRoles as $role) {
                                $roleArray[] = $role['name'];
                            }
                        }
        
                        // Email to created sub admin                        
                        \Mail::send('email_templates.admin.sub_admin_user_create',
                        [
                            'user'          => $userData,
                            'password'      => $password,
                            'siteSetting'   => $siteSetting,
                            'app_config'    => [
                                'appname'       => $siteSetting->website_title,
                                'appLink'       => Helper::getBaseUrl(),
                                'controllerName'=> 'users',
                                'currentLang'=> $currentLang,
                            ],
                        ], function ($m) use ($userData, $siteSetting) {
                            $m->to($userData->email, $userData->full_name)->subject('Sub Admin Registration - '.$siteSetting->website_title);
                        });

                        // Mail to admin
                        \Mail::send('email_templates.admin.sub_admin_user_create_to_super_admin',
                        [
                            'user'          => $userData,
                            'password'      => $password,
                            'roleArray'     => $roleArray,
                            'siteSetting'   => $siteSetting,
                            'app_config'    => [
                                'appname'       => $siteSetting->website_title,
                                'appLink'       => Helper::getBaseUrl(),
                                'controllerName'=> 'users',
                                'currentLang'=> $currentLang,
                            ],
                        ], function ($m) use ($siteSetting) {
                            $m->to($siteSetting->to_email, $siteSetting->website_title)->subject('New Sub Admin Registration - '.$siteSetting->website_title);
                        });
                        //============mail code end============//
                    
                        $request->session()->flash('alert-success',trans('custom_admin.success_data_added_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list');
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back()->withInput();
                    }				
				}
            }
            $data['roleList'] = Role::where('id', '<>', '1')
                                    ->where("is_admin","1")
                                    ->select('id','name','slug','is_admin')
                                    ->get();
			return view('admin.subadmin.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list')->with('error', $e->getMessage());
		}
    }    

    /*****************************************************/
    # Function name : edit
    # Params        :
    /*****************************************************/
    public function edit(Request $request, $id = null) {
        $data['page_title'] = trans('custom_admin.lab_edit_subadmin');
        $data['panel_title']= trans('custom_admin.lab_edit_subadmin');

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $data['roleList'] = Role::where('id', '<>', '1')
                                    ->where("is_admin","1")
                                    ->select('id','name','slug','is_admin')
                                    ->get();
            $data['details'] = $user = User::where(['id' => $id, 'type' => 'A'])->first();
            $data['id'] = $id;
            $roleIds = [];
            if ($data['details']->userRoles) {
                foreach ($data['details']->userRoles as $role) {
                    $roleIds[] = $role['id'];
                }
            }
            $data['roleIds'] = $roleIds;

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list');
                }               

                // Checking validation
                $validationCondition = array(
                    'first_name'    => 'required|min:2|max:255',
                    'last_name'     => 'required|min:2|max:255',
                    'email'         => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
                    'phone_no'      => 'required',
				);
				$validationMessages = array(
                    'first_name.required'   => trans('custom_admin.error_enter_first_name'),
					'first_name.min'        => trans('custom_admin.error_enter_first_minimum'),
                    'first_name.max'        => trans('custom_admin.error_enter_first_maximum'),
                    'last_name.required'    => trans('custom_admin.error_enter_last_name'),
                    'last_name.min'         => trans('custom_admin.error_enter_last_minimum'),
                    'last_name.max'         => trans('custom_admin.error_enter_last_maximum'),
                    'email.required'        => trans('custom_admin.error_enter_email_address'),
                    'email.regex'           => trans('custom_admin.error_enter_email_regex'),
				);
                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    $validationEmailMessages = array();

                    $validationFlag = false;
                    // Unique Email validation for User type "Admin"
                    $userEmailExistCheck = User::where('id', '<>', $id)
                                                ->where(['email' => $request->email, 'type' => 'A'])
                                                ->count();
                    if ($userEmailExistCheck > 0) {
                        $validationFlag = true;
                        $validationEmailMessages['email'] = trans('custom_admin.error_email_taken');
                    }
                    
                    if (!$validationFlag) {                        
                        $profileName        = $request->first_name.' '.$request->last_name;
                        $user->first_name   = isset($request->first_name)? $request->first_name : '';
                        $user->last_name    = isset($request->last_name)? $request->last_name : '';
                        $user->full_name    = $profileName;
                        $user->email        = $request->email;
                        $user->phone_no     = isset($request->phone_no)? $request->phone_no : '';
                        $user->save();
                        
                        /*----------- Deleting & Inserting data to user_roles table ----------*/
                        $deletingUserRoles = UserRole::where('user_id', $user->id)->delete();
                        if ($request->role) {
                            foreach ($request->role as $valRole) {
                                $userRoleData           = new UserRole;
                                $userRoleData->user_id  = $id;
                                $userRoleData->role_id  = $valRole;
                                $userRoleData->save();
                            }
                        }
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list', ['page' => $pageNo]);
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list', ['page' => $pageNo]);
                    }
                }                
            }            
            return view('admin.subadmin.edit', $data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list');
            }
            $details = User::where('id', $id)->first();
            if ($details != null) {
                if ($details->status == 1) {
                    $details->status = '0';
                    $details->save();
                    
                    $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                    return redirect()->back();

                } else if ($details->status == 0) {
                    $details->status = '1';
                    $details->save();

                    $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                    return redirect()->back();
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
                    return redirect()->back();
                }
            } else {
                return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : delete
    # Params        : Request $request, $id
    /*****************************************************/
    public function delete(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list');
            }

            $details = User::where('id', $id)->first();
            if ($details != null) {                
                $delete = $details->delete();
                if ($delete) {
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                }                
                return redirect()->back();
                
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.subAdmin.list')->with('error', $e->getMessage());
        }
    }

}
