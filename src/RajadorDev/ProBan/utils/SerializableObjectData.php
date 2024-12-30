<?php

declare (strict_types=1);

/***
 *   
 * Rajador Developer
 * 
 * ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
 * ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
 * ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█
 * 
 * GitHub: https://github.com/RajadorDev
 * 
 * Discord: rajadortv
 * 
 * 
**/

namespace RajadorDev\ProBan\utils;

use JsonSerializable;

abstract class SerializableObjectData implements JsonSerializable 
{

    const DATA_OBJECT = 'object_id';

    abstract protected function serializeObjectdata() : array;

    abstract public static function unserialize(array $data) : SerializableObjectData;

    public function jsonSerialize() : mixed 
    {
        $data = [
            self::DATA_OBJECT => get_class($this)
        ];
        return array_merge(
            $data, $this->serializeObjectdata()
        );
    }

}