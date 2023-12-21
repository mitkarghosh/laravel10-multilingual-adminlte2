<?php
/*****************************************************/
# Pagen/Class name   : ProductMenuTitle
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMenuTitle extends Model
{
  use SoftDeletes;

  /*****************************************************/
  # Function name : local
  # Params        : 
  /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\ProductMenuTitleLocal', 'product_menu_title_id');
  }
  
  /*****************************************************/
  # Function name : menuValues
  # Params        : 
  /*****************************************************/
	public function menuValues() {
		return $this->hasMany('App\Models\ProductMenuValue', 'product_menu_title_id');
	}

}
