<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Redirect;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Carbon\Carbon;
// use GuzzleHttp\Psr7\Request;
// use Spatie\GoogleTimeZone\GoogleTimeZone;

use App\ScheduledSms;
use App\ScheduledData;
use App\PromotionInfo;
use View;



class PublicController extends Controller
{

	protected $url = 'https://webinarjam.genndi.com/api/everwebinar/';

	protected $api_key = '';

	protected $webinar_id = '';

    public function getDetails ($timezone)
    {

    	$client = new Client();

    	$response = $client->request('POST', $this->url . 'webinar', 
    		[
    			'json' => [
					'api_key' => $this->api_key,
					'webinar_id' => $this->webinar_id,
					'timezone' => $timezone,
					'real_dates' => 1,
				]
			]
    	);

    	$result = $response->getBody()->getContents();
		$result = json_decode($result);

		if(empty($result->webinar)){
	    	return response()->json([]);
		}


        foreach($result->webinar->schedules as $k => $v){
            // seperate date parts
            $xp = explode(' ', $v->date);

            // convert it into date 
            $setAsDate = new Carbon($xp[2].' '. $xp[1].', '. date('Y'));

            $location = $this->getLocation();
            date_default_timezone_set($location->timezone);

            $now = Carbon::now();
            
            // internal usage
            $result->webinar->internal_status[$v->schedule] = $v->date;

            $result->webinar->schedules[$k]->hide = $result->webinar->schedules[$k]->date;

            if($xp[1] == $now->format('d')){
                $result->webinar->schedules[$k]->date = 'Today at '. $xp[3] . ' '. $xp[4];

            }elseif($xp[1] == ($now->format('d')+1)){
                
                $result->webinar->schedules[$k]->date = 'Tomorrow at '. $xp[3] . ' '. $xp[4];

            }else {

                $result->webinar->schedules[$k]->date = $xp[0] . ' at '. $xp[3] . ' '. $xp[4];
            }

        }

    	return response()->json($result->webinar);
    }

    public function register (Request $request) 
    {
    	$client = new Client();

    	$response = $client->request('POST', $this->url . 'register', 
    		[
				'api_key' => $this->api_key,
				'webinar_id' => $this->webinar_id,
				'first_name' => $request->first_name,
				'last_name' => '',
				'email' => $request->email,
				'schedule' => $request->schedule, // id
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'phone_country_code' => '',
				'phone' => $request->phone,
				'timezone' => 'GMT-5',
				'real_dates' => 1,
			]
    	);

    	$response = $response->getBody();

    	return response()->json($response);
    }

    private function getLocation()
    {
        $client = new Client();
        $ip = $_SERVER['REMOTE_ADDR'] != '127.0.0.1' ? $_SERVER['REMOTE_ADDR'] : '';
        $response = $client->request('GET', 'http://ip-api.com/json/' . $ip);
        $stream = Psr7\stream_for($response->getBody());

        $userData = json_decode($stream->getContents());


        return $userData;
    }

