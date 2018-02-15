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
        /**@var string OAUTH 2 Google access token
        $accessToken,
        /**@var ClientInterface|null **/
        $client;

    public
        /**@var boolean Flag for testing the request without actually delivering the message. **/
        $validate_only = false;

    /**
     * Request constructor.
     * @param Message $message Message to be sent
     * @param bool $validate_only Flag for testing the request without actually delivering the message.
     */
    function __construct(Message $message,$validate_only=false,ClientInterface $client=null)
    {
        $this->message = $message;
        $this->validate_only=$validate_only;
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

    /**
     * @param ServiceAccount $account
     * @throws FcmError
     * @throws FcmError|\GuzzleHttp\Exception\GuzzleException
     */
    public function send(ServiceAccount $account){
        $payload = $this->jsonSerialize();
        // Add OAuth 2.0 token to the request
        $client = $account->authorize($this->client);
        $apiUrl = $account->getFcmApiV1Url();

        echo(json_encode($payload,JSON_PRETTY_PRINT));

        try{
            $rq = $client->request('POST',$apiUrl,[
                'json' => $payload,
            ]);

            echo
                "Result:\n status code:".$rq->getStatusCode()."\t".$rq->getReasonPhrase().
                "\n ---- Headers: ----\n\t".implode("\n\t",$rq->getHeaders())."\n--body--\n".$rq->getBody();
        }catch(RequestException $e){

            $request = $e->getRequest();
            $response = $e->getResponse();
            $json = json_decode($response->getBody(),true);


            if($json && isset($json['error']['details'])){
                foreach($json['error']['details'] AS $d){
                    if( $d['@type']==='type.googleapis.com/google.firebase.fcm.v1.FcmError'){
                        throw new FcmError($d['errorCode'],$e);
                    }
                }
            }

            throw $e;
        }
    }
}