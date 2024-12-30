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
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

final class ProBanPlugin extends PluginBase 
{

    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveResource('config.yml');
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
        $serverMessage = $this->getMessage('player.kick', ['{name}', '{by}', '{reason}'], [$player->getName(), $author->getName(), $reason]);
        Server::getInstance()->broadcastMessage($serverMessage);
        $screenMessage = $this->getMessage('kick.screen', ['{by}', '{reason}'], [$author->getName(), $reason]);
        $player->kick(disconnectScreenMessage: $screenMessage);
    }
    
}