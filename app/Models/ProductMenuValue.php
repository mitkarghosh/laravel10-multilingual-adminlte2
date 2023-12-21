<?php
/*****************************************************/
# Pagen/Class name   : ProductMenuValue
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMenuValue extends Model
{
  use SoftDeletes;

  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\ProductMenuValueLocal', 'product_menu_value_id');
	}
}
