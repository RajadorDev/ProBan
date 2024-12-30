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
use RajadorDev\ProBan\ProBanPlugin;

class BanCommand extends FormCommand 
{

    protected function run(CommandSender $sender, string $label, array $args): void
    {
        $username = $args[0];
        $reason = self::collectReason($args, 1);
        $targetUUID = ProBanPlugin::getInstance()->getPlayerUUIDByUsername($username);
        if ($targetUUID)
        {
            $this->plugin->ban($targetUUID, $username, $sender, $reason);
        }
    }

    protected function sendForm(Player $player): void
    {
        $list = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player)
        {
            $list[$player->getUniqueId()->getBytes()] = $player->getName();
        }
        $visualList = $list;
        sort($visualList);
        $form = new CustomForm(
            'Ban',
            [
                new Dropdown('player', 'Select the Player', $visualList),
                new Input('reason', 'Reason')
            ], function (Player $player, CustomFormResponse $response) use ($list, $visualList) : void {
                $playerIndex = $response->getInt('player');
                $playerUsername = $visualList[$playerIndex];
                $playerUUID = array_search($playerUsername, $list);
                $reason = $response->getString('reason');
                if (trim($reason) == '')
                {
                    $reason = ProBanPlugin::getInstance()->getDefaultReason();
                }
                $this->plugin->ban($playerUUID, $playerUsername, $player, $reason);
            }
        );
        $player->sendForm($form);
    }

    protected function getFirstArgsNeedle(): ?int
    {
        return 0;
    }

}