<?php
namespace Plokko\PhpFcmV1;

use Error;

/**
 * Class FcmError
 * @package Plokko\PhpFcmV1
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/FcmError
 */
class FcmError extends Error
{
    private static
        $error_code_enums = [
            'UNSPECIFIED_ERROR' => 'No more information is available about this error.',
            'INVALID_ARGUMENT'  => 'Request parameters were invalid. An extension of type google.rpc.BadRequest is returned to specify which field was invalid.',
            'UNREGISTERED'      => 'App instance was unregistered from FCM. This usually means that the token used is no longer valid and a new one must be used.',
            'SENDER_ID_MISMATCH'=> 'The authenticated sender ID is different from the sender ID for the registration token.',
            'QUOTA_EXCEEDED'    => 'Sending limit exceeded for the message target. An extension of type google.rpc.QuotaFailure is returned to specify which quota got exceeded.',
            'APNS_AUTH_ERROR'   => 'APNs certificate or auth key was invalid or missing.',
            'UNAVAILABLE'       => 'The server is overloaded.',
            'INTERNAL'          => 'An unknown internal error occurred.',
        ];
    private
        $error_code;

    function __construct($error_code,$code=0,\Throwable $t=null){
        parent::__construct($error_code,$code,$t);
        $this->error_code = $error_code;
    }


    public function getDescription()
    {
        return array_key_exists($this->error_code,self::$error_code_enums)?self::$error_code_enums[$this->error_code]:'';
    }

    public function __toString(){
        return 'FcmError['.$this->getMessage().']: '.$this->getDescription();
    }
}