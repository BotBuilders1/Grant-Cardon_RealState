<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Carbon\Carbon;

use App\ScheduledSms;
use App\ScheduledData;
use App\PromotionInfo;

use URL;

class WorkshopController extends Controller
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

        $hook = $pi->toarray();

        date_default_timezone_set($hook['timezone']);
        $hook['apock'] = strtotime($pi->date);

        $this->sendHubSpot($data, $result, $hook['apock']);


        // if(!empty($pi->phone)){
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

            // $res = $client->request('POST', 'https://hooks.zapier.com/hooks/catch/3458841/o40atsf/', 
            //     [
            //         'json' => [
            //             'form' => $hook,
            //             'webinar' => $result,
            //         ]
            //     ]
            // );

            // https://hooks.zapier.com/hooks/catch/3458841/ov49ipl/
            // https://hooks.zapier.com/hooks/catch/3458841/ovhk8c5/   
            $res = $client->request('POST', 'https://hooks.zapier.com/hooks/catch/3458841/ov49ipl/', 
                [
                    'json' => [
                        'form' => $hook,
                        'webinar' => $result,
                    ]
                ]
            );
            $res2 = $client->request('POST', 'https://hooks.zapier.com/hooks/catch/3458841/ovhk8c5/', 
                [
                    'json' => [
                        'form' => $hook,
                        'webinar' => $result,
                    ]
                ]
            );
        // }

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
            // $data->date = date('l, F j', $strtotime) . ' at ' . date('g:i A', $strtotime);
            $data->date = date('l', $strtotime) . ' at ' . date('g:i A', $strtotime);
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


    public function workbook()
    {
        $file = URL::to('/'). '/download/10x_business_plan_workbook.pdf';

        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }

    public function workbook_with_answer()
    {
        $file = URL::to('/'). '/download/10x_business_plan_workbook_with_answers.pdf';
        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }



    public function businessPlan()
    {
        $file = URL::to('/'). '/download/10X-Business-Plan-Workbook-Completed.pdf';

        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
    }

    public function businessPlan2()
    {
        $file = URL::to('/'). '/download/10X-Business-Plan-Workbook-Customizable.pdf';

        $headers = [
              'Content-Type: application/pdf',
           ];
        // return Response::download($file, '10X-Income-Workbook-for-Training.pdf', $headers);

    // return Response::download($file, 'filename.pdf', $headers);
           return redirect($file);
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

        $day = new Carbon($data['hidden_schedule']);

        // $hub_data['properties'][] = ["property"=> "n10xij_webinar_status", "value"=> 'registered'];
        // $hub_data['properties'][] = ["property"=> "n10xij_webinar_day", "value"=> $day->format('l')];
        // $hub_data['properties'][] = ["property"=> "n10xij_week_1_webinar_date", "value"=> $data['hidden_schedule']];
        

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

        // $hub_data['properties'][] = ["property"=> "n10xiw_ip_address", "value"=> $_SERVER['REMOTE_ADDR']];
        // $hub_data['properties'][] = ["property"=> "n10xiw_live_link", "value"=> $client_data->user->live_room_url];
        // $hub_data['properties'][] = ["property"=> "n10xiw_confirmation_link", "value"=> $client_data->user->thank_you_url];
        // $hub_data['properties'][] = ["property"=> "n10xiw_replay_link", "value"=> $client_data->user->replay_room_url];
        // $hub_data['properties'][] = ["property"=> "n10xiw_last_webinar_registration", "value"=> $data['hidden_schedule'].':00 ('.$timezone.')'];
        // $hub_data['properties'][] = ["property"=> "n10xiw_lead_source", "value"=> '10X Webclass Landing Page'];
        // $hub_data['properties'][] = ["property"=> "n10xis_live_epoch", "value"=> $apock];

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

		$hub_data['properties'][] = ["property"=> "n10xms__ip_address", "value"=> $data['location_information']['ip']];
		$hub_data['properties'][] = ["property"=> "n10xms__timezone", "value"=> $data['php_timezone']];
		$hub_data['properties'][] = ["property"=> "n10xms__registration_page", "value"=> (!empty($data['page']) ? $data['page']: '')];
		$hub_data['properties'][] = ["property"=> "n10xms__last_webinar_registration", "value"=> $data['hidden_schedule']];
		$hub_data['properties'][] = ["property"=> "n10xms__live_link", "value"=> $client_data->user->live_room_url];
		$hub_data['properties'][] = ["property"=> "n10xms__live_epoch", "value"=> $apock];
		$hub_data['properties'][] = ["property"=> "n10xms__confirmation_link", "value"=> $client_data->user->thank_you_url];
		$hub_data['properties'][] = ["property"=> "n10xms__replay_link", "value"=> $client_data->user->replay_room_url];
		$hub_data['properties'][] = ["property"=> "n10xms__webinar_status", "value"=> 'Upcoming'];
		$hub_data['properties'][] = ["property"=> "n10xms__webinar_registration", "value"=> 'Upcoming'];
		// $hub_data['properties'][] = ["property"=> "n10xms__date_registered", "value"=> $apock];



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

    public function d7step1()
    {
        $file = URL::to('/'). '/download/7-Step-Social Media.pdf';

        $headers = [
              'Content-Type: application/pdf',
           ];
           return redirect($file);
    }

    public function d7step2()
    {
        $file = URL::to('/'). '/download/7-Steps-Internet-Marketing.pdf';

        $headers = [
              'Content-Type: application/pdf',
           ];
           return redirect($file);
    }




}
