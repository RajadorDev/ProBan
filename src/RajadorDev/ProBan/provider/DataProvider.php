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

use pocketmine\promise\Promise;
use RajadorDev\ProBan\data\PlayerBannedData;

interface DataProvider 
{

    /** 
     * @param PlayerBannedData
     * @return Promise<bool>
     */
    public function save(PlayerBannedData $data) : Promise;

    /**
     * @param string $username
     * @return Promise<PlayerBannedData | null>
     */
    public function fetchBannedByUsername(string $username) : Promise;

    /**
     * @return Promise<PlayerBannedData[]>
     */
    public function getAll() : Promise;

    /**
     * @param PlayerBannedData
     * @return void 
     */
    public function delete(PlayerBannedData $data) : void;

}