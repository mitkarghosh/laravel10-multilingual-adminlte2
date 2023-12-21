<?php
/*****************************************************/
# Page/Class name   : User
# Purpose           : Table declaration, Hash password
/*****************************************************/
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*****************************************************/
    # Function name : setPasswordAttribute
    # Params        : $pass
    /*****************************************************/
    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = \Hash::make($pass);
    }

    /*****************************************************/
    # Function name : getFirstNameAttribute
    # Params        : $firstName
    /*****************************************************/
    public function getFirstNameAttribute($firstName)
    {
        return ucfirst($firstName);
    }

    /*****************************************************/
    # Function name : role
    # Params        : 
    /*****************************************************/
    public function role() {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }
    
    /*****************************************************/
    # Function name : checkRolePermission
    # Params        : 
    /*****************************************************/
    public function checkRolePermission() {
        return $this->belongsTo('App\Models\Role', 'role_id')->where('is_admin','1');
    }

    /*****************************************************/
    # Function name : allRolePermissionForUser
    # Params        : 
    /*****************************************************/
    public function allRolePermissionForUser() {
        return $this->hasMany('App\Models\RolePermission', 'role_id', 'role_id');
    }

    /*****************************************************/
    # Function name : avatarDetails
    # Params        : 
    /*****************************************************/
    public function avatarDetails() {
        return $this->belongsTo('App\Models\Avatar', 'avatar_id');
    }

    /*****************************************************/
    # Function name : userNotification
    # Params        : 
    /*****************************************************/
    public function userNotification() {
        return $this->hasOne('App\Models\Notification', 'user_id');
    }

    /*****************************************************/
    # Function name : userRoles
    # Params        : 
    /*****************************************************/
    public function userRoles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_roles', 'user_id', 'role_id');
    }
    
    /*****************************************************/
    # Function name : userDeliveryAddresses
    # Params        : 
    /*****************************************************/
    public function userDeliveryAddresses() {
        return $this->hasMany('App\Models\DeliveryAddress', 'user_id')->orderBy('id','desc');
    }

    /*****************************************************/
    # Function name : userOrders
    # Params        : 
    /*****************************************************/
    public function userOrders() {
        return $this->hasMany('App\Models\Order', 'user_id')->orderBy('id','desc');
    }

}