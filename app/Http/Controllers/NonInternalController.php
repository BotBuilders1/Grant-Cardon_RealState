<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use DateTimeZone;
use DateTime;

class NonInternalController extends Controller
{
    public function apochCalculator (Request $request)
    {
    	if(empty($request->time2)) {
			return 'error';
    	}

    	$time1 = new Carbon(date("Y-m-d H:i:s", strtotime(Carbon::now())));
    	$time2 = new Carbon(date("Y-m-d H:i:s", substr($request->time2, 0, 10)));

    	$diff = $time2->diffInMinutes($time1);

    	return response()->json(['minutes'=> $diff]);
    }

    public function apochDateCalculator ($date,$timezone)
    {
        if(empty($date)) {
            return 'error';
        }
        if(empty($timezone)) {
            return 'error';
        }

        $tz = str_replace('UTC','',$timezone);

        $date1 = Carbon::now()->setTimezone('GMT'.$tz);

        $date2 = new Carbon($date.' 00:00:00 '.$tz);

        $diff = $date2->diffInMinutes($date1);

        $apoch = new Carbon($date.' 00:00:00 GMT'.$tz);
        $apoch = $apoch->timestamp;

        return response()->json([
                'date'=>$date, 
                'timezone'=> $timezone, 
                'minutes'=> $diff, 
                'apoch_seconds'=> $apoch,
                'apoch_minutes'=> ($apoch/60)
            ]);
    }
}
