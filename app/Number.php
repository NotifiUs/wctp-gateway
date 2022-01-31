<?php

namespace App;

use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class Number extends Model
{
    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function getType()
    {
        if( $this->carrier->api == 'twilio' )
        {
            return substr( $this->identifier, 0, 2);
        }

        return "PN";
    }

    public function getFriendlyType(){
        if( $this->carrier->api == 'twilio' )
        {
            if( Str::startsWith( $this->identifier, 'MG') )
            {
                return "Messaging Service";
            }
        }

        return "Phone Number";
    }

    public function provision()
    {
        if( $this->carrier->api == 'twilio' )
        {
            try{
                $twilio = new Client( $this->carrier->twilio_account_sid, decrypt( $this->carrier->twilio_auth_token ) );

                if( $this->getType() == 'MG' )
                {
                    $twilio->messaging->v1->services($this->identifier)
                        ->update([
                            'inboundRequestUrl' => secure_url("/sms/inbound/{$this->identifier}/primary" ),
                            'inboundMethod' => 'POST',
                            'fallbackUrl' => secure_url("/sms/inbound/{$this->identifier}/fallback" ),
                            'fallbackMethod' => 'POST',
                            'statusCallback' => secure_url("/sms/callback/{$this->identifier}/status"),
                            //'statusCallbackMethod' => 'POST', //not in docs
                        ]);
                }
                else
                {
                    $twilio
                        ->incomingPhoneNumbers( $this->identifier )
                        ->update(array(
                                'smsApplicationSid' => '',
                                'smsFallbackMethod' => 'POST',
                                'smsFallbackUrl' => secure_url("/sms/inbound/{$this->identifier}/fallback"),
                                'smsMethod' => 'POST',
                                'smsUrl' => secure_url("/sms/inbound/{$this->identifier}/primary" ),
                                'statusCallback' => secure_url("/sms/callback/{$this->identifier}/status"),
                                'statusCallbackMethod' => 'POST',
                            )
                        );
                }


            }
            catch( Exception $e ) { return false; }
        }
        elseif( $this->carrier->api == 'thinq' )
        {
            $ipify = new Guzzle(['base_uri' => 'https://api.ipify.org']);
            try{ $response = $ipify->get( '/'); } catch( Exception $e ){ Log::debug($e->getMessage());return false; }
            $ip = (string)$response->getBody();

            $validator = Validator::make(['ip' => $ip], ['ip' => 'required|ip']);
            if( $validator->fails() ) { Log::debug('IP validation failed' );return false; }

            try{
                $thinq = new Guzzle([
                    'base_uri' => 'https://api.thinq.com',
                    'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                    'headers' => [ 'content-type' => 'application/json' ],
                ]);
            }
            catch( Exception $e ){ Log::debug($e->getMessage());return false; }

            //get all current ip whitelists
            $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/ip";

            try{
                $res = $thinq->get($url);
            }
            catch( Exception $e ){ Log::debug($e->getMessage());return false; }

            $hasIP = false;
            $list = json_decode( (string)$res->getBody(), true );
            foreach( $list['rows'] as $row )
            {
                if( $row['ip'] == $ip ){  $hasIP = true; break; }
            }

            //if our public ip is in the whitelist list, continue
            //if our public ip is not in the whitlelist list, add it
            if( ! $hasIP )
            {
                $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/ip/{$ip}";
                try{
                    $res = $thinq->post($url);
                }
                catch( Exception $e ){ Log::debug($e->getMessage());return false; }
            }

            //get all current sms routing profiles
            $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/profile";
            try{
                $res = $thinq->get($url);
            }
            catch( Exception $e ){ Log::debug($e->getMessage());return false; }

            $hasProfile = false;
            $list = json_decode( (string)$res->getBody(), true );
            $sms_routing_profile = '';

            foreach( $list['rows'] as $row )
            {
               if( $row['name'] == $this->identifier )
               {
                   $sms_routing_profile = $row['id'];
                   $hasProfile = true;
                   break;
               }
            }

            //if our url is in the profile list, continue
            //if our url is not in the profile list, add it
            if( ! $hasProfile )
            {
                $webhook = secure_url("/sms/inbound/{$this->identifier}/primary" );
                $body = [
                    "sms_routing_profile" => [
                        'name' => $this->identifier,
                        'url' => $webhook,
                        'attachment_type' => 'url'
                    ]
                ];

                $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/profile";
                try{
                    $res = $thinq->post($url, ['body' => json_encode( $body ) ]);
                }
                catch( Exception $e ){ Log::debug($e->getMessage());return false; }

                $profile = json_decode( (string)$res->getBody(), true );

                $sms_routing_profile = $profile['id'];

            }
            else
            {
                //update it so we enesure it has our most recent url
                $webhook = secure_url("/sms/inbound/{$this->identifier}/primary" );
                $body = [
                    "sms_routing_profile" => [
                        'name' => $this->identifier,
                        'url' => $webhook,
                        'attachment_type' => 'url'
                    ]
                ];

                $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/profile/{$sms_routing_profile}";
                try{
                    $res = $thinq->put($url, ['body' => json_encode( $body ) ]);
                }
                catch( Exception $e ){ Log::debug($e->getMessage());return false; }

            }

            //set our outbound message status url
            //update it so we enesure it has our most recent url
            $webhook = secure_url("/sms/callback/{$this->identifier}/status" );
            $body = [
                "settings" => [
                    'deliveryConfirmationUrl' => $webhook,
                    'deliveryNotificationType' => 'form-data',
                ]
            ];

            $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/settings/outbound";
            try{
                $res = $thinq->post($url, ['body' => json_encode( $body ) ]);
            }
            catch( Exception $e ){ Log::debug($e->getMessage());return false; }

            //create a feature order to do the following:
            //  enable SMS
            //  associate sms routing profile
            $body = [
                "order" => [
                   "tns" => [
                       [
                           "sms_routing_profile_id" => $sms_routing_profile,
                           "features" => [ "cnam" => false, "e911" => false, "sms" => true ],
                           "did" => $this->identifier,
                       ]
                   ]
                ]
            ];

            $url = "/account/{$this->carrier->thinq_account_id}/origination/did/features/create";
            try{
                $res = $thinq->post($url, ['body' => json_encode( $body ) ]);
            }
            catch( Exception $e ){ Log::debug($e->getMessage());return false; }

            $order = json_decode( (string)$res->getBody(), true );

            // complete feature order
            $url = "/account/{$this->carrier->thinq_account_id}/origination/did/features/complete/{$order['order']['id']}";
            try{
                $res = $thinq->post($url);
            }
            catch( Exception $e ){ Log::debug($e->getMessage());return false; }

        }
        else
        {
            return false;
        }

        return true;
    }

    public function getCarrierDetails()
    {
        if( $this->carrier->api == 'twilio')
        {
            try {
                $twilio = new Client( $this->carrier->twilio_account_sid, decrypt( $this->carrier->twilio_auth_token ) );

                if( $this->getType() == 'MG' )
                {
                    $serviceAddons = [];
                    $results = $twilio->messaging->v1->services( $this->identifier )->fetch();
                    foreach( $results->phoneNumbers->read(100, 100) as $num )
                    {
                        $serviceAddons['numbers'][] = $num->toArray();
                    }

                    foreach( $results->shortCodes->read(100, 100) as $shortcode )
                    {
                        $serviceAddons['shortcodes'][] = $shortcode->toArray();
                    }
                    return Arr::dot(array_merge( $results->toArray(), $serviceAddons ) );
                }
                else {

                    $results = $twilio->incomingPhoneNumbers($this->identifier)->fetch();
                    return Arr::dot($results->toArray() );
                }

            }catch( Exception $e ) {return [];}



        }
        elseif( $this->carrier->api == 'thinq')
        {
            $url = "/origination/did/search2/did/{$this->carrier->thinq_account_id}";
            $guzzle = new Guzzle(
                ['base_uri' => 'https://api.thinq.com',]
            );
            try{
                $res = $guzzle->get( $url, ['auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)]]);
            }
            catch( RequestException $e ) {
               return [];
            }
            catch( Exception $e ){
                return [];
            }

            $thinq_numbers = json_decode( (string)$res->getBody(), true );

            if( $thinq_numbers['total_rows'] > 0 )
            {
                foreach( $thinq_numbers['rows'] as $thinq_number )
                {
                    if( $this->identifier == $thinq_number['id'] )
                    {
                        return $thinq_number;
                    }
                }
            }

            return [];
        }

        return [];
    }
}
