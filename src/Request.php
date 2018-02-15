<?php
namespace Plokko\PhpFcmV1;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JsonSerializable;

class Request implements JsonSerializable
{
    private
        /**@var string OAUTH 2 Google access token
        $accessToken,
        /**@var ClientInterface **/
        $client;

    public
        /**@var boolean Flag for testing the request without actually delivering the message. **/
        $validate_only = false;

    /**
     * Request constructor.
     * @param $project_id string Required. Firebase project id
     * @param $accessToken string OAuth 2 access token
     */
    function __construct($message)
    {
        $this->apiUrl = str_replace(['{parent}'],['projects/'.$project_id],$this->apiUrl);
        $this->accessToken = $accessToken;
        $this->client = new Client();
    }

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
            'message'       => $message,
        ];
    }
}