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
use RajadorDev\ProBan\libs\dktapps\pmforms\CustomForm;
use RajadorDev\ProBan\libs\dktapps\pmforms\CustomFormResponse;
use RajadorDev\ProBan\libs\dktapps\pmforms\element\Dropdown;

class UnbanCommand extends FormCommand 
{

    protected function run(CommandSender $sender, string $label, array $args): void
    {
        $search = $args[0];
        if ($banData = $this->plugin->getProvider()->getBannedData($search))
        {
            $this->plugin->getProvider()->delete($banData);
            $sender->sendMessage($this->plugin->getMessage('delete.ban', '{name}', $banData->getUsername()));
        } else {
            $sender->sendMessage($this->plugin->getMessage('ban.notfound', '{name}', $search));
        }
    }

    protected function getFirstArgsNeedle(): ?int
    {
        return 0;
    }

    protected function sendForm(Player $player): void
    {
        $playerGetter = static function () use ($player) : ? Player {
            if ($player instanceof Player && $player->isOnline())
            {
                return $player;
            }
            return null;
        };
        $this->plugin->getProvider()->getAll()->onCompletion(
            function (array $list) use ($playerGetter) : void {
                if ($player = $playerGetter())
                {
                    if (count($list) > 0)
                    {
                        /** @var PlayerBannedData[] */
                        $list = array_values($list);
                        $bansNames = array_map(
                            fn (PlayerBannedData $data) : string => $data->getUsername(),
                            $list
                        );
                        $form = new CustomForm(
                            'Unban',
                            [
                                new Dropdown('ban', 'Ban', $bansNames)
                            ],
                            function (Player $player, CustomFormResponse $response) use ($list) : void {
                                $ban = $response->getInt('ban');
                                $ban = $list[$ban];
                                $this->plugin->getProvider()->delete($ban);
                                $player->sendMessage($this->plugin->getMessage('delete.ban', '{name}', $ban->getUsername()));
                            }
                        );
                        $player->sendForm($form);
                    } else {
                        $player->sendMessage($this->plugin->getMessage('banlist.empty'));
                    }
                }
            }, function () use ($playerGetter) : void {
                if ($player = $playerGetter())
                {
                    $player->sendMessage($this->plugin->getPrefix() . 'An error occurred');
                }
            }
        );
    }

}