<?php
namespace Plokko\phpFCM\Targets;


class TokenTarget implements Target
{
    const TARGET_NAME = 'token';

    private
        /**@var string **/
        $token;

    /**
     * TokenTarget constructor.
     * @param $token string
     */
    function __construct($token)
    {
        $this->token = $token;
    }

    public function jsonSerialize()
    {
        return [
            self::TARGET_NAME => $this->token,
        ];
    }
}