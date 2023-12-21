<?php
/*****************************************************/
# Page/Class name   : Drink
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drink extends Model
{
  use SoftDeletes;

  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\DrinkLocal', 'drink_id');
	}
}
