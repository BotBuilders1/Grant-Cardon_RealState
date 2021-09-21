<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduledData extends Model
{
    function sms(){
    	return $this->belongsTo('App\ScheduledSms');
    }
}
