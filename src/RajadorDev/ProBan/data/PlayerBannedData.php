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

namespace RajadorDev\ProBan\data;

use RajadorDev\ProBan\utils\SerializableObjectData;

class PlayerBannedData extends SerializableObjectData 
{

    const DATA_UUID = 'player_uniqueid_base64';

    const DATA_USERNAME = 'player_username';

    const DATA_BANNED_REASON = 'banned_reason';

    const DATA_BANNED_BY = 'bannedby';

    const DATA_BANNED_TIME = 'bannedat';

    protected int $bannedAt;

    public function __construct(private string $uuid, protected string $username, protected string $reason, protected string $bannedBy, ? string $bannedAt = null)
    {
        $this->bannedAt = $bannedAt ?? time();
    }

    final public function getId() : string 
    {
        return $this->uuid;
    }

    public function getUsername() : string 
    {
        return $this->username;
    }

    public function getBannedTime() : int 
    {
        return $this->bannedAt;
    }

    public function getReason() : string 
    {
        return $this->reason;
    }

    public function getBannedBy() : string 
    {
        return $this->bannedBy;
    }

    protected function serializeObjectdata(): array
    {
        return [
            self::DATA_UUID => $this->getId(),
            self::DATA_USERNAME => $this->getUsername(),
            self::DATA_BANNED_REASON => $this->getReason(),
            self::DATA_BANNED_BY => $this->getBannedBy(),
            self::DATA_BANNED_TIME => $this->getBannedTime()
        ];
    }

    public static function unserialize(array $data): SerializableObjectData
    {
        return new PlayerBannedData(
            $data[self::DATA_UUID],
            $data[self::DATA_USERNAME],
            $data[self::DATA_BANNED_REASON],
            $data[self::DATA_BANNED_BY],
            $data[self::DATA_BANNED_TIME]
        );
    }

}