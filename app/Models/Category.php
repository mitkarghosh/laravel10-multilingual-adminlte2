<?php
/*****************************************************/
# Pagen/Class name   : Category
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  	use SoftDeletes;

  	/*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\CategoryLocal', 'category_id');
	}

	/*****************************************************/
    # Function name : products
    # Params        : 
    /*****************************************************/
	public function products() {
		return $this->hasMany('App\Models\Product', 'category_id')->orderBy('sort', 'asc');
	}
}
