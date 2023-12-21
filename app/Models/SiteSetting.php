<?php
/*****************************************************/
# SiteSetting
# Page/Class name   : SiteSetting
# Author            :
# Created Date      : 23-05-2019
# Functionality     : Table declaration
# Purpose           : Table declaration
/*****************************************************/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public $timestamps = false;
    
    protected $hidden = ['id'];

}