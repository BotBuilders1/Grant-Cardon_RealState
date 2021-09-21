<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Carbon\Carbon;

use App\ScheduledSms;
use App\ScheduledData;
use App\PromotionInfo;
use Response;
use URL;

class GcController extends Controller
{
    protected $url = 'https://api.webinarjam.com/everwebinar/';

    protected $api_key = '';

    protected $webinar_id = '';

    protected $webinar_ids = array(
            '0' => array('17','21', '29'), // week 1
            '1' => array('18','22', '30'), // week 2
            '2' => array('19','23', '31'), // week 3
            '3' => array('20','24', '32'), // week 4
            );

    protected $jot = '';

    public function getDetails ($timezone)
    {

        $schedules = array();
        $schedule_key = 0;


        foreach($this->webinar_ids[0] as $key => $value){

            $client = new Client();

            $response = $client->request('POST', $this->url . 'webinar', 
                [
                    'json' => [
                        'api_key' => $this->api_key,
                        'webinar_id' => $value,
                        'timezone' => $timezone
                    ]
                ]
            );

            $result = $response->getBody()->getContents();
            $result = json_decode($result);

            if(empty($result->webinar)){
                return response()->json([]);
            }

            // set to est time zone
            $est_dateTime  = Carbon::now()->setTimezone($timezone);

            if(empty($schedules)){
                foreach($result->webinar->schedules as $k => $v){
                    $date = new Carbon($v->date);
                    
                    if($date > $est_dateTime){
                        $arrange_array = $result->webinar->schedules[$k]; 
                        $arrange_array->hide = $result->webinar->schedules[$k]->date;

                        $arrange_array->date = $date->format('l \a\t g:i A');

                        $schedules[$date->format('nd')]  = $arrange_array;
                        $schedule_key = $k;
                        break;
                    }

                }
            } else {
                $date = new Carbon($result->webinar->schedules[$schedule_key]->date);
                $arrange_array = $result->webinar->schedules[$schedule_key]; 
                $arrange_array->hide = $result->webinar->schedules[$schedule_key]->date;

                $arrange_array->date = $date->format('l \a\t g:i A');


                $schedules[$date->format('nd')]  = $arrange_array;
            }
        }

        return response()->json(['schedules'=>$schedules, 'timezone' => $result->webinar->timezone]);
    }

    public function getscheduleId($webinar_id, $timezone)
    {
        $client = new Client();

        $response = $client->request('POST', $this->url . 'webinar', 
            [
                'json' => [
                    'api_key' => $this->api_key,
                    'webinar_id' => $webinar_id,
                    'timezone' => $timezone
                ]
            ]
        );

        $result = $response->getBody()->getContents();
        $result = json_decode($result);
        return $result;
    }

    public function setSchedule (Request $request, $timezone)
    {   


        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(false);
        }

        $domain_ext_lib = app('App\Http\Controllers\CommonController')->emailExtension();

        $ext = strrev(Explode(".", Strrev($request->email))[0]);

        if(!in_array($ext, $domain_ext_lib)){
            return response()->json(false);
        }

        // app('App\Http\Controllers\KartraController')->kartraCreate($request);

        $this->getDetails($timezone);
        
        $selected_date = new Carbon($request->hidden_schedule);

        $id_key = 0;

        switch($selected_date->setTimezone('GMT-12')->format('l')){
            case 'Tuesday':
                $id_key = 0;
                break;
            case 'Wednesday':
                $id_key = 1;
                break;
            case 'Saturday':
                $id_key = 2;
                break;
            default:
                return response()->json(false);
                break;
        }

