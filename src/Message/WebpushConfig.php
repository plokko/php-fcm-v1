<?php
namespace Plokko\phpFCM\Message;

use JsonSerializable;

/**
 * Class WebpushConfig
 * @package Plokko\phpFCM\Message
 * @see
 */
class WebpushConfig implements JsonSerializable
{
    private
        /**@var array**/
        $headers,
        /**@var array**/
        $data,
        /**@var WebPushNotification**/
        $notification;

    public function jsonSerialize()
    {
        return array_filter([
           'headers'        => $this->headers,
           'data'           => $this->data,
           'notification'   => $this->notification,
        ]);
    }
}