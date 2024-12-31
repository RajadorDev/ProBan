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

namespace RajadorDev\ProBan\task;

use pocketmine\scheduler\AsyncTask;

use pmmp\thread\ThreadSafeArray;
use RajadorDev\ProBan\ProBanPlugin;
use Throwable;

class SendWebhookTask extends AsyncTask 
{

    protected string $url;

    protected ThreadSafeArray $webhook;

    public function __construct(string $url, array $webHook)
    {
        $this->url = $url;
        $this->webhook = ThreadSafeArray::fromArray($webHook);
    }

    public function onRun(): void
    {
        try {
            $url = $this->url;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->webhook);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_exec($curl);
        } catch (Throwable $e) {
            $this->setResult($e->getMessage());
        }
    }

    public function onCompletion(): void
    {
        if (is_string($result = $this->getResult()))
        {
            ProBanPlugin::getInstance()->getLogger()->error($result);
        }
    }
    
}