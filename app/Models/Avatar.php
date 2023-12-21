<?php
/*****************************************************/
# Pagen/Class name   : Avatar
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avatar extends Model
{
  use SoftDeletes;

  /*****************************************************/
  # Function name : local
  # Params        : 
  /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\AvatarLocal', 'avatar_id');
  }
  
}