        foreach($this->webinar_ids as $key => $value){


            $sched = $this->getscheduleId($value[$id_key], $timezone);

            $schedule_date = new Carbon($request->hidden_schedule);

            if($key > 0){
                $daysToAdd = 7*$key;
                $schedule_date = $schedule_date->addDays($daysToAdd);

            }

            // set credentials
            $data = array();
            $data = $request->all();
            $data['schedule'] = $sched->webinar->schedules[0]->schedule;
            $data['schedule_code'] = $sched->webinar->schedules[0]->schedule;
            $data['api_key'] = $this->api_key;
            $data['webinar_id'] = $value[$id_key];
            $data['hidden_schedule'] = $schedule_date->format('Y-m-d H:i');
            $data['date'] = $schedule_date->format('Y-m-d H:i');

            $client = new Client();

            $response = $client->request('POST', $this->url . 'register', 
                [
                    'json' => $data
                ]
            );

            $result = $response->getBody()->getContents();
            $result = json_decode($result);

            $pi = new PromotionInfo();
            $pi->partner = 'jumpstart';
            $pi->user_id = $result->user->user_id;
            $pi->schedule_code = $sched->webinar->schedules[0]->schedule;
            $pi->jot = false;
            $pi->email = $request->email;    
            $pi->name = $request->first_name .' '. $request->last_name;
            $pi->phone = '';
            $pi->timezone = $request->php_timezone;
            $pi->schedule = $schedule_date->format('Y-m-d H:i') . ':00';
            $pi->sent = 1;
            $pi->gmt = $timezone;
            $pi->live_room_url = $result->user->live_room_url;
            $pi->replay_room_url = $result->user->replay_room_url;
            $pi->thank_you_url = $result->user->thank_you_url;
            $pi->date = $result->user->date;
            $pi->common_data = json_encode($request->list_data);
            $pi->save();


            $hook = $pi->toarray();

            date_default_timezone_set($hook['timezone']);
            $hook['apock'] = strtotime($pi->date);

            $this->sendHubSpot($data, $result, strtotime($sched->webinar->schedules[0]->date));


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

        if(empty($data)) {
            return response()->json(false);
        }

        $scheds = PromotionInfo::where('email', $data->email)->where('id', '<=', $data->id)
            ->orderBy('id','desc')->limit(4)->get();

        $return = array();

        foreach($scheds as $k => $v){
            $return[$v->id] = $v->toArray();
            $return[$v->id]['common_data'] = json_decode($v->common_data);
            $return[$v->id]['start'] = date('m/d/Y H:i', strtotime($v->schedule));


            $return[$v->id]['short_url'] = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? 'webiner.local/' : 'trainingurl.com/';

            $live = explode('/',$return[$v->id]['live_room_url']); 

            if(!empty($live[6])){
                $return[$v->id]['short_url'] = $return[$v->id]['short_url'] . $live[5] . '/' . $live[6];
            } else {
                $return[$v->id]['short_url'] = $return[$v->id]['short_url'] . $live[5];
            }

        }

        ksort($return);
        $new_key = 0;
        foreach($return as $key => $val){
            if(!empty($val)){
                $return[$new_key] = $val;
                unset($return[$key]); 
                $new_key++;
            }
        }

        // dd($return);

        return response()->json($return);
    }

