<?php
/*****************************************************/
# Pagen/Class name   : Allergen
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allergen extends Model
{
  use SoftDeletes;

  /*****************************************************/
  # Function name : local
  # Params        : 
  /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\AllergenLocal', 'allergen_id');
  }
}

