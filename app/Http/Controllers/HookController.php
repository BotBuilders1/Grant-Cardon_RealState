<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ReceiverHook;


class HookController extends Controller
{
    
    public function kartraWebHookSave(Request $request)
    {

		$data = new ReceiverHook();
		$data->json = json_encode($request->all());

		$data->save();

    }

    public function kartraWebHookCall()
    {
    	$data = ReceiverHook::orderBy('id','desc')->first();

    	if(!empty($data)){
	    	$r = json_decode($data->json);

	    	$array = array(
	                    'app_id' => '',
	                    'api_key' => '',
	                    'api_password' => '',
	                    'lead' => array(
	                        // 'email' => $request['lead']['email'],
	                        // 'first_name' => $request['lead']['first_name'],
	                        // 'last_name' => $request['lead']['last_name']
	                    ),
	                    'actions' => array(
	                        '0' => array(
	                               'cmd' => 'edit_lead',
	                        )
	                    )
	                );

	        if(!empty($r->lead->email)) {
	        	$array['lead']['email'] = $r->lead->email;
	        }else{
	        	$array['lead']['email'] = 'ormojames@gmail.com';
	        }

	        if(!empty($r->lead->first_name)) {
	        	$array['lead']['first_name'] = $r->lead->first_name;
	        }
	        if(!empty($r->lead->last_name)) {
	        	$array['lead']['last_name'] = $r->lead->last_name;
	        }

	        $tag = '';
	        if(!empty($r->action_details->tag->tag_name)){
	        	$tag = $r->action_details->tag->tag_name;
	        }

	        switch ($tag) {
	            case 'OriginalSourceMainWebinar':
	                $array['lead']['OriginalWebinarRegistration'] = 'Main Webinar';
	            break;
	            case 'OriginalSourceMollyMahoneyPromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Molly Mahoney';
	            break;
	            case 'OriginalSourceChristineRowePromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Christina Rowe';
	            break;
	            case 'OriginalSourceRossHamiltonPromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Ross Hamilton';
	            break;
	            case 'OriginalSourceMattAndrewsPromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Matt Andrews';
	            break;
	            case 'OriginalSourceCathyDemersPromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Cathy Demers';
	            break;
	            case 'OriginalSourceJasonLucchesiPromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Jason Lucchesi';
	            break;
	            case 'OriginalSourceJasonStonePromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Jason Stone';
	            break;
	            case 'OriginalSourceJustOneDimePromo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Just One Dime';
	            break;
	            case 'OriginalSourceChristianSosaPormo':
	                $array['lead']['OriginalWebinarRegistration'] = 'Christian Sosa';
	            break;


	            case 'AMZ':
	                $array['lead']['Niche'] = 'Amazon';
	            break;
	            case 'REI':
	                $array['lead']['Niche'] = 'Real Estate Investing';
	            break;
	            case 'Standard':
	                $array['lead']['Niche'] = 'Standard';
	            break;


	            case 'BBCustomer':
	                $array['lead']['CanAccess'] = 'Yes';
	            break;


	            case 'LastRegistrationMainWebinar':
		            $array['lead']['LastWebinarRegistration'] = 'Main Webinar';
	            break;
	            case 'LastRegistrationMollyMahoney':
		            $array['lead']['LastWebinarRegistration'] = 'Molly Mahoney';
	            break;
	            case 'LastRegistrationChristinaRowe':
		            $array['lead']['LastWebinarRegistration'] = 'Christina Rowe';
	            break;
	            case 'LastRegistrationRossHamilton':
		            $array['lead']['LastWebinarRegistration'] = 'Ross Hamilton';
	            break;
	            case 'LastRegistrationMattAndrews':
		            $array['lead']['LastWebinarRegistration'] = 'Matt Andrews';
	            break;
	            case 'LastRegistrationCathyDemers':
		            $array['lead']['LastWebinarRegistration'] = 'Cathy Demers';
	            break;
	            case 'LastRegistrationJasonLucchesi':
		            $array['lead']['LastWebinarRegistration'] = 'Jason Lucchesi';
	            break;
	            case 'LastRegistrationNoSource':
		            $array['lead']['LastWebinarRegistration'] = 'No Source';
	            break;
	            case 'LastRegistrationJustOneDime':
		            $array['lead']['LastWebinarRegistration'] = 'No Source';
	            break;
	            case 'LastRegistrationJasonStone':
	                $array['lead']['LastWebinarRegistration'] = 'Jason Stone';
	            break;
	            case 'LastRegistrationChristianSosa':
	                $array['lead']['LastWebinarRegistration'] = 'Christian Sosa';
	            break;


	            default:
	                $array['lead']['OriginalWebinarRegistration'] = 'No Source';
	            break;
	        }

	        $ch = curl_init();

	        // CONNECT TO API, VERIFY MY API KEY AND PASSWORD AND GET THE LEAD DATA
	        curl_setopt($ch, CURLOPT_URL,"https://app.kartra.com/api");
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( 
	        	$array
	        ) );

	        // REQUEST CONFIRMATION MESSAGE FROM API…
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $server_output = curl_exec ($ch);
	        curl_close ($ch);
	        $server_json = json_decode($server_output);

			if($server_json->status == "Success"){
	        	$data->delete();
			}
		}

    }


    public function checker($id) 
    {
    	if(is_numeric($id)){
			$data = ReceiverHook::where('id', $id)->orderBy('id', 'desc')->first()->toArray();
    	}else{
			$data = ReceiverHook::orderBy('id', 'desc')->first()->toArray();
    	}

		$data['json'] = json_decode($data['json']);
		dd($data);
    }
}
