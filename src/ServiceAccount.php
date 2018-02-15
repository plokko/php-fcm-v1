<?php
namespace Plokko\PhpFcmV1;

use Google_Client;
use GuzzleHttp\ClientInterface;

class ServiceAccount
{
    private
        $apiUrl = 'https://fcm.googleapis.com/v1/{parent}/messages:send',
        $gclient;


    /**
     * ServiceAccount constructor.
     * @param string $auth_config_file
     * @throws \Google_Exception
     */
    function __construct($auth_config_file)
    {
        $this->gclient = new Google_Client();
        $this->gclient->setAuthConfig($auth_config_file);
        $this->gclient->setScopes(['https://www.googleapis.com/auth/firebase.messaging','https://www.googleapis.com/auth/cloud-platform']);
    }

    function authorize(ClientInterface $request=null){
         return $this->gclient->authorize($request);
    }

    function submit(Request $request){

    }
}