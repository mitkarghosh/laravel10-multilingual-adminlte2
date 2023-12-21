<?php
/*****************************************************/
# Page/Class name   : UsersController
# Purpose           : User Management
/*****************************************************/
namespace App\Http\Controllers\admin;

use App;
use App\Http\Controllers\Controller;
use AdminHelper;
use App\Models\User;
use Hash;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Redirect;
use Validator;
use View;
use Mail;
use Helper;
use App\Models\Role;
use App\Models\SiteSetting;
use Auth;

class UsersController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request) 
    {  
        $data['page_title'] = 'User List';
        $data['panel_title']= 'User List';        
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);

            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';

            $userQuery = User::whereNull('deleted_at')->where(['type' => 'C'])->where('id', '!=', '1');

            $data['searchText'] = $key = $request->searchText;
            
            if ($key) {
                // if the search key is provided, proceed to build query for search
                $userQuery->where(function ($q) use ($key) {
                    $q->where('full_name', 'LIKE', '%' . $key . '%')
                    ->orWhere('email', 'LIKE', '%' . $key . '%');                    
                });
            }           

            $userExists = $userQuery->count();
            if ($userExists > 0) {
                $userList = $userQuery->orderBy($data['order_by'], $data['order'])->paginate(AdminHelper::ADMIN_USER_LIMIT);
                $data['userList'] = $userList;
            } else {
                $data['userList'] = array();
            }       
            return view('admin.user.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.user.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request) 
    {  
        $data['page_title'] = 'User List';
        $data['panel_title']= 'User List';        
        
        try
        {
            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';

            $userQuery = User::whereNull('deleted_at')->where(['type' => 'C'])->where('id', '!=', '1');

            $data['searchText'] = $key = $request->searchText;
            
            if ($key) {
                // if the search key is provided, proceed to build query for search
                $userQuery->where(function ($q) use ($key) {
                    $q->where('full_name', 'LIKE', '%' . $key . '%')
                    ->orWhere('email', 'LIKE', '%' . $key . '%');                    
                });
            }           

            $userExists = $userQuery->count();
            if ($userExists > 0) {
                $userList = $userQuery->orderBy($data['order_by'], $data['order'])->get();
                $data['userList'] = $userList;
            } else {
                $data['userList'] = array();
            }       
            return view('admin.user.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.user.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request) 
    {
        $data['page_title']  = 'Add User';
        $data['panel_title'] = 'Add User';

        try
        {
            if ($request->isMethod('POST')) {
                $validationCondition = array(
                    'full_name'         => 'required|min:2|max:255',
                    'email'             => 'required|unique:'.(new User)->getTable().',email|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',
                    'phone_no'          => 'required|unique:'.(new User)->getTable().',phone_no|regex:/^(?:[+]9)?[0-9]+$/',
                    'password'          => 'required|regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                );
                $validationMessages = array(
                    'full_name.required'        => 'Please enter first name',
                    'full_name.min'             => 'First name should be should be at least 2 characters',
                    'full_name.max'             => 'First name should not be more than 255 characters',
                    'email.required'            => 'Please enter email',
                    'email.regex'               => 'Please enter valid email',
                    'phone_no.required'         => 'Please enter valid phone number',
                    'phone_no.unique'           => 'The phone number has already been taken',
                    'password.required'         => 'Please enter password',
                    'password.regex'            => 'Password should be minimum 8 characters, alphanumeric and special character',
                );

                if ($request->email != null) {
                    $validationConditionEmail = array(
                        'email' => 'unique:'.(new User)->getTable().',email'
                    );
                    $validationMessagesEmail = array(
                        'email.unique'  => 'The email has already been taken'
                    );
                    $ValidatorEmail = Validator::make($request->all(), $validationConditionEmail, $validationMessagesEmail);
                    if ($ValidatorEmail->fails()) {
                        return redirect()->back()->withErrors($ValidatorEmail);
                    }
                }

                if ($request->phone_no != null) {
                    // Checking validation
                    $validationConditionPhone = array(
                        'phone_no'  => 'unique:'.(new User)->getTable().',phone_no'
                    );
                    $validationMessagesPhone = array(
                        'phone_no.unique' => 'The phone has already been taken'
                    );
                    $ValidatorPhone = Validator::make($request->all(), $validationConditionPhone, $validationMessagesPhone);
                    if ($ValidatorPhone->fails()) {
                        return redirect()->back()->withErrors($ValidatorPhone);
                    }
                }

                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);

                if ($Validator->fails()) {
                    return Redirect::back()->withErrors($Validator);
                } else {
                    $newUser = new User;
                    $newUser->full_name = trim($request->full_name, ' ');
                    $newUser->email     = trim($request->email, ' ');
                    $newUser->phone_no  = trim($request->phone_no, ' ');
                    $newUser->password  = $request->password;
                    $newUser->status    = '1';
                    $saveUser = $newUser->save();
                    if ($saveUser) {
                        $request->session()->flash('alert-success','User has been added successfully');
                        return redirect()->route('admin.user.list');
                    } else {
                        $request->session()->flash('alert-danger', 'An error occurred while adding the user');
                        return redirect()->back();
                    }
                }
            }            
            return view('admin.user.add', $data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.user.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id
    /*****************************************************/
    public function edit(Request $request, $id = null)
    {
        $data['page_title']  = 'Edit User';
        $data['panel_title'] = 'Edit User';

        try
        {
            if($id !== null) {
                $usersDetail = User::find($id);
            }

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->back();
                }              

                $validationCondition = array(
                    'full_name' => 'required|min:2|max:255',
                    'phone_no'  => 'required|unique:'.(new User)->getTable().',phone_no,' .$usersDetail->id,
                    'email'     => 'required|regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/|unique:'.(new User)->getTable().',email,' .$usersDetail->id,
                ); 
                $validationMessages = array(                        
                    'full_name.required'    => 'Please enter first name',
                    'full_name.min'         => 'First name should be should be at least 2 characters',
                    'full_name.max'         => 'First name should not be more than 255 characters',
                    'email.required'        => 'Please enter email',
                    'email.regex'           => 'Please enter valid email',
                    'phone_no.required'     => 'Please enter valid phone number',
                    'phone_no.unique'       => 'The phone number has already been taken',
                );
                $Validat = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validat->fails()) {
                    return Redirect::back()->withErrors($Validat)->withInput();
                }

                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator);
                } else {
                    $usersData = User::find($id);
                    if ($request->password != null) {
                        $pass = Hash::make($request->password);
                    } else {
                        $pass = $usersData->password;
                    }

                    $updateUserData = array(
                        'full_name' => trim($request->full_name, ' '),
                        'email'     => isset($request->email)?trim($request->email, ''):null,
                        'phone_no'  => isset($request->phone_no)?trim($request->phone_no, ''):null,
                        'password'  => $pass,
                    );
                    $saveUserData = User::where('id', $id)->update($updateUserData);                    
                    if ($saveUserData) {
                        $request->session()->flash('alert-success', 'User data has been updated successfully');
                        return redirect()->route('admin.user.list');
                    } else {
                        $request->session()->flash('alert-danger', 'An error took place while updating the profile');
                        return redirect()->back();
                    }
                }
            }
            $data['details']    = $usersDetail;
            return view('admin.user.edit', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.user.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : changePassword
    # Params        : Request $request, $id
    /*****************************************************/
    public function changePassword(Request $request, $id = null)
    {
        $data['page_title']  = 'Change password';
        $data['panel_title'] = 'Change password';

        try
        {
            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.user.list');
                }

                $validationCondition = array(
                    'password'          => 'required|regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                    'confirm_password'  => 'required|regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/|same:password',
                );
                $validationMessages = array(
                    'password.required'         => 'Please enter password',
                    'password.regex'            => 'Password should be minimum 8 characters, alphanumeric and special character',
                    'confirm_password.required' => 'Please enter confirm password',
                    'confirm_password.regex'    => 'Confirm Password should be minimum 8 characters, alphanumeric and special character',
                    'confirm_password.same'     => 'Confirm password should be same as new password',
                );
                $Validator = Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->route('admin.'.\App::getLocale().'.user.change-password', $id)->withErrors($Validator);
                } else {
                    $updateUserData = array(
                        'password' => Hash::make($request->password),
                    );
                    $updatePassword = User::where('id', $id)->update($updateUserData);
                    if ($updatePassword) {
                        $request->session()->flash('alert-success', 'Password has been updated successfully');
                        return redirect()->back();
                    } else {
                        $request->session()->flash('alert-danger', 'An error occurred while updating the password');
                        return redirect()->back();
                    }
                }
            }
            $details['id'] = $id;
            $dataUser = User::where('id', $id)->first()->toArray();
            $data['details'] = $details;
            return view('admin.user.change_password')->with(['data'=>$data, 'dataUser'=>$dataUser]);

        } catch (Exception $e) {
            return Redirect::Route('admin.'.\App::getLocale().'.user.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.user.list');
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
                return redirect()->route('admin.'.\App::getLocale().'.user.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.user.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # UsersController
    # Function name : delete
    # Author        :
    # Created Date  : 22-05-2019
    # Purpose       : Soft delete a users
    # Params        : Request $request, $id
    /*****************************************************/
    public function delete(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.'.\App::getLocale().'.user.list');
            }

            $userExists = User::where('id', $id)->count();
            $orderQuery = Order::where('user_id',$id)->first();
            if ($userExists > 0 && empty($orderQuery)) {
                $userData       = User::find($id);
                $timeStamp      = time();

                $newEmail = null; $newPhoneno = null;
                if ($userData->email != null) {
                    $explodedEmail  = explode('@',$userData->email);
                    $newEmail       = $explodedEmail[0].'_'.$timeStamp.'@'.$explodedEmail[1];
                }
                if ($userData->phone_no != null) {
                    $newPhoneno     = $userData->phone_no.$timeStamp;
                }
                
                $updateUserData = array(
                    'email'     => $newEmail,
                    'phone_no'  => $newPhoneno
                );
                $updateUserData = User::where('id', $id)->update($updateUserData);



                $updateUser = User::find($id)->delete();
                if ($updateUser) {
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                    return redirect()->back();
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.not_authorized_delete'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}