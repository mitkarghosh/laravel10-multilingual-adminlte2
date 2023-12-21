<?php
/*****************************************************/
# Page/Class name   : OrderIngredient
# Purpose           : Table declaration
/*****************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderIngredient extends Model
{
    public $timestamps = false;

    /*****************************************************/
    # Function name : orderIngredientLocals
    # Params        : 
    /*****************************************************/
    public function orderIngredientLocals() {
        return $this->hasMany('App\Models\OrderIngredientLocal', 'order_ingredient_id');
    }

}