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
use ProBan\command\ProBanCommand;

class KickCommand extends ProBanCommand 
{

    protected function run(CommandSender $sender, string $label, array $args): void
    {
        $username = $args[0];
        $reason = self::collectReason($args, 1);
        if ($target = self::searchPlayer($username, $sender))
        {
            $this->plugin->kick($target, $sender, $reason);
        }
    }

    protected function getFirstArgsNeedle(): ?int
    {
        return 0;
    }

}