    public function sendHubSpot($data, $client_data, $apock)
    {

        if(empty($data['location_information'])){
            $data['location_information']['city'] = 'na';
            $data['location_information']['regionName'] = 'na';
            $data['location_information']['country'] = 'na';
            $data['location_information']['currency'] = 'na';

            $data['goal'] = 'na';
        }

        $day = new Carbon($data['date']);

        $hub_data['properties'][] = ["property"=> "n10xij_webinar_status", "value"=> 'registered'];
        $hub_data['properties'][] = ["property"=> "n10xij_webinar_day", "value"=> $day->format('l')];
        $hub_data['properties'][] = ["property"=> "n10xij_week_1_webinar_date", "value"=> $data['date']];
        

        $timezone = !empty($data['timezone']) ? $data['timezone'] : 'None';

        $phone = !empty($data['phone']) ? '+' . $data['phone_country_code'] . $data['phone'] : '';
        
        $hub_data['properties'][] = ["property" => "firstname", "value"=> $data['first_name']];
        $hub_data['properties'][] = ["property" => "phone", "value"=> $phone];
        $hub_data['properties'][] = ["property"=> "webinar_registration", "value"=> 'Upcoming'];
        $hub_data['properties'][] = ["property"=> "n10x_webinar_city", "value"=> $data['location_information']['city']];
        $hub_data['properties'][] = ["property"=> "n10x_webinar_state_region", "value"=> $data['location_information']['regionName']];
        $hub_data['properties'][] = ["property"=> "n10x_webinar_country", "value"=> $data['location_information']['country']];
        $hub_data['properties'][] = ["property"=> "goal_yearly_income", "value"=> $data['goal']];
        $hub_data['properties'][] = ["property"=> "local_currency", "value"=> $data['location_information']['currency']];

        $hub_data['properties'][] = ["property"=> "n10xiw_ip_address", "value"=> $_SERVER['REMOTE_ADDR']];
        $hub_data['properties'][] = ["property"=> "n10xiw_live_link", "value"=> $client_data->user->live_room_url];
        $hub_data['properties'][] = ["property"=> "n10xiw_confirmation_link", "value"=> $client_data->user->thank_you_url];
        $hub_data['properties'][] = ["property"=> "n10xiw_replay_link", "value"=> $client_data->user->replay_room_url];
        $hub_data['properties'][] = ["property"=> "n10xiw_last_webinar_registration", "value"=> $data['hidden_schedule'].':00 ('.$timezone.')'];
        $hub_data['properties'][] = ["property"=> "n10xiw_lead_source", "value"=> '10X Webclass Landing Page'];
        $hub_data['properties'][] = ["property"=> "n10xis_live_epoch", "value"=> $apock];

        if(!empty($data['offer_id'])){
            $hub_data['properties'][] = ["property"=> "offer_id", "value"=> $data['offer_id']];
        }
        if(!empty($data['transaction_id'])){
            $hub_data['properties'][] = ["property"=> "transaction_id", "value"=> $data['transaction_id']];
        }

        if(!empty($data['utm_source'])){
            $hub_data['properties'][] = ["property"=> "utm_source", "value"=> $data['utm_source']];
        }

        if(!empty($data['utm_medium'])){
            $hub_data['properties'][] = ["property"=> "utm_medium", "value"=> $data['utm_medium']];
        }

        if(!empty($data['utm_content'])){
            $hub_data['properties'][] = ["property"=> "utm_content", "value"=> $data['utm_content']];
        }

        if(!empty($data['utm_term'])){
            $hub_data['properties'][] = ["property"=> "utm_term", "value"=> $data['utm_term']];
        }

        if(!empty($data['utm_campaign'])){
            $hub_data['properties'][] = ["property"=> "utm_campaign", "value"=> $data['utm_campaign']];
        }

        if(!empty($data['utm_siteurl'])){
            $hub_data['properties'][] = ["property"=> "website", "value"=> $data['utm_siteurl']];
        }
        
        if(!empty($data['ip'])){
            $hub_data['properties'][] = ["property"=> "tune_ip_address", "value"=> $data['ip']];
        }
        if(!empty($data['partner'])){
            $hub_data['properties'][] = ["property"=> "tune_partner_name", "value"=> $data['partner']];
        }

        $email = $data['email'];
        $key = '';
        $url = 'https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/'. $email .'/?hapikey=' . $key;

        $client = new Client();

        $res = $client->request('POST', $url, 
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' =>    $hub_data
                
                ]
            );


        $result = $res->getBody()->getContents();
        $result = json_decode($result);

    }

    public function download()
    {
        $file = URL::to('/'). '/download/10X-Income-Workbook-for-Training.pdf';
        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }

    public function download2()
    {
        $file = URL::to('/'). '/download/Grant-Cardone-Virtual-Coaching-New-Year-1.pdf';
        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }

    public function workbook()
    {
        $file = URL::to('/'). '/download/10X-Business-Plan-Workbook.pdf';
        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }

    public function workbook_with_answer()
    {
        $file = URL::to('/'). '/download/10X-Business-Plan-Workbook-with-Answers';
        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }


}
