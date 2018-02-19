<?php
namespace Plokko\PhpFcmV1;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use JsonSerializable;

/**
 * FCM Request
 * @package Plokko\PhpFcmV1
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages/send#request-body
 */
class Request implements JsonSerializable
{
    private
        /**@var ServiceAccount**/
        $serviceAccount,
        /**@var ClientInterface|null **/
        $client;

    public
        /**@var boolean Flag for testing the request without actually delivering the message. **/
        $validate_only = false;

    /**
     * Request constructor.
     * @param bool $validate_only Flag for testing the request without actually delivering the message.
     */
    function __construct(ServiceAccount $account,$validate_only=false,ClientInterface $client=null)
    {
        $this->serviceAccount = $account;
        $this->validate_only = $validate_only;
        $this->client = $client;
    }

    /**
     * Set a custom Http client (GuzzleHttp)
     * @param ClientInterface $client Client that will be used in the request
     */
    function setHttpClient(ClientInterface $client){
        $this->client = $client;
    }

    function validateOnly($validate=true){
        $this->validate_only=$validate;
    }

    public function jsonSerialize()
    {
        return [
            'validate_only' => $this->validate_only,
            'message'       => $this->message->jsonSerialize(),
        ];
    }

    private function getPayload(Message $message){
        return [
            'validate_only' => $this->validate_only,
            'message'       => $message->getPayload(),
        ];
    }

    /**
     * @param Message $message
     * @throws FcmError
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return string|null submitted message name
     * @internal Only use Message submit
     */
    public function submit(Message $message){
        $payload = $this->getPayload($message);
        // Add OAuth 2.0 token to the request
        $client = $this->serviceAccount->authorize($this->client);
        // Get FCM v1 Api URL
        $apiUrl = $this->serviceAccount->getFcmApiV1Url();

        $key = 'AAAA469wX50:APA91bG6Jpfuj2T3efqiCUA3xc5setHCixMwDkYRITq2VVEhb5j7m3tI83ZSk6OAC7glcNSI_qRFXLNoFRJekzLEYkP9Kt2tNkgMUZTUf0SlslHVDRyTZwJprFXBEsI6H9mLvxcnaWSk';
        try{
            $rq = $client->request('POST',$apiUrl,[
                'json' => $payload,
                'headers'=> [
                    'Authentication' => http_build_query(['authentication_key'=>$key,])
                ]
            ]);

            echo
                "Result:\n status code:".$rq->getStatusCode()."\t".$rq->getReasonPhrase().
                "\n--body--\n".$rq->getBody();

            $json = json_decode($rq->getBody(),true);

            return ($json && isset($json['message']['name']))?$json['message']['name']:null;

        }catch(RequestException $e){
            $response = $e->getResponse();
            $json = json_decode($response->getBody(),true);

            echo $response->getBody();
            if($json && isset($json['error']['details'])){

                $error = $e;
                foreach($json['error']['details'] AS $d){

                    switch($d['@type']){
                        case 'type.googleapis.com/google.firebase.fcm.v1.FcmError':
                            $error = new FcmError($d['errorCode'],$e->getCode(),$error);
                            break;
                        case 'type.googleapis.com/google.rpc.BadRequest':
                            $error = new BadRequest($d['fieldViolations'],$e->getCode(),$error);
                            break;
                        default:
                    }
                }
                throw $error;

            }

            throw $e;
        }
    }
}