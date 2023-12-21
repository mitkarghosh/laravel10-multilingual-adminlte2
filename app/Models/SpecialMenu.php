<?php
/*****************************************************/
# Page/Class name   : SpecialMenu
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialMenu extends Model
{
  use SoftDeletes;

  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\SpecialMenuLocal', 'special_menu_id');
	}
}
