<?php
namespace Plokko\PhpFcmV1;

use JsonSerializable;
use Plokko\PhpFcmV1\Message\AndroidConfig;
use Plokko\PhpFcmV1\Message\ApnsConfig;
use Plokko\PhpFcmV1\Message\Data;
use Plokko\PhpFcmV1\Message\Notification;
use Plokko\PhpFcmV1\Message\WebpushConfig;
use Plokko\PhpFcmV1\Targets\Target;
use UnexpectedValueException;

/**
 * Class Message
 * @package Plokko\PhpFcmV1
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages
 *
 * @property Data $data Arbitrary key/value payload.
 * @property Notification $notification Basic notification template to use across all platforms.
 * @property AndroidConfig $android Android specific options for messages sent through FCM connection server.
 * @property WebpushConfig $webpush Webpush protocol options.
 * @property ApnsConfig $apns Apple Push Notification Service specific options.
 *
 */
class Message implements JsonSerializable {
    private
        /**@var string**/
        $name,
        /**@var \Plokko\PhpFcmV1\Message\Data **/
        $data,
        /**@var \Plokko\PhpFcmV1\Message\Notification **/
        $notification,
        /**@var \Plokko\PhpFcmV1\Message\AndroidConfig **/
        $android,
        /**@var \Plokko\PhpFcmV1\Message\WebpushConfig **/
        $webpush,
        /**@var \Plokko\PhpFcmV1\Message\ApnsConfig **/
        $apns,

        /**@var \Plokko\PhpFcmV1\Targets\Target **/
        $target;

    function __construct()
    {
    }

    function __get($name)
    {
        if(!$this->{$name}){
            //Lazy creation
            switch($name){
                case 'data':
                    $this->data = new Data();
                    break;
                case 'notification':
                    $this->notification = new Notification();
                    break;
                case 'android':
                    $this->android = new AndroidConfig();
                    break;
                case 'webpush':
                    $this->webpush = new WebpushConfig();
                    break;
                case 'apns':
                    $this->apns = new ApnsConfig();
                    break;
                default:
            }
        }
        return $this->{$name};
    }


    function setTarget(Target $target){
        $this->target = $target;
    }


    function getPayload(){
        if(!$this->target){
            throw new UnexpectedValueException('FCMMEssage target not specified!','TARGET_NOT_SPECIFIED');
        }

        $data = array_filter([
            'data'          => $this->data,
            'notification'  => $this->notification,
            'android'       => $this->android,
            'webpush'       => $this->webpush,
            'apns'          => $this->apns,
        ]);
        return array_merge($data,$this->target->jsonSerialize());
    }

    /**
     * Submit the message
     * @param Request $request
     * @throws FcmError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(Request $request){
        $name = $request->submit($this);
        if(!$request->validate_only)
            $this->name = $name;
    }

    /**
     * Validate the message with Firebase without submitting it
     * @param Request $request
     * @return bool
     * @throw FcmError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validate(Request $request){
        $request = clone $request;
        $request->validate_only=true;

        $request->submit($this);

        return true;
    }

    public function jsonSerialize()
    {
        $data = array_filter([
            'name'          => $this->name,
            'data'          => $this->data,
            'notification'  => $this->notification,
            'android'       => $this->android,
            'webpush'       => $this->webpush,
            'apns'          => $this->apns,
        ]);

        return $this->target?array_merge($data,$this->target->jsonSerialize()):$data;
    }

    function __toString()
    {
        return json_encode($this);
    }

    function isSubmitted(){return !!$this->name;}
}