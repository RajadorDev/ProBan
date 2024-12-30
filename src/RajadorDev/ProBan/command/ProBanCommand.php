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

namespace RajadorDev\ProBan\command;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use RajadorDev\ProBan\ProBanPlugin;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

abstract class ProBanCommand extends Command implements PluginOwned
{

    /** @var ProBanPlugin */
    protected ProBanPlugin $plugin;

    public function __construct(string $commandName, string $commandDescription, string $permission, ? string $usage = null, array $aliases = [])
    {
        parent::__construct($commandName, $commandDescription, $usage, $aliases);
        $this->setPermission($permission);
        $this->plugin = ProBanPlugin::getInstance();
        Server::getInstance()->getCommandMap()->register('proban', $this);
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($this->testPermission($sender))
        {
            if (is_int($argsNeedle = $this->getFirstArgsNeedle()))
            {
                if (self::parseArg($argsNeedle, $args))
                {
                    $this->run($sender, $commandLabel, $args);
                } else {
                    $this->usage($sender, $commandLabel);
                }
            } else {
                $this->run($sender, $commandLabel, $args);
            }
        }
    }

    abstract protected function run(CommandSender $sender, string $label, array $args) : void;

    abstract protected function getFirstArgsNeedle() : ? int;


    protected function usage(CommandSender $sender, string $label) : void 
    {
        $sender->sendMessage(str_replace('{label}', $label, $this->getUsage()));
    }

    public static function parseArg(int $index, array $args) : bool 
    {
        for ($i = 0; $i <= $index; $i++)
        {
            if (!isset($args[$i]) || trim($args[$i]) == '')
            {
                return false;
            }
        }
        return true;
    }

    public static function searchPlayer(string &$input, CommandSender $commandSender = null) : ? Player
    {
        $searchUsername = strtolower($input);
        $found = null;
        foreach (Server::getInstance()->getOnlinePlayers() as $player)
        {
            $playerName = strtolower($player->getName());
            if ($playerName === $searchUsername)
            {
                $found = $player;
                break;
            } else if (str_contains($playerName, $searchUsername)) {
                $found = $player;
            }
        }
        if ($found)
        {
            $input = $found->getName();
        } else if ($commandSender) {
            $commandSender->sendMessage(ProBanPlugin::getInstance()->getMessage('player.notfound', '{name}', $input));
        }
        return $found;
    }

    public static function collectReason(array $args, int $startIndex) : string 
    {
        if (self::parseArg($startIndex, $args))
        {
            return implode(' ', array_slice($args, $startIndex));
        } else {
            return ProBanPlugin::getInstance()->getDefaultReason();
        }
    }


}