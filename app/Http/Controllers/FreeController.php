<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Carbon\Carbon;

use App\ScheduledSms;
use App\ScheduledData;
use App\PromotionInfo;

class FreeController extends Controller
{
    protected $url = 'https://api.webinarjam.com/everwebinar/';
                
    protected $api_key = '';

    protected $webinar_id = '';

    protected $jot = '';

    public function getDetails ($timezone)
    {

        $client = new Client();

        $response = $client->request('POST', $this->url . 'webinar', 
            [
                'json' => [
                    'api_key' => $this->api_key,
                    'webinar_id' => $this->webinar_id,
                    'timezone' => $timezone
                ]
            ]
        );

        $result = $response->getBody()->getContents();
        $result = json_decode($result);

        if(empty($result->webinar)){
            return response()->json([]);
        }
        
        $location = $this->getLocation();
        date_default_timezone_set($location->timezone);

        $now = Carbon::now();

        foreach($result->webinar->schedules as $k => $v){

            if ($result->webinar->schedules[$k]->comment == "Just in time") {
                $this->jot = $result->webinar->schedules[$k]->schedule;
            }

            $date = date('Y-m-d H:i:s', strtotime($v->date));

            $result->webinar->internal_status[$v->schedule] = $result->webinar->schedules[$k]->date;
            $result->webinar->schedules[$k]->hide = $date;

            
            $checkDay = date('d', strtotime($v->date));

            if($checkDay == $now->format('d')){

                $result->webinar->schedules[$k]->date = 'Today '. date(' \a\t g:i A', strtotime($v->date));

            } elseif($checkDay == ($now->format('d')+1)){
                
                $result->webinar->schedules[$k]->date = 'Tomorrow '. date(' \a\t g:i A', strtotime($v->date));

            }else {

                $result->webinar->schedules[$k]->date = date('l \a\t g:i A', strtotime($v->date));

            }

        }

        return response()->json($result->webinar);
    }
    
    public function setSchedule (Request $request, $timezone)
    {   
        $this->getDetails($timezone);
        
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

        $result = $response->getBody()->getContents();
        $result = json_decode($result);


        $pi = new PromotionInfo();
        $pi->partner = 'free';
        $pi->user_id = $result->user->user_id;
        $pi->schedule_code = $request->schedule_code;
        $pi->jot = ($this->jot == $request->schedule_code) ? true : false;
        $pi->email = $request->email;
        $pi->name = $request->first_name;
        $pi->phone = !empty($request->phone) ? '+' . $request->phone_country_code . $request->phone : '';
        $pi->timezone = $request->php_timezone;
        $pi->schedule = $request->hidden_schedule . ':00';
        $pi->sent = 1;
        $pi->gmt = $timezone;
        $pi->live_room_url = $result->user->live_room_url;
        $pi->replay_room_url = $result->user->replay_room_url;
        $pi->thank_you_url = $result->user->thank_you_url;
        $pi->date = $result->user->date;
        $pi->common_data = json_encode($request->list_data);

        $pi->save();

        if(empty($result->user)){
            if(!empty($request->embed) && $request->embed) {
                return false;
            }else{
                return response()->json(false);
            }
        }

        if(!empty($pi->phone)){
            $client = new Client();

            $hook = $pi->toarray();

            date_default_timezone_set($hook['timezone']);
            $hook['apock'] = strtotime($pi->date);

            $live = explode('/', $result->user->live_room_url);

            if(!empty($live[6])){
                $hook['short_url'] = 'http://trainingurl.com/' . $live[5] . '/' . $live[6];
            } else {
                $hook['short_url'] = 'http://trainingurl.com/' . $live[5];
            }

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
            return $pi->id;
        }else{
            return response()->json($pi->id);
        }
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

    public function getThankyou ($id)
    {
        $data = PromotionInfo::find($id);

        date_default_timezone_set($data->timezone);

        if(empty($data)) {
            return response()->json(false);
        }

        $data->common_data = json_decode($data->common_data);

        $strtotime = strtotime($data->date);

        $data->apock = $strtotime.'000';
        
        $day = date('d', $strtotime);
        $data->start = date('m/d/Y H:i', $strtotime);

        if((int)date('d') == (int)$day){

            // $data->date = 'Today, ' . date('F j', $strtotime) . ' at ' . date('g:i A', $strtotime);
            $data->date = 'Today at ' . date('g:i A', $strtotime);

        } elseif((date('d')+1) == $day){

            $data->date = 'Tomorrow at ' . date('g:i A', $strtotime);

        } else {
            $data->date = date('l, F j', $strtotime) . ' at ' . date('g:i A', $strtotime);
        }

        $data->short_url = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? 'webiner.local/' : 'trainingurl.com/';

        $live = explode('/',$data->live_room_url); 

        if(!empty($live[6])){
            $data->short_url = $data->short_url . $live[5] . '/' . $live[6];
        } else {
            $data->short_url = $data->short_url . $live[5];
        }

        return response()->json($data);

    }
}
