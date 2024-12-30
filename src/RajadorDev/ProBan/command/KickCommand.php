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
use pocketmine\Server;
use RajadorDev\ProBan\libs\dktapps\pmforms\CustomForm;
use RajadorDev\ProBan\libs\dktapps\pmforms\CustomFormResponse;
use RajadorDev\ProBan\libs\dktapps\pmforms\element\Dropdown;
use RajadorDev\ProBan\libs\dktapps\pmforms\element\Input;

class KickCommand extends FormCommand 
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

    protected function sendForm(Player $player): void
    {
        $playersList = Server::getInstance()->getOnlinePlayers();
        $playersList = array_values($playersList);
        $playersList = array_map(
            fn (Player $player) : string => $player->getName(), $playersList
        );
        $form = new CustomForm(
            'Kick',
            [
                new Dropdown('player', 'Select the player', $playersList),
                new Input('reason', 'Reason', $this->plugin->getDefaultReason())
            ],
            function (Player $player, CustomFormResponse $response) use ($playersList) : void 
            {
                $option = $response->getInt('player');
                $playerName = $playersList[$option];
                $target = Server::getInstance()->getPlayerExact($playerName);
                if ($target instanceof Player && $target->isOnline())
                {
                    $reason = $response->getString('reason');
                    if (trim($reason) == '')
                    {
                        $reason = $this->plugin->getDefaultReason();
                    }
                    $this->plugin->kick($target, $player, $reason);
                } else {
                    $player->sendMessage($this->plugin->getMessage('player.notfound', '{name}', $playerName));
                }
            }
        );
        $player->sendForm($form);
    }

    protected function getFirstArgsNeedle(): ?int
    {
        return 0;
    }

}