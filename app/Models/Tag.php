<?php
/*****************************************************/
# Pagen/Class name   : Tag
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
  use SoftDeletes;

  /*****************************************************/
  # Function name : local
  # Params        : 
  /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\TagLocal', 'tag_id');
  }
  
}
