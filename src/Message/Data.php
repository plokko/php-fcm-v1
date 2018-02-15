<?php
namespace Plokko\phpFCM\Message;


use ArrayAccess;
use JsonSerializable;

class Data implements ArrayAccess,JsonSerializable
{
    private $data=[];

    function __construct($data=[]){
        $this->data = $data;
    }

    function get($k){
        return $this->data[$k];
    }

    function set($k,$v){
        $this->data[$k]=$v;
        return $this;
    }

    function fill(array $data){
        $this->data=$data;
    }
    function clear(){
        $this->data=[];
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }


    public function jsonSerialize()
    {
        return $this->data;
    }
}