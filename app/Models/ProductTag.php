<?php
/*****************************************************/
# Pagen/Class name   : ProductTag
/*****************************************************/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
  public $timestamps = false;

  /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
	public function tagDetails() {
		return $this->belongsTo('App\Models\Tag', 'tag_id');
  }
  
}
