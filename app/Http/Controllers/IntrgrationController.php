<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Twilio;

use App\ScheduledSms;
use App\ScheduledData;
use App\PromotionInfo;


class IntrgrationController extends Controller
{
    public function twilioSendSms (Request $request) 
    {

    	if (empty($request->phone_country_code) || empty($request->phone)) {
    		return [];
    	}

    	$phone = '+'.$request->phone_country_code . $request->phone;

    	// changable to client template 
		$message = 'The "Making Money with Automated Bots" training is live in 15 minutes!  Check your email for your link and let\'s rock!';

		$sms = Twilio::message($phone, $message);
    }

    /**
     * This controller will call from crud/cron job
     * reference https://www.itsolutionstuff.com/post/laravel-6-cron-job-task-scheduling-tutorialexample.html
     * Also sending sms through twilio
     * reference https://github.com/aloha/laravel-twilio
     **/
    public function getSmsReadySchdule () 
    {

        date_default_timezone_set('Asia/Manila');
    	$objTime = Carbon::now();
    	$from = $objTime->format('Y-m-d H:i:s');
    	$to =  $objTime->addMinutes(3)->format('Y-m-d H:i:s');
		
		$sms = ScheduledSms::where('sent', 0)->whereBetween('standard_datetime', [$from,$to]);
		$sms = $sms->get();

		if(!empty($sms)){
			foreach($sms as $v){

				if(!empty($v->phone)){
					$message = 'The "Making Money with Automated Bots" training is live in 15 minutes!  Check your email for your link and let\'s rock!';

					$sms = Twilio::message($v->phone, $message);
				}

				$v->sent = 1;
				$v->save();
			}

			return 'success';
		}

		return 'no scheduled sms';

    }

    public function testsmstome ()
    {
    	$message = 'The "Making Money with Automated Bots" training is live in 15 minutes!  Check your email for your link and let\'s rock!';

		$sms = Twilio::message('+639455061595', $message);

    }


    protected $url = 'https://api.webinarjam.com/everwebinar/';

    public function getWebinarSchedule ($key, $id, $timezone)
    {
        if(empty($key) || empty($id) || empty($timezone)) {
            return response()->json(['error', 'Missing field']);
        }

        $client = new Client();

        $response = $client->request('POST', $this->url . 'webinar', 
            [
                'json' => [
                    'api_key' => $key,
                    'webinar_id' => $id,
                    'timezone' => $timezone
                ]
            ]
        );

        $result = $response->getBody()->getContents();
        $result = json_decode($result);

        if(empty($result->webinar)){
            return response()->json([]);
        }

        return response()->json($result);

    }

    public function registerUser ($key, $id, $first_name, $email, $schedule, $timezone)
    {

        $data = array();
        $data['api_key'] = $key;
        $data['webinar_id'] = $id;
        $data['first_name'] = $first_name;
        $data['email'] = $email;
        $data['schedule'] = $schedule;
        $data['timezone'] = $timezone;

        $client = new Client();

        $response = $client->request('POST', $this->url . 'register', 
            [
                'json' => $data
            ]
        );


        $result = $response->getBody()->getContents();
        $result = json_decode($result);

        $cde = $result->user->date;

        $carbon_schedule = Carbon::create($cde)->shiftTimezone($result->user->timezone); //2:pm

        $data = new PromotionInfo();
        $data->partner = 'endpoint';
        $data->user_id = $result->user->user_id;
        $data->schedule_code = $schedule;
        $data->jot = false;
        $data->email = $result->user->email;    
        $data->name = $result->user->first_name;
        $data->phone = '';
        $data->timezone = $result->user->timezone;
        $data->schedule = $carbon_schedule->format('Y-m-d H:i:s');
        $data->sent = 1;
        $data->gmt = $timezone;
        $data->live_room_url = $result->user->live_room_url;
        $data->replay_room_url = $result->user->replay_room_url;
        $data->thank_you_url = $result->user->thank_you_url;
        $data->date = $result->user->date;
        // $data->common_data = json_encode($request->list_data);
        $data->save();

        if(empty($result->user)){
            return response()->json(false);
        }

        $result->human_date = $carbon_schedule->format('F d, Y');
        $result->human_time = $carbon_schedule->format('g:i A');

        $result->custom_thankyou_url = 'https://10xworkshop.com/thankyou/'.$data->id;
        $result->custom_date_est = $carbon_schedule->setTimezone('US/Eastern')->format('m/d/Y H:i');
        $result->custom_date_est2 = $carbon_schedule->setTimezone('US/Eastern')->toAtomString();


        $now = Carbon::now()->setTimezone('US/Eastern');

        $checkDay = $carbon_schedule->setTimezone('US/Eastern')->format('d');

        if($checkDay == $now->format('d')){
            $result->day = 'today';

        } elseif($checkDay == ($now->format('d')+1)){
            
            $result->day = 'tomorrow';

        } elseif($checkDay == ($now->format('d')+2)){
            
            $result->day = 'next day';

        } else {

            $result->day = $carbon_schedule->setTimezone('US/Eastern')->format('l \a\t g:i A');

        }

        return response()->json($result);

    }

    public function utcToGmt($utc)
    {
        $utc = explode('+', $utc);

        $utc[1] = str_replace(' ', '', $utc[1]);
 
        $gmt = 'GMT+'.$utc[1];

        return response()->json(['timezoneGMT'=> $gmt]);
    }

    public function dateToArray($date)
    {

        $carbon = new Carbon($date);

        $arrayDate = $carbon->toArray();
        
        $array = [
            'day' => $carbon->format('l'),
            'date' => $carbon->format('F d'),
            'time' => $carbon->format('g:i A'),
            'military_time' => $carbon->format('H:i')
        ];

        return response()->json($array);
    }

    public function nearestSchedule($category)
    {
        $now = Carbon::now();
        // $now->setTimezone( '+08' );
        // dd($now->format('Y-m-d H:i:s'));
        $minute = $now->format('i');
        $second = $now->format('s');

        while($minute > $category) {
            $minute = $minute - $category;
        }


        $minute = $category - $minute;
        $second = 60 - $second;


        return response()->json(['minutes' => $minute, 'seconds' => $second]);

    }


}
