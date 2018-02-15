<?php
namespace Plokko\PhpFcmV1;

use Google_Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;

class ServiceAccount
{
    private
        $apiV1Url = 'https://fcm.googleapis.com/v1/{parent}/messages:send',
        /**@var Google_Client**/
        $gclient,
        /**@var array*/
        $authConfig;


    /**
     * ServiceAccount constructor.
     * @param string|array $authConfig
     * @throws \Google_Exception
     */
    function __construct($authConfig)
    {
        if (is_string($authConfig)) {
            if (!file_exists($authConfig)) {
                throw new InvalidArgumentException('FCM auth config file not found');
            }

            $json = file_get_contents($authConfig);

            if (!$authConfig = json_decode($json, true)) {
                throw new LogicException('invalid json for FCM auth config');
            }
        }

        $this->authConfig = $authConfig;
        $this->gclient = new Google_Client();
        $this->gclient->setAuthConfig($authConfig);
        $this->gclient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        //$this->gclient->addScope('https://www.googleapis.com/auth/cloud-platform');
    }

    function authorize(ClientInterface $request=null){
         return $this->gclient->authorize($request);
    }

    /**
     * Return the Firebase project id
     * @return string
     */
    function getProjectId(){
        if(empty($this->authConfig['project_id'])){
            throw new UnexpectedValueException('project_id not found in auth config file!');
        }
        return $this->authConfig['project_id'];
    }

    /**
     * Returns the Fcm V1 API url
     * @return string
     */
    function getFcmApiV1Url(){
        return str_replace('{parent}','projects/'.$this->getProjectId(),$this->apiV1Url);
    }
}