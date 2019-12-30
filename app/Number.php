<?php

namespace App;

use Exception;
use Twilio\Rest\Client;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\RequestException;

class Number extends Model
{
    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function provision()
    {
        if( $this->carrier->api == 'twilio' )
        {
            try{
                $twilio = new Client( $this->carrier->twilio_account_sid, decrypt( $this->carrier->twilio_auth_token ) );
                $number = $twilio
                    ->incomingPhoneNumbers( $this->identifier )
                    ->update(array(
                        'smsApplicationSid' => '',
                        'smsFallbackMethod' => 'POST',
                        'smsFallbackUrl' => secure_url("/sms/inbound/{$this->identifier}/fallback"),
                        'smsMethod' => 'POST',
                        'smsUrl' => secure_url("/sms/inbound/{$this->identifier}/primary" ),
                        'statusCallback' => secure_url("/sms/inbound/{$this->identifier}/status"),
                        'statusCallbackMethod' => 'POST',
                    )
                );
            }
            catch( Exception $e ) { return false; }
        }
        elseif( $this->carrier->api == 'thinq' )
        {
            $ipify = new Guzzle(['base_uri' => 'https://api.ipify.org']);
            try{ $response = $ipify->get( '/'); } catch( Exception $e ){ return false; }
            $ip = (string)$response->getBody();

            $validator = Validator::make(['ip' => $ip], ['ip' => 'required|ip']);
            if( $validator->fails() ) { return false; }

            try{
                $thinq = new Guzzle([
                    'base_uri' => 'https://api.thinq.com',
                    'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                    'headers' => [ 'content-type' => 'application/json' ],
                ]);
            }
            catch( Exception $e ){ return false; }

            //get all current ip whitelists
            $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/ip";

            try{
                $res = $thinq->get($url);
            }
            catch( Exception $e ){ return false; }

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
                catch( Exception $e ){ return false; }
            }

            //get all current sms routing profiles
            $url = "/account/{$this->carrier->thinq_account_id}/product/origination/sms/profile";
            try{
                $res = $thinq->get($url);
            }
            catch( Exception $e ){ return false; }

            $hasProfile = false;
            $list = json_decode( (string)$res->getBody(), true );
            $sms_routing_profile = '';

            foreach( $list['rows'] as $row )
            {
               if( $row['url'] == secure_url("/sms/inbound/{$this->identifier}/primary" ) )
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
                catch( Exception $e ){ return false; }

                $profile = json_decode( (string)$res->getBody(), true );

                $sms_routing_profile = $profile['id'];

            }

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
            catch( Exception $e ){ return false; }

            $order = json_decode( (string)$res->getBody(), true );

            // complete feature order
            $url = "/account/{$this->carrier->thinq_account_id}/origination/did/features/complete/{$order['order']['id']}";
            try{
                $res = $thinq->post($url);
            }
            catch( Exception $e ){ return false; }

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
                $incoming_phone_number = $twilio->incomingPhoneNumbers( $this->identifier )
                    ->fetch();

            } catch( Exception $e ) {return [];}

            return $incoming_phone_number->toArray();

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
