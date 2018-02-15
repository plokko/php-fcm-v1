<?php
namespace Plokko\PhpFCM;

use JsonSerializable;
use Plokko\phpFCM\Message\AndroidConfig;
use Plokko\phpFCM\Message\ApnsConfig;
use Plokko\phpFCM\Message\Data;
use Plokko\phpFCM\Message\Notification;
use Plokko\phpFCM\Message\WebpushConfig;
use Plokko\PhpFCM\Targets\Target;

/**
 * Class Message
 * @package Plokko\PhpFCM
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages
 *
 * @property string $name Output Only. The identifier of the message sent, in the format of projects/*\/messages/{message_id}.
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
        /**@var \Plokko\phpFCM\Message\Data **/
        $data,
        /**@var \Plokko\phpFCM\Message\Notification **/
        $notification,
        /**@var \Plokko\phpFCM\Message\AndroidConfig **/
        $android,
        /**@var \Plokko\phpFCM\Message\WebpushConfig **/
        $webpush,
        /**@var \Plokko\phpFCM\Message\ApnsConfig **/
        $apns,

        $targetType=null,
        $target=null;

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


    public function jsonSerialize()
    {

        $data = array_filter([
            'name'          => $this->name,
            'data'          => $this->data,
            'notification'  => $this->notification,
            'android'       => $this->android,
            'webpush'       => $this->webpush,
            'apns'          => $this->apns,
            $this->targetType => $this->target,
        ]);

        return $data;
    }




}