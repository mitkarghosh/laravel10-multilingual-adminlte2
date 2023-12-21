<?php
/*****************************************************/
# Pagen/Class name   : Product
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  use SoftDeletes;

  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\ProductLocal', 'product_id');
  }
  
  /*****************************************************/
  # Function name : productAttributes
  # Params        : 
  /*****************************************************/
	public function productAttributes() {
		return $this->hasMany('App\Models\ProductAttribute', 'product_id');
  }

  /*****************************************************/
  # Function name : productTags
  # Params        : 
  /*****************************************************/
	public function productTags() {
		return $this->hasMany('App\Models\ProductTag', 'product_id');
  }

  /*****************************************************/
  # Function name : productCategory
  # Params        : 
  /*****************************************************/
	public function productCategory() {
		return $this->belongsTo('App\Models\Category', 'category_id');
  }

  /*****************************************************/
  # Function name : productMenuTitles
  # Params        : 
  /*****************************************************/
	public function productMenuTitles() {
		return $this->hasMany('App\Models\ProductMenuTitle', 'product_id');
  }
  
}
