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

use pocketmine\player\Player;
use pocketmine\promise\Promise;
use RajadorDev\ProBan\data\PlayerBannedData;

interface DataProvider 
{

    /** 
     * @param PlayerBannedData $data
     * @return Promise<bool>
     */
    public function save(PlayerBannedData $data) : Promise;

    /**
     * @return Promise<PlayerBannedData[]>
     */
    public function getAll() : Promise;

    /**
     * @param PlayerBannedData
     * @return void 
     */
    public function delete(PlayerBannedData $data) : void;

    /**
     * @param string | Player $input
     * @return bool
     */
    public function isBanned(string | Player $input) : bool;

    /**
     * @param string | Player $input
     * @return PlayerBannedData
     */
    public function getBannedData(string|Player $input) : ? PlayerBannedData;


}