<?php
/*****************************************************/
# Page/Class name   : OrderDetail
# Purpose           : Table declaration and Other relations
/*****************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /*****************************************************/
    # Function name : order
    # Params        : 
    /*****************************************************/
    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    /*****************************************************/
    # Function name : orderDetailLocals
    # Params        : 
    /*****************************************************/
    public function orderDetailLocals() {
        return $this->hasMany('App\Models\OrderDetailLocal', 'order_details_id');
    }

    /*****************************************************/
    # Function name : orderIngredients
    # Params        : 
    /*****************************************************/
    public function orderIngredients() {
        return $this->hasMany('App\Models\OrderIngredient', 'order_details_id');
    }

    /*****************************************************/
    # Function name : orderAttributeLocalDetails
    # Params        : 
    /*****************************************************/
    public function orderAttributeLocalDetails() {
        return $this->hasMany('App\Models\OrderAttributeLocal','order_details_id');
    }

    /*****************************************************/
    # Function name : orderProduct
    # Params        : 
    /*****************************************************/
    public function orderProduct() {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

}
