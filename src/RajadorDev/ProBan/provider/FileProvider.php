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

namespace RajadorDev\ProBan\provider;

use JsonSerializable;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\utils\Config;
use RajadorDev\ProBan\data\PlayerBannedData;

class FileProvider implements DataProvider, JsonSerializable
{

    /** @var array<string, PlayerBannedData> */
    protected $bans = [];

    /**
     * @param PlayerBannedData[] $bans
     */
    public function __construct(protected Config $file, array $bans)
    {
        foreach ($bans as $banData)
        {
            $this->bans[$banData->getId()] = $banData;
        }
    }

    public static function promise(mixed $result) : Promise 
    {
        $promise = new PromiseResolver;
        $promise->resolve($result);
        return $promise->getPromise();
    }

    public function save(PlayerBannedData $data): Promise
    {
        $this->bans[$data->getId()] = $data;
        $this->saveFile();
        return self::promise(true);
    }

    public function fetchBannedByUsername(string $username): Promise
    {
        $username = strtolower($username);
        $found = null;
        foreach ($this->bans as $banData)
        {
            if (strtolower($username) == $username)
            {
                $found = $banData;
                break;
            }
        }
        return self::promise($found);
    }

    public function getAll(): Promise
    {
        return self::promise($this->bans);
    }

    public function delete(PlayerBannedData $data): void
    {
        unset($this->bans[$data->getId()]);
        $this->saveFile();
    }

    protected function saveFile() : void 
    {
        $list = $this->jsonSerialize();
        $this->file->setAll($list);
        $this->file->save();
    }

    public function jsonSerialize(): mixed
    {
        return array_map(
            fn (PlayerBannedData $data) : array => $data->jsonSerialize(),
            array_values($this->bans)
        );
    }

    /**
     * @param array[] $bans
     * @return PlayerBannedData[]
     */
    public static function unserializeList(array $bans) : array 
    {
        return array_map(
            fn (array $data) : PlayerBannedData => PlayerBannedData::unserialize($data), 
            $bans
        );
    }

}
