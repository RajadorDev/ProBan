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

namespace RajadorDev\ProBan;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\promise\Promise;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use RajadorDev\ProBan\command\BanCommand;
use RajadorDev\ProBan\command\KickCommand;
use RajadorDev\ProBan\data\PlayerBannedData;
use RajadorDev\ProBan\provider\DataProvider;
use RajadorDev\ProBan\provider\FileProvider;

final class ProBanPlugin extends PluginBase 
{

    use SingletonTrait;

    private DataProvider $provider;

    private Config $uuidsFile;

    /** @var array<string, string> */
    private array $uuids = [];

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveResource('config.yml');
        $this->initProvider();
        $this->initCommands();
        $this->initUUIDs();
        $this->getServer()->getPluginManager()->registerEvents(new ProBanListener($this), $this);
    }

    protected function onDisable(): void
    {
        $this->saveUUIDs();
    }

    private function initProvider() : void 
    {
        $dir = $this->getDataFolder();
        $file = new Config($dir . 'bans.json', Config::JSON);
        $this->provider = new FileProvider($file, FileProvider::unserializeList($file->getAll()));
    }

    private function initCommands() : void 
    {
        new KickCommand('kick', 'Kick players', 'proban.kick', $this->getMessage('kick.usage'));
        new BanCommand('ban', 'Ban players', 'proban.ban', $this->getMessage('ban.usage'));
    }

    private function initUUIDs() : void 
    {
        $this->uuidsFile = new Config($this->getDataFolder() . 'uuids.json', Config::JSON);
        /** @var string $uuidBytes64 */
        foreach ($this->uuidsFile->getAll() as $uuidBytes64 => $username)
        {
            $this->uuids[base64_decode($uuidBytes64)] = $username;
        }
    }

    private function saveUUIDs() : void 
    {
        $list = [];
        foreach ($this->uuids as $bytes => $username)
        {
            $list[base64_encode($bytes)] = $username;
        }
        $this->uuidsFile->setAll($list);
        $this->uuidsFile->save();
    }

    public function getProvider() : DataProvider
    {
        return $this->provider;
    }

    public function getConfigValue(string $id, mixed $default = null, bool $warnConsoleIfNotExists = true) : mixed
    {
        if ($this->getConfig()->exists($id))
        {
            return $this->getConfig()->get($id);
        } else if ($warnConsoleIfNotExists) {
            $this->getLogger()->warning("Config with id $id not found!");
        }
        return $default;
    }

    public function getDefaultReason() : string 
    {
        return $this->getConfigValue('default.reason', '');
    }

    public function getPrefix() : string 
    {
        return $this->getMessage('prefix', default: '§l§bPRO§f§eBAN§r§7  ');
    }

    public function getMessage(string $messageId, string | array $replace = null, string | array $to = null, string $default = '{prefix}Message not found!', bool $warnConsoleIfNotExists = true, bool $addPrefix = true) : string 
    {
        $messages = $this->getConfig()->get('messages', []);

        if (isset($messages[$messageId]))
        {
            $message = $messages[$messageId];
        } else {
            $message = $default;
            if ($warnConsoleIfNotExists)
            {
                $this->getLogger()->warning("Message with id $messageId not found!");
            }
        }

        if (!is_null($replace) && !is_null($to))
        {
            $message = str_replace($replace, $to, $message);
        }

        if ($addPrefix)
        {
            $message = str_replace('{prefix}', $this->getPrefix(), $message);
        }
        return $message;
    }

    public function kick(Player $player, CommandSender $author, string $reason) : void 
    {
        $serverMessage = $this->getMessage('player.kicked', ['{name}', '{by}', '{reason}'], [$player->getName(), $author->getName(), $reason]);
        Server::getInstance()->broadcastMessage($serverMessage);
        $screenMessage = $this->getMessage('kick.screen', ['{by}', '{reason}'], [$author->getName(), $reason]);
        $player->kick(disconnectScreenMessage: $screenMessage);
    }

    public function ban(string $uuid, string $username, CommandSender $author, string $reason) : Promise 
    {
        $promisse = $this->provider->save(new PlayerBannedData($uuid, $username, $reason, $author->getName()));
        $authorUsername = $author->getName();
        $promisse->onCompletion(
            function (bool $result) use ($uuid, $authorUsername, $username, $reason) : void {
                if ($result)
                {
                    $serverMessage = $this->getMessage('player.banned', ['{name}', '{by}', '{reason}'], [$username, $authorUsername, $reason]);
                    Server::getInstance()->broadcastMessage($serverMessage);
                    if ($target = Server::getInstance()->getPlayerByRawUUID($uuid))
                    {
                        $screenMessage = $this->getMessage('ban.screen', ['{by}', '{reason}'], [$authorUsername, $reason]);
                        $target->kick(disconnectScreenMessage: $screenMessage);
                    }
                }
            },
            function () : void {
                $this->getLogger()->alert('Falied to save a PlayerBannedData');
            }
        );
        
        return $promisse;
    }

    /** @return array<string, string> */
    public function getAllUUIDS() : array 
    {
        return $this->uuids;
    }

    public function setUUID(string $uuid, string $username)
    {
        $this->uuids[$uuid] = $username;
    }

    public function getPlayerUUIDByUsername(string &$username, bool $exact = true) : ? string 
    {
        $found = null;
        $searchUsername = strtolower($username);
        foreach ($this->uuids as $bytes => $name)
        {
            $name = strtolower($name);
            if ($searchUsername === $name)
            {
                $found = $bytes;
                break;
            } else if (!$exact && str_contains($name, $searchUsername)) {
                $found = $bytes;
            }
        }

        if ($found)
        {
            $username = $this->uuids[$found];
        }
        return $found;
    }
    
}