<?php
/*****************************************************/
# Page/Class name   : Faq
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
  use SoftDeletes;
  
  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function local() {
		return $this->hasMany('App\Models\FaqLocal', 'faq_id');
	}
}
