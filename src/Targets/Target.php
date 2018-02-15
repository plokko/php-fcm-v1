<?php
namespace Plokko\PhpFCM\Targets;
use JsonSerializable;

interface Target extends JsonSerializable {
    public function jsonSerialize();

}