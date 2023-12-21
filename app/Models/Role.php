<?php
/*****************************************************/
# Role
# Page/Class name   : Role
# Author            :
# Created Date      : 20-08-2019
# Functionality     : Table declaration
# Purpose           : 
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $table = 'roles';

    use SoftDeletes;

	/*****************************************************/
    # Role
    # Function name : permissions
    # Author        :
    # Created Date  : 20-08-2019
    # Purpose       : Getting role permissions
    # Params        : 
    /*****************************************************/
	public function permissions() {
		return $this->hasMany('App\Models\RolePermission', 'role_id');
    }
    
    /*****************************************************/
    # Role
    # Function name : rolePermissionToRolePage
    # Author        :
    # Created Date  : 10-09-2019
    # Purpose       : To get Role permission to role pages
    # Params        : 
    /*****************************************************/
    public function rolePermissionToRolePage()
    {
        return $this->belongsToMany('App\Models\RolePage', 'role_permissions', 'role_id', 'page_id');
    }
}
