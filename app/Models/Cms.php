<?php
/*****************************************************/
# Page/Class name   : Cms
# Purpose           : Table declaration
/*****************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    /*****************************************************/
    # Function name : local
    # Params        : 
    /*****************************************************/
    public function local() {
        return $this->hasMany('App\Models\CmsLocal', 'page_id');
    }
}