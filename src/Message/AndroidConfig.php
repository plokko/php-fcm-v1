<?php
namespace Plokko\phpFCM\Message;

use JsonSerializable;

/**
 * Class AndroidConfig
 * @package Plokko\phpFCM\Message
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#AndroidConfig
 */
class AndroidConfig implements JsonSerializable
{
    const
        PRIORITY_NORMAL='NORMAL',
        PRIORITY_HIGH='HIGH';


    private
        $collapse_key,
        $priority = self::PRIORITY_NORMAL,
        $ttl,
        $restricted_package_name,
        $data,
        $notification;

    function setPriorityHigh(){
        $this->priority = self::PRIORITY_HIGH;
        return $this;
    }

    function setPriorityNormal(){
        $this->priority = self::PRIORITY_NORMAL;
        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter([
            'collapse_key'  => $this->collapse_key,
            'priority'      => $this->priority,
            'ttl'           => $this->ttl,
            'restricted_package_name' => $this->restricted_package_name,
            'data'          => $this->data,
            'notification'  => $this->notification,
        ]);
    }
}