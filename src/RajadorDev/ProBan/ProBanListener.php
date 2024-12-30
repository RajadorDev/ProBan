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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;

final class ProBanListener implements Listener 
{

    public function __construct(protected ProBanPlugin $plugin)
    {}

    /**
     * @priority LOWEST
     */
    public function saveUUID(PlayerJoinEvent $event) : void 
    {
        $player = $event->getPlayer();
        $this->plugin->setUUID($player->getUniqueId()->getBytes(), $player->getName());
    }

    /**
     * @priority LOWEST
     */
    public function login(PlayerLoginEvent $event) : void 
    {
        if (!$event->isCancelled())
        {
            if ($banData = $this->plugin->getProvider()->getBannedData($event->getPlayer()))
            {
                $event->cancel();
                $screenMessage = $this->plugin->getMessage('ban.screen', ['{by}', '{reason}'], [$banData->getBannedBy(), $banData->getReason()]);
                $event->setKickMessage($screenMessage);
            }
        }
    }

}