    public function setSchedule (Request $request, $timezone)
    {
		// set credentials
		$data = array();
		$data = $request->all();
    	$data['api_key'] = $this->api_key;
		$data['webinar_id'] = $this->webinar_id;

    	$client = new Client();

    	$response = $client->request('POST', $this->url . 'register', 
    		[
    			'json' => $data
			]
    	);


        // first save to dB
        $save = new ScheduledSms();
        $save->sent = 1;

        if(!empty($request->sms)){

            // instead of sending message directly after the registration, save it in the database and assign schedule
            // app('App\Http\Controllers\IntrgrationController')->twilioSendSms($request);

            // convert to datetime format
            $xp = explode(' ', $request->hidden_schedule);


            $schedule = $xp[2].' '.$xp[1]. ', '. date('Y') . ' '. $xp[3] . ' ' . $xp[4];

            // set client side timezone and get the current date and schedule to have the diff
            date_default_timezone_set($request->php_timezone);
            $schedule = new Carbon(date('Y-m-d H:i', strtotime($schedule)));
            $date = Carbon::now();

            // this all are the difference
            $days = $schedule->diff($date)->d;
            $hours = $schedule->diff($date)->h;
            $mins = $schedule->diff($date)->i;

            // create a standard timezone to schedule properly the sms
            date_default_timezone_set('Asia/Manila');
            $standard_datetime = Carbon::now()->addDays($days)->addHours($hours)->addMinutes($mins);

            $save->standard_datetime = $standard_datetime;
            $save->schedule = $schedule;
            $save->sent = 0;
        }

        $save->schedule_code = $request->schedule_code;
        $save->email = $request->email;
        $save->name = $request->first_name;
        $save->phone = !empty($request->phone) ? '+' . $request->phone_country_code . $request->phone : '';
        $save->timezone = $request->php_timezone;
        
        $save->save();


    	$result = $response->getBody()->getContents();
		$result = json_decode($result);

        $data = new ScheduledData();

        $data->user_id = $result->user->user_id;
        $data->sms_id = !empty($save) ? $save->id : null;
        $data->first_name = $result->user->first_name;
        $data->email = $result->user->email;
        $data->timezone = $result->user->timezone;
        $data->live_room_url = $result->user->live_room_url;
        $data->replay_room_url = $result->user->replay_room_url;
        $data->thank_you_url = $result->user->thank_you_url;
        $data->date = $result->user->date;
        $data->common_data = json_encode($request->list_data);
        $data->gmt = $timezone;

        $data->save();

		if(empty($result->user)){
            if(!empty($request->embed) && $request->embed) {
                return false;
            }else{
    	    	return response()->json(false);
            }
		}

        if(!empty($save->phone)){
            $client = new Client();

            $hook = $save->toarray();

            date_default_timezone_set($hook['timezone']);
            $hook['apock'] = strtotime($data->date);

            $sched = $hook['schedule_code'] == "jot" ? 0 : $hook['schedule_code'];
            $hook['short_url'] = 'http://trainingurl.com/' . $sched . '-' . $result->user->user_id;

            $res = $client->request('POST', 'https://hooks.zapier.com/hooks/catch/3458841/o40atsf/', 
                [
                    'json' => [
                        'form' => $hook,
                        'webinar' => $result,
                    ]
                ]
            );
        }

        if(!empty($request->embed) && $request->embed) {
            return $data->id;
        }else{
        	return response()->json($data->id);
        }
    }

    public function getThankyou ($id)
    {
        $data = ScheduledData::find($id);

        date_default_timezone_set($data->timezone);

        if(empty($data)) {
            return response()->json(false);
        }

        $data->sms;

        $data->common_data = json_decode($data->common_data);

        $strtotime = strtotime($data->date);

        $data->apock = $strtotime.'000';
        
        $day = date('d', $strtotime);
        $data->start = date('m/d/Y H:i', $strtotime);

        if((int)date('d') == (int)$day){

            $data->date = 'Today, ' . date('F j', $strtotime) . ' at ' . date('g:i A', $strtotime);

        } elseif((date('d')+1) == $day){

            $data->date = 'Tomorrow, ' . date('F j', $strtotime) . ' at ' . date('g:i A', $strtotime);

        } else {
            $data->date = date('l, F j', $strtotime) . ' at ' . date('g:i A', $strtotime);
        }

        $data->short_url = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? 'webiner.local/' : 'trainingurl.com/';

        $sched = $data->sms->schedule_code == "jot" ? 0 : $data->sms->schedule_code;

        $data->short_url = $data->short_url . $sched .'-' . $data->user_id;


        return response()->json($data);

    }

