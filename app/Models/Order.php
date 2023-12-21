<?php
/*****************************************************/
# Page/Class name   : Order
# Purpose           : Table declaration and Other relations
/*****************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /*****************************************************/
    # Function name : orderDetails
    # Params        : 
    /*****************************************************/
    public function orderDetails() {
        return $this->hasMany('App\Models\OrderDetail');
    }

    /*****************************************************/
    # Function name : userDetails
    # Params        : 
    /*****************************************************/
    public function userDetails() {
        return $this->belongsTo('App\Models\User','user_id');
    }

    /*****************************************************/
    # Function name : shippingState
    # Params        : 
    /*****************************************************/
    public function shippingState() {
        return $this->belongsTo('App\Models\State','shipping_state_id');
    }    

    /*****************************************************/
    # Function name : orderDetailsCount
    # Params        : 
    /*****************************************************/
    public function orderDetailsCount() {
        return $this->hasMany('App\Models\OrderDetail');
    }
    
}
