<?php
namespace Plokko\PhpFcmV1;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;

class ServiceAccount
{
    private
        $apiV1Url = 'https://fcm.googleapis.com/v1/{parent}/messages:send',
        /**@var ServiceAccountCredentials **/
        $credentials,
        /**@var array */
        $authConfig;


    /**
     * ServiceAccount constructor.
     * @param string|array $authConfig
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

        $this->authConfig   = $authConfig;
        $this->credentials  = new ServiceAccountCredentials('https://www.googleapis.com/auth/firebase.messaging',$this->authConfig);

    }

    function authorize(ClientInterface $request=null){
        $config = $request? $request->getConfig(): ['handler'=> HandlerStack::create(),];
        $middleware  = new AuthTokenMiddleware($this->credentials);

        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware);
        $config['auth'] = 'google_auth';

        return new Client($config);
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