<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use App\PaymentPendingList;

class HubspotController extends Controller
{
    public function valueFilter(Request $request)
    {

        $data = array(
            'transaction_id' => $request->properties['transaction_id']['value'],
            'amount' => $request->properties['amount']['value']
        );

        $client = new Client();

        $url = 'http://go.grantcardone.com/aff_lsr?offer_id='.$request->properties['offer_id']['value'].'&amount='.$data['amount'].'&transaction_id='. $data['transaction_id'];

        $response = $client->request('POST', $url, 
            [
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );

        $result = $response->getBody()->getContents();
        $result = json_decode($result);

        return response()->json(array($result, $url));
    }
    
    public function valueFilter2(Request $request)
    {

        $data = array(
            'transaction_id' => $request->properties['transaction_id']['value'],
            'amount' => $request->properties['amount']['value']
        );

        $client = new Client();

        $url = 'http://go.gcexclusive.com/aff_lsr?offer_id='.$request->properties['offer_id']['value'].'&amount='.$data['amount'].'&transaction_id='. $data['transaction_id'];

        $response = $client->request('POST', $url, 
            [
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );

        $result = $response->getBody()->getContents();
        $result = json_decode($result);

        return response()->json(array($result, $url));
    }

    public function paymentFilter(Request $request)
    {
        $var = $request->transaction_data;
        $var = json_decode($var);
        // $var = $this->datasample();

        // if(empty($var->resource->payer)){
        //     return response()->json('Error: Unidentified resource->payer');
        // }

        $hubspot_data = array();

        if(!empty($var->resource_type) && $var->resource_type == "refund"){
            if(empty($var->resource->billing_agreement_id)){
                return response()->json('Error: Unidentified!! Billing id');
            }

            $hubspot_data = $this->refundState($var);

            $data['filterGroups'][0]['filters'][0]['propertyName'] = 'n10xis_paypal_subscription_id';
            $data['filterGroups'][0]['filters'][0]['operator'] = 'EQ';
            $data['filterGroups'][0]['filters'][0]['value'] = $var->resource->billing_agreement_id;

        } elseif($var->resource->state == 'completed'){
            $hubspot_data = $this->completeState($var);

            $data['filterGroups'][0]['filters'][0]['propertyName'] = 'n10xis_paypal_subscription_id';
            $data['filterGroups'][0]['filters'][0]['operator'] = 'EQ';
            $data['filterGroups'][0]['filters'][0]['value'] = $var->resource->billing_agreement_id;

        } elseif($var->resource->state == 'Active') {
            $hubspot_data = $this->activeState($var);

            $data['filterGroups'][0]['filters'][0]['propertyName'] = 'n10xis_paypal_subscription_id';
            $data['filterGroups'][0]['filters'][0]['operator'] = 'EQ';
            $data['filterGroups'][0]['filters'][0]['value'] = $var->resource->id;

        } elseif($var->resource->state == 'Canceled') {
            $hubspot_data = $this->cancelState($var);

            $data['filterGroups'][0]['filters'][0]['propertyName'] = 'n10xis_paypal_subscription_id';
            $data['filterGroups'][0]['filters'][0]['operator'] = 'EQ';
            $data['filterGroups'][0]['filters'][0]['value'] = $var->resource->id;

        } elseif($var->resource->state == 'denied') {
            $hubspot_data = $this->deniedState($var);

            $data['filterGroups'][0]['filters'][0]['propertyName'] = 'n10xis_paypal_subscription_id';
            $data['filterGroups'][0]['filters'][0]['operator'] = 'EQ';
            $data['filterGroups'][0]['filters'][0]['value'] = $var->resource->billing_agreement_id;


        } elseif($var->resource->state == 'pending') {
            $hubspot_data = $this->pendingState($var);

            $data['filterGroups'][0]['filters'][0]['propertyName'] = 'n10xis_paypal_subscription_id';
            $data['filterGroups'][0]['filters'][0]['operator'] = 'EQ';
            $data['filterGroups'][0]['filters'][0]['value'] = $var->resource->billing_agreement_id;


        } elseif($var->resource->state == 'UNDER_REVIEW') {
            return response()->json('Error: We dont save record from Unidentified!!');
        } else {
            return response()->json('Error: We dont save record from Unidentified!!');
        }

        $key = '';
        $url = 'https://api.hubapi.com/crm/v3/objects/contacts/search?hapikey='. $key;

        $client = new Client();
        $res = $client->request('POST', $url, 
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' =>    $data
            
            ]
        );

        $result = $res->getBody()->getContents();
        $result = json_decode($result);

        if($result->total <= 0){
            return response()->json('Didnt match in hubspot data');
        }

        $email = $result->results[0]->properties->email;

        if(empty($email)){
            $email = $result->email;
            if(empty($email)){
                return response()->json('Cannot Map Email Address');
            }
        }

        $url = 'https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/'. $email .'/?hapikey=' . $key;
        $client = new Client();
        $res = $client->request('POST', $url, 
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' =>    $hubspot_data
                
                ]
            );


        $result = $res->getBody()->getContents();
        $result = json_decode($result);


        return response()->json('Success!');
    }

