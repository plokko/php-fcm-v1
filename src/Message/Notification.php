<?php
namespace Plokko\phpFCM\Message;

use JsonSerializable;

/**
 * Class Notification
 * @package Plokko\phpFCM\Message
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#Notification
 */
class Notification implements JsonSerializable
{
    public
        /**@var string The notification's title.*/
        $title,
        /**@var string The notification's body text.*/
        $body;


    function setTitle($title){
        $this->title = $title;
        return $this;
    }

    function setBody($body){
        $this->body = $body;
        return $this;
    }


    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
        ];
    }
}