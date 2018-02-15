<?php
namespace Plokko\phpFCM;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Plokko\PhpFCM\Targets\Target;

class Request
{
    private
        $apiUrl = 'https://fcm.googleapis.com/v1/{parent}/messages:send',
        /**@var ClientInterface **/
        $client;
    public
        /**@var boolean Flag for testing the request without actually delivering the message. **/
        $validate_only = false;

    function __construct($parent)
    {
        $this->apiUrl = str_replace(['{parent}'],[$parent],$this->apiUrl);
        $this->client = new Client();
    }

    function setHttpClient(ClientInterface $client){
        $this->client = $client;
    }

    function validateOnly($validate=true){
        $this->validate_only=$validate;
    }

    private function getBody(Message $message,Target $target){
        $message = array_merge($message->jsonSerialize(),$target->jsonSerialize());
        return [
            'validate_only' => $this->validate_only,
            'message'       => $message,
        ];
    }

    function send(Message $message,Target $target){
        $body = $this->getBody($message,$target);

    }
}