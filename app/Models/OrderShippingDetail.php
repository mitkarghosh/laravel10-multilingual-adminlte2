<?php
/*****************************************************/
# Page/Class name   : OrderShippingDetail
# Purpose           : Table declaration
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShippingDetail extends Model
{
    /*****************************************************/
    # Function name : order
    # Params        : 
    /*****************************************************/
    public function order() {
        return $this->belongsTo('App\Models\Order');
    }

    /*****************************************************/
    # Function name : orderDetail
    # Params        : 
    /*****************************************************/
    public function orderDetail() {
        return $this->belongsTo('App\Models\OrderDetail');
    }
}
