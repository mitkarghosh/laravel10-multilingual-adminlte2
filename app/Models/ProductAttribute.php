<?php
/*****************************************************/
# Pagen/Class name   : ProductAttribute
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttribute extends Model
{
  use SoftDeletes;

  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\ProductAttributeLocal', 'product_attribute_id');
	}
}
