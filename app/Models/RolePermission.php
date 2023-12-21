<?php
/*****************************************************/
# RolePermission
# Page/Class name   : RolePermission
# Author            :
# Created Date      : 20-08-2019
# Functionality     : Table declaration, get role page details
# Purpose           : Table declaration, get role page details
/*****************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
  protected $table = 'role_permissions';

  public $timestamps = false;

  /*****************************************************/
  # RolePermission
  # Function name : page
  # Author        :
  # Created Date  : 20-08-2019
  # Purpose       : Getting role page details
  # Params        : 
  /*****************************************************/
  public function page() {
		return $this->belongsTo('App\Models\RolePage', 'page_id');
	}
}