<?php
/*****************************************************/
# Page/Class name   : OrderReview
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReview extends Model
{
  	// 
	
	/*****************************************************/
    # Function name : userDetails
    # Params        : 
    /*****************************************************/
    public function userDetails() {
        return $this->belongsTo('App\Models\User', 'user_id');
	}

    /*****************************************************/
    # Function name : orderDetails
    # Params        : 
    /*****************************************************/
    public function orderDetails() {
        return $this->belongsTo('App\Models\Order', 'order_id');
	}
	
}
