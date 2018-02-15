<?php
namespace Plokko\phpFCM\Message;

use JsonSerializable;

/**
 * Class WebPushNotification
 * @package Plokko\phpFCM\Message
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#WebpushNotification
 */
class WebPushNotification implements JsonSerializable
{
    public
        /**@var string**/
        $title,
        /**@var string**/
        $body,
        /**@var string**/
        $icon;

    public function jsonSerialize()
    {
        return array_filter([
            'title' => $this->title,
            'body'  => $this->body,
            'icon'  => $this->icon,
        ]);
    }
}