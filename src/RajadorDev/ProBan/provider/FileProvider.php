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
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use RajadorDev\ProBan\data\PlayerBannedData;
use RajadorDev\ProBan\utils\SerializableObjectData;

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
        /** @var PlayerBannedData[] */
        $list = array_map(
            fn (array $data) : SerializableObjectData => PlayerBannedData::unserialize($data), 
            $bans
        );
        return $list;
    }

    public function isBanned(string|Player $input): bool
    {
        return $this->getBannedData($input) instanceof PlayerBannedData;
    }

    public function getBannedData(string|Player $input): ?PlayerBannedData
    {
        if (isset($this->bans[$uuid = $input instanceof Player ? $input->getUniqueId()->getBytes() : $input]))
        {
            return $this->bans[$uuid];
        } else if (is_string($input)) {
            $username = strtolower($input);
            foreach ($this->bans as $banData)
            {
                if ($username == strtolower($banData->getUsername()))
                {
                    return $banData;
                }
            }
        }
        return null;
    }

}