    private function refundState($request)
    {
        $resource_type = !empty($request->resource_type) ? $request->resource_type : '';
        $event_type = !empty($request->event_type) ? $request->event_type : '';
        $billing_agreement_id = !empty($request->resource->billing_agreement_id) ? $request->resource->billing_agreement_id : '';
        $update_time = !empty($request->resource->update_time) ? $request->resource->update_time : '';

        $hub_data = array();
        $hub_data['properties'][] = ["property" => "n10xis_paypal_resource_type", "value"=> $resource_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_event_type", "value"=> $event_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_cycles_completed", "value"=> 0];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_next_billing_date", "value"=> 'refund'];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_state", "value"=> 'refund'];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_id", "value"=> $billing_agreement_id];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_date", "value"=> $update_time];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_amount", "value"=> 0];

        return $hub_data;
    }

    private function completeState($request)
    {

        $resource_type = !empty($request->resource_type) ? $request->resource_type : '';
        $event_type = !empty($request->event_type) ? $request->event_type : '';
        $soft_descriptor = !empty($request->resource->soft_descriptor) ? $request->resource->soft_descriptor : '';
        $state = !empty($request->resource->state) ? $request->resource->state : '';
        $billing_agreement_id = !empty($request->resource->billing_agreement_id) ? $request->resource->billing_agreement_id : '';
        $update_time = !empty($request->resource->update_time) ? $request->resource->update_time : '';
        $total = !empty($request->resource->amount->total) ? $request->resource->amount->total : '';

        $hub_data = array();
        $hub_data['properties'][] = ["property" => "n10xis_paypal_resource_type", "value"=> $resource_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_event_type", "value"=> $event_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_cycles_completed", "value"=> 1];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_next_billing_date", "value"=> 'complete'];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_description", "value"=> $soft_descriptor];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_failed_payment_count", "value"=> 'none'];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_state", "value"=> $state];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_id", "value"=> $billing_agreement_id];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_date", "value"=> $update_time];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_amount", "value"=> $total];

        return $hub_data;
    }

    private function activeState($request)
    {
        $res = $request->resource;
        $resource_type = !empty($request->resource_type) ? $request->resource_type : '';
        $event_type = !empty($request->event_type) ? $request->event_type : '';
        $cycles_completed = !empty($res->agreement_details->cycles_completed) ? $res->agreement_details->cycles_completed : '';
        $next_billing_date = !empty($res->agreement_details->next_billing_date) ? $res->agreement_details->next_billing_date : '';
        $description = !empty($res->description) ? $res->description : '';
        $failed_payment_count = !empty($res->agreement_details->failed_payment_count) ? $res->agreement_details->failed_payment_count :'';
        $state = !empty($res->state) ? $res->state : '';
        $id = !empty($res->id) ? $res->id : '';

        $hub_data = array();
     
        $hub_data['properties'][] = ["property" => "n10xis_paypal_resource_type", "value"=> $resource_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_event_type", "value"=> $event_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_cycles_completed", "value"=> $cycles_completed];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_next_billing_date", "value"=> $next_billing_date];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_description", "value"=> $description];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_failed_payment_count", "value"=> $failed_payment_count];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_state", "value"=> $state];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_id", "value"=> $id];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_date", "value"=> $res->update_time];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_amount", "value"=> $res->amount->total];

        return $hub_data;

    }

    private function cancelState($request)
    {
        $res = $request->resource;

        $resource_type = !empty($request->resource_type) ? $request->resource_type : '';
        $event_type = !empty($request->event_type) ? $request->event_type : '';
        $cycles_completed = !empty($res->agreement_details->cycles_completed) ? $res->agreement_details->cycles_completed : '';
        $next_billing_date = !empty($res->agreement_details->next_billing_date) ? $res->agreement_details->next_billing_date : '';
        $description = !empty($res->description) ? $res->description : '';
        $failed_payment_count = !empty($res->agreement_details->failed_payment_count) ? $res->agreement_details->failed_payment_count : '';
        $state = !empty($res->state) ? $res->state : '';
        $id = !empty($res->id) ? $res->id : '';
        $last_payment_amount = !empty($res->agreement_details->last_payment_amount) ? $res->agreement_details->last_payment_amount : '';
     
        $hub_data = array();
        $hub_data['properties'][] = ["property" => "n10xis_paypal_resource_type", "value"=> $resource_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_event_type", "value"=> $event_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_cycles_completed", "value"=> $cycles_completed];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_next_billing_date", "value"=> $next_billing_date];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_description", "value"=> $description];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_failed_payment_count", "value"=> $failed_payment_count];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_state", "value"=> $state];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_id", "value"=> $id];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_date", "value"=> $res->update_time];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_amount", "value"=> $last_payment_amount];

        return $hub_data;
    }

    private function deniedState($request)
    {
        $res = $request->resource;

        $billing_agreement_id = !empty($res->billing_agreement_id) ? $res->billing_agreement_id : '';
        $state = !empty($res->state) ? $res->state : '';
        $total = !empty($res->amount->total) ? $res->amount->total : '';
     
        $hub_data = array();
        $hub_data['properties'][] = ["property" => "n10xis_paypal_resource_type", "value"=> $request->resource_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_event_type", "value"=> $request->event_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_cycles_completed", "value"=> 0];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_next_billing_date", "value"=> 'denied'];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_description", "value"=> $res->description];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_failed_payment_count", "value"=> 'none'];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_state", "value"=> $state];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_id", "value"=> $billing_agreement_id];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_date", "value"=> $res->update_time];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_amount", "value"=> $total];

        return $hub_data;

    }

    private function pendingState($request)
    {
        $res = $request->resource;

        $state = !empty($res->state) ? $res->state : '';
        $billing_agreement_id = !empty($res->billing_agreement_id) ? $res->billing_agreement_id : '';
        $total = !empty($res->amount->total) ? $res->amount->total : '';

     
        $hub_data = array();
        $hub_data['properties'][] = ["property" => "n10xis_paypal_resource_type", "value"=> $request->resource_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_event_type", "value"=> $request->event_type];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_cycles_completed", "value"=> 0];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_next_billing_date", "value"=> 'pending'];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_description", "value"=> $res->description];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_failed_payment_count", "value"=> 'none'];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_state", "value"=> $state];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_subscription_id", "value"=> $billing_agreement_id];
        // $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_date", "value"=> $res->update_time];
        $hub_data['properties'][] = ["property" => "n10xis_paypal_last_payment_amount", "value"=> $total];

        return $hub_data;
    }

    public function updateIpAdressMatch(Request $request)
    {

        $var = $request->transaction_data;

        $key = '';
        $url = 'https://api.hubapi.com/crm/v3/objects/contacts/search?hapikey='. $key;

        $data['filterGroups'][0]['filters'][1]['propertyName'] = 'n10xiw_ip_address';
        $data['filterGroups'][0]['filters'][1]['operator'] = 'EQ';
        $data['filterGroups'][0]['filters'][1]['value'] = $var->resource->n10xis_buyer_ip_address ;


        $client = new Client();
        $res = $client->request('POST', $url, 
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' =>    $data
            
            ]
        );

        $result = $res->getBody()->getContents();
        $result = json_decode($result);

        if(!$result){
            return response()->json('Didnt match in hubspot data');
        }

        $hub_data['properties'][] = ["property" => "n10xis_buyer_ip_address_match", "value"=> 'YES'];

        
        $url = 'https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/'. $result->email .'/?hapikey=' . $key;
        $client = new Client();
        $res = $client->request('POST', $url, 
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' =>    $hubspot_data
                
                ]
            );


        $result = $res->getBody()->getContents();
        $result = json_decode($result);


        return response()->json($result);
        
    }


    private function datasample()
    {
        return (object) array (
              'id' => 'WH-9JN52222XD243971S-070650127E136344J',
              'event_version' => '1.0',
              'create_time' => '2020-08-20T02:27:45.372Z',
              'resource_type' => 'Agreement',
              'event_type' => 'BILLING.SUBSCRIPTION.CREATED',
              'summary' => 'A billing subscription was created',
              'resource' => 
              (object) array (
                'agreement_details' => 
                (object) array (
                  'outstanding_balance' => 
                  (object) array (
                    'currency' => 'USD',
                    'value' => '0.00',
                  ),
                  'cycles_remaining' => '-1',
                  'cycles_completed' => '1',
                  'next_billing_date' => '2021-08-19T10:00:00Z',
                  'last_payment_date' => '2020-08-20T02:27:33Z',
                  'last_payment_amount' => 
                  (object) array (
                    'currency' => 'USD',
                    'value' => '997.00',
                  ),
                  'failed_payment_count' => '0',
                ),
                'description' => '10X System Annual Paypal',
                'links' => 
                (object) array (
                  0 => 
                  (object) array (
                    'href' => 'https://node-subscriptionmgmtserv-vip.live21.ccg13.slc.paypalinc.com:19472/v1/payments/billing-agreements/I-SJJJ4JKP8LEA/suspend',
                    'rel' => 'suspend',
                    'method' => 'POST',
                  ),
                  1 => 
                  (object) array (
                    'href' => 'https://node-subscriptionmgmtserv-vip.live21.ccg13.slc.paypalinc.com:19472/v1/payments/billing-agreements/I-SJJJ4JKP8LEA/re-activate',
                    'rel' => 're_activate',
                    'method' => 'POST',
                  ),
                  2 => 
                  (object) array (
                    'href' => 'https://node-subscriptionmgmtserv-vip.live21.ccg13.slc.paypalinc.com:19472/v1/payments/billing-agreements/I-SJJJ4JKP8LEA/cancel',
                    'rel' => 'cancel',
                    'method' => 'POST',
                  ),
                  3 => 
                  (object) array (
                    'href' => 'https://node-subscriptionmgmtserv-vip.live21.ccg13.slc.paypalinc.com:19472/v1/payments/billing-agreements/I-SJJJ4JKP8LEA/bill-balance',
                    'rel' => 'self',
                    'method' => 'POST',
                  ),
                  4 => 
                  (object) array (
                    'href' => 'https://node-subscriptionmgmtserv-vip.live21.ccg13.slc.paypalinc.com:19472/v1/payments/billing-agreements/I-SJJJ4JKP8LEA/set-balance',
                    'rel' => 'self',
                    'method' => 'POST',
                  ),
                ),
                'id' => 'I-SJJJ4JKP8LEA',
                'state' => 'Active',
                'shipping_address' => 
                (object) array (
                  'recipient_name' => '',
                  'line1' => '707 SEIGLE AVE',
                  'line2' => 'APT 344',
                  'city' => 'CHARLOTTE',
                  'state' => 'NC',
                  'postal_code' => '28204',
                  'country_code' => 'US',
                ),
                'payer' => 
                (object) array (
                  'paymentMethodType' => 'PAYPAL',
                  'payment_method' => 'paypal',
                  'status' => 'verified',
                  'payer_info' => 
                  (object) array (
                    'email' => 'derek.milgate58@gmail.com',
                    'first_name' => 'Derek',
                    'last_name' => 'Milgate',
                    'payer_id' => 'VEPWPBG7X2DPY',
                    'shipping_address' => 
                    (object) array (
                      'recipient_name' => '',
                      'line1' => '707 SEIGLE AVE',
                      'line2' => 'APT 344',
                      'city' => 'CHARLOTTE',
                      'state' => 'NC',
                      'postal_code' => '28204',
                      'country_code' => 'US',
                    ),
                  ),
                ),
                'plan' => 
                (object) array (
                  'payment_definitions' => 
                  (object) array (
                    0 => 
                    (object) array (
                      'type' => 'REGULAR',
                      'frequency' => 'YEAR',
                      'amount' => 
                      (object) array (
                        'currency' => 'USD',
                        'value' => '997.00',
                      ),
                      'cycles' => '0',
                      'frequency_interval' => '1',
                      'charge_models' => 
                      (object) array (
                        0 => 
                        (object) array (
                          'type' => 'TAX',
                          'amount' => 
                          (object) array (
                            'currency' => 'USD',
                            'value' => '0.00',
                          ),
                        ),
                        1 => 
                        (object) array (
                          'type' => 'SHIPPING',
                          'amount' => 
                          (object) array (
                            'currency' => 'USD',
                            'value' => '0.00',
                          ),
                        ),
                      ),
                    ),
                  ),
                  'merchant_preferences' => 
                  (object) array (
                    'setup_fee' => 
                    (object) array (
                      'currency' => 'USD',
                      'value' => '0.00',
                    ),
                    'auto_bill_amount' => 'YES',
                    'max_fail_attempts' => '0',
                  ),
                ),
                'start_date' => '2020-08-19T04:00:00Z',
              ),
              'links' => 
              (object) array (
                0 => 
                (object) array (
                  'href' => 'https://api.paypal.com/v1/notifications/webhooks-events/WH-9JN52222XD243971S-070650127E136344J',
                  'rel' => 'self',
                  'method' => 'GET',
                ),
                1 => 
                (object) array (
                  'href' => 'https://api.paypal.com/v1/notifications/webhooks-events/WH-9JN52222XD243971S-070650127E136344J/resend',
                  'rel' => 'resend',
                  'method' => 'POST',
                ),
              ),
            );
    }
    

}
