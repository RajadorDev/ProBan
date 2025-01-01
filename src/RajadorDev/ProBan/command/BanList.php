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

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RajadorDev\ProBan\data\PlayerBannedData;

class BanList extends ProBanCommand 
{

    protected function run(CommandSender $sender, string $label, array $args): void
    {
        $getSender = static function () use ($sender) : ? CommandSender {
            if ($sender instanceof CommandSender)
            {
                if (!($sender instanceof Player) || $sender->isOnline())
                {
                    return $sender;
                }
            }
            return null;
        };
        $this->plugin->getProvider()->getAll()->onCompletion(
            function (array $list) use ($getSender) : void {
                if ($sender = $getSender())
                {
                    if (count($list) > 0)
                    {
                        $list = implode(
                            ', ',
                            array_map(
                                fn (PlayerBannedData $data) : string => $data->getUsername(),
                                $list
                            )
                        );
                        $message = $this->plugin->getMessage('banlist', '{list}', $list);
                    } else {
                        $message = $this->plugin->getMessage('banlist.empty');
                    }
                    $sender->sendMessage($message);
                }
            },
            function () use ($getSender) : void {
                if ($sender = $getSender())
                {
                    $sender->sendMessage($this->plugin->getPrefix() . 'An error occurred');
                }
            }
        );
    }

    protected function getFirstArgsNeedle(): ?int
    {
        return null;    
    }

}