<?php
/*****************************************************/
# Page/Class name   : RoleController
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Auth;
use App\Models\Role;
use App\Models\RolePage;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserRole;
use AdminHelper;
use Helper;

class RoleController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        :
    /*****************************************************/
    public function list(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_role_list');
        $data['panel_title']= trans('custom_admin.lab_role_list');
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);
            
            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';
            
            $data['searchText'] = $key = $request->searchText;
            if ($key) {
                $query = Role::where('name', $key);
            } else {
                $query = new Role;
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->where("id","!=",'1')
                            ->where('is_admin','=','1')
                            ->orderBy($data['order_by'], $data['order'])
                            ->paginate(AdminHelper::ADMIN_LIST_LIMIT);
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.roles.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.roles.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        :
    /*****************************************************/
    public function showAll(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_role_list');
        $data['panel_title']= trans('custom_admin.lab_role_list');
        
        try
        {
            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';
            
            $data['searchText'] = $key = $request->searchText;
            if ($key) {
                $query = Role::where('name', $key);
            } else {
                $query = new Role;
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->where("id","!=",'1')
                            ->where('is_admin','=','1')
                            ->orderBy($data['order_by'], $data['order'])
                            ->get();
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.roles.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.roles.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : create
    # Params        :
    /*****************************************************/
    public function add(Request $request)
    {
        $data['page_title']     = trans('custom_admin.lab_add_role');
        $data['panel_title']    = trans('custom_admin.lab_add_role');

        try
        {
        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    'name'  => 'required|max:100|unique:'.(new Role)->getTable().',name',
				);
				$validationMessages = array(
                    'name.required' => trans('custom_admin.error_role'),
                    'name.unique'   => trans('custom_admin.error_role_unique'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.role.add')->withErrors($Validator)->withInput();
				} else {
                    $roleName = $request->name;
                    $newSlug = Helper::generateUniqueSlug(new Role(), $roleName);

                    $role = new Role();
                    $role->name     = $roleName;
                    $role->slug     = $newSlug;
                    $role->is_admin = '1';
                    if ($role->save()) {
                        // Inserting role_page_id into role_permission table
                        if (isset($request->role_page_ids)) {
                            foreach ($request->role_page_ids as $keyRolePageId => $rolePageId) {                    
                                $rolePermission[$keyRolePageId]['role_id'] = $role->id;
                                $rolePermission[$keyRolePageId]['page_id'] = $rolePageId;
                                if ($rolePageId == 75) {
                                    $rolePermission[count($request->role_page_ids)]['role_id'] = $role->id;
                                    $rolePermission[count($request->role_page_ids)]['page_id'] = 74;
                                }
                            }
                            RolePermission::insert($rolePermission);
                        }
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
						return redirect()->route('admin.'.\App::getLocale().'.role.list');                        
					} else {
						$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back();
					}
				}
			}
			$routeCollection        = self::getRoutes();
            $data['routeCollection']= $routeCollection;

            return view('admin.roles.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.role.list')->with('error', $e->getMessage());
		}
    }

    /*****************************************************/
    # Function name : edit
    # Params        :
    /*****************************************************/
    public function edit(Request $request, $id = null)
    {
        $data['page_title']     = trans('custom_admin.lab_edit_role');
        $data['panel_title']    = trans('custom_admin.lab_edit_role');

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $data['id'] = $id;
            $details = $role = Role::where('id', $id)->first();
            $data['details'] = $details;
            $routeCollection = self::getRoutes();            

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.role.list');
                }
                $validationCondition = array(
                    'name'  => 'required|unique:' .(new Role)->getTable().',name,'.$id.',id,deleted_at,NULL',
                );
                $validationMessages = array(
                    'name.required' => trans('custom_admin.error_role'),
                    'name.unique'   => trans('custom_admin.error_role_unique'),
                );
                
                $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    $roleName = trim($request->name, ' ');
                    $role->name = $roleName;
                    $newSlug = Helper::generateUniqueSlug(new Role(), $roleName, $id);
                    $role->slug = $newSlug;

                    if ($role->save()) {
                        // Deleting and Inserting role_page_id into role_permission table
                        $deleteRolePermissions = RolePermission::where('role_id',$role->id)->delete();
                        if (isset($request->role_page_ids)) {                            
                            foreach ($request->role_page_ids as $keyRolePageId => $rolePageId) {
                                $rolePermission[$keyRolePageId]['role_id'] = $role->id;
                                $rolePermission[$keyRolePageId]['page_id'] = $rolePageId;
                                if ($rolePageId == 75) {
                                    $rolePermission[count($request->role_page_ids)]['role_id'] = $role->id;
                                    $rolePermission[count($request->role_page_ids)]['page_id'] = 74;
                                }
                            }
                            RolePermission::insert($rolePermission);
                        }
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.role.list', ['page' => $pageNo]);
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.role.list', ['page' => $pageNo]);
                    }
                }
            }           

            $existingPermission = [];
            if (count($details->permissions) > 0) {
                foreach ($details->permissions as $permisn) {
                    $existingPermission[] = $permisn['page_id'];
                }
            }
            $data['routeCollection']    = $routeCollection;
            $data['existingPermission'] = $existingPermission;
            
            return view('admin.roles.edit', $data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.role.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.role.list');
            }

            $isExistUserWithTheRole = UserRole::where('role_id', $id)->count();
            if ($isExistUserWithTheRole > 0) {                
                Session::flash('error_message', trans('custom_admin.error_role_user'));
                return redirect()->back();
            } else {
                $deleteRole = Role::find($id)->delete();
                if ($deleteRole) {
                    RolePermission::where('role_id',$id)->delete();
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                }
                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.role.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : getRoutes
    # Params        : 
    /*****************************************************/
    public function getRoutes()
    {
        $routeCollection = \Route::getRoutes();
        // dd($routeCollection);

        // echo "<table style='width:100%'>";
        //     echo "<tr>";
        //         echo "<td width='10%'><h4>Serial</h4></td>";
        //         echo "<td width='10%'><h4>HTTP Method</h4></td>";
        //         echo "<td width='10%'><h4>Route</h4></td>";
        //         echo "<td width='10%'><h4>Name</h4></td>";
        //         echo "<td width='70%'><h4>Corresponding Action</h4></td>";
        //     echo "</tr>";
        //     $k = 1;
        //     foreach ($routeCollection as $route) {
        //         $namespace = $route->uri();
        //         if (!in_array("POST", $route->methods)  && strstr($namespace,'securepanel/') != '' && strstr($route->getName(),'admin.en') != ''){
        //             echo "<tr>";
        //                 echo "<td>" . $k . "</td>";
        //                 echo "<td>" . $route->methods[0] . "</td>";
        //                 echo "<td>" . $route->uri() . "</td>";
        //                 echo "<td>" . $route->getName() . "</td>";
        //                 echo "<td>" . $route->getActionName() . "</td>";
        //             echo "</tr>";
        //             $k++;
        //         }                
        //     }
        // echo "</table>";

        // die('here');

        $list = [];
        $excludedSections = ['forgot','profile','update','reset','role','subAdmin'];
        
        foreach($routeCollection as $route) {
            $namespace = $route->uri();
            
            if (!in_array("POST", $route->methods)  && strstr($namespace,'securepanel/') != '' && strstr($route->getName(),'admin.en') != '') {
                $group = str_replace("admin.en.", "", $route->getName());
                $group = strstr($group, ".", true);
                if ($group) {
                    if (!in_array($group, $excludedSections)) {
                        $pagePath       = explode('admin.en.',$route->getName());
                        $getPagePath    = $pagePath[1];
                        
                        //Checking route exist in role_pages table or not, if not then insert and get the id
                        $rolePageDetails = RolePage::where('routeName', '=', $getPagePath)->first();
                        if ($rolePageDetails == null) {
                            $rolePageDetails = new RolePage();
                            $rolePageDetails->routeName = $getPagePath;
                            $rolePageDetails->save();
                        }

                        if (!array_key_exists($group, $list)) {
                            $list[$group] = [];
                        }
                        array_push($list[$group], [
                            "method" => $route->methods[0],
                            "uri" => $route->uri(),
                            "path" => $getPagePath,
                            "role_page_id" => $rolePageDetails->id,
                            "group_name" => ($group) ? $group : '',
                            "middleware"=>$route->middleware()
                        ]);
                    }
                }
            }
        }
        return $list;
    }

}