    public function apiEndpoint ($user_id)
    {

        $data = PromotionInfo::where('user_id',$user_id)->orderBy('id','desc')->first();

        date_default_timezone_set($data->timezone);

        if(empty($data)) {
            return response()->json(['status'=>'false']);
        }

        $data->common_data = json_decode($data->common_data);

        $strtotime = strtotime($data->date);

        $data->date = date('l, j F Y H:i A', $strtotime);
        $data->start = date('Y-m-d H:i:s', $strtotime);
        $data->apock = $strtotime;

        $data->custom_thankyou = config('app.url') . 'thankyou/'. $data->id;


        $data->short_url = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? 'webiner.local/' : 'trainingurl.com/';

        $live = explode('/',$data->live_room_url); 

        if(!empty($live[6])){
            $data->short_url = $data->short_url . $live[5] . '/' . $live[6];
        } else {
            $data->short_url = $data->short_url . $live[5];
        }

        $carbon_schedule = Carbon::create($data->schedule)->shiftTimezone($data->timezone); //2:pm

        $checkDay = $carbon_schedule->format('d');
        $data->human_date = $carbon_schedule->format('F d, Y');
        $data->human_time = $carbon_schedule->format('g:i A');

        $data->custom_thankyou_url = 'https://10xworkshop.com/thankyou/'.$data->id;
        $data->custom_date_est = $carbon_schedule->setTimezone('US/Eastern')->format('m/d/Y H:i');
        $data->custom_date_est2 = $carbon_schedule->setTimezone('US/Eastern')->toAtomString();

        $now = Carbon::now()->setTimezone($data->timezone);

        if($checkDay == $now->format('d')){
            $data->day = 'today';

        } elseif($checkDay == ($now->format('d')+1)){
            
            $data->day = 'tomorrow';

        } elseif($checkDay == ($now->format('d')+2)){
            
            $data->day = 'next day';

        } else {

            $data->day = $carbon_schedule->setTimezone('US/Eastern')->format('l \a\t g:i A');

        }

        return response()->json($data);

    }

    public function apiEndpointByEmail ($email)
    {
        $data = PromotionInfo::where('email',$email)->orderBy('id','desc')->first();

        if(empty($data)) {
            return response()->json(['status'=> 'false']);
        }

        date_default_timezone_set($data->timezone);

        if(empty($data)) {
            return response()->json(['status'=>'false']);
        }

        $data->common_data = json_decode($data->common_data);

        $strtotime = strtotime($data->date);

        $data->date = date('l, j F Y H:i A', $strtotime);
        $data->start = date('Y-m-d H:i:s', $strtotime);
        $data->apock = $strtotime;

        $data->custom_thankyou = config('app.url') . 'thankyou/'. $data->id;
        
        $data->short_url = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? 'webiner.local/' : 'trainingurl.com/';

        $live = explode('/',$data->live_room_url); 

        if(!empty($live[6])){
            $data->short_url = $data->short_url . $live[5] . '/' . $live[6];
        } else {
            $data->short_url = $data->short_url . $live[5];
        }

        $data->custom_thankyou = config('app.url') .  'thankyou/'. $data->id;


        $carbon_schedule = Carbon::create($data->schedule)->shiftTimezone($data->timezone); //2:pm

        $checkDay = $carbon_schedule->format('d');
        $data->human_date = $carbon_schedule->format('F d, Y');
        $data->human_time = $carbon_schedule->format('g:i A');

        $data->custom_thankyou_url = 'https://10xworkshop.com/thankyou/'.$data->id;
        $data->custom_date_est = $carbon_schedule->setTimezone('US/Eastern')->format('m/d/Y H:i');
        $data->custom_date_est2 = $carbon_schedule->setTimezone('US/Eastern')->toAtomString();

        $now = Carbon::now()->setTimezone($data->timezone);

        if($checkDay == $now->format('d')){
            $data->day = 'today';

        } elseif($checkDay == ($now->format('d')+1)){
            
            $data->day = 'tomorrow';

        } elseif($checkDay == ($now->format('d')+2)){
            
            $data->day = 'next day';

        } else {

            $data->day = $carbon_schedule->setTimezone('US/Eastern')->format('l');

        }

        $data->status = true;

        return response()->json($data);

    }

    public function liveLink ($id)
    {
        $data = ScheduledData::find($id);

        return Redirect::to($data->live_room_url);
    }


    public function backup ()
    {
        // dd('asd');
        return View::make('backup');
    }
	

}





// https://www.npmjs.com/package/vue-tel-input