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

namespace RajadorDev\ProBan\discord;

use pocketmine\Server;
use RajadorDev\ProBan\ProBanPlugin;
use RajadorDev\ProBan\task\SendWebhookTask;

final class WebHookManager 
{

    const KICK = 'kick';

    const BAN = 'ban';

    private string $filePath;

    protected ? string $webHookUrl = null;

    public function __construct(private ProBanPlugin $plugin, string $path)
    {
        $this->filePath = $path . 'webhook.txt';
        $this->reloadWebhookFromFile();
    }

    private function reloadWebhookFromFile() : bool  
    {
        if (file_exists($this->filePath))
        {
            $webHook = file_get_contents($this->filePath);
            $webHookInfo = parse_url($webHook);
            if (is_array($webHookInfo) && isset($webHookInfo['host']) && $webHookInfo['host'] == 'discord.com')
            {
                $this->setWebhook($webHook, false);
                return true;
            }
        }
        $this->webHookUrl = null;
        return false;
    }

    public static function replaceAll(array $replace, array $to, array &$list) : void 
    {
        foreach ($list as &$dataList)
        {
            if (is_string($dataList))
            {
                $dataList = str_replace($replace, $to, $dataList);
            } else {
                self::replaceAll($replace, $to, $dataList);
            }
        }
    }

    public function setWebhook(? string $webHook, bool $save = true) : void 
    {
        $this->webHookUrl = $webHook;
        if ($save)
        {
            if (is_string($webHook))
            {
                file_put_contents($this->filePath, $webHook);
            } else if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        }
    }

    public function getWebhook() : ? string 
    {
        return $this->webHookUrl;
    }

    public function hasWebhook() : bool 
    {
        return is_string($this->webHookUrl);
    }

    protected function getWebhookFormat(string $type) : array 
    {
        return $this->plugin->getConfigValue($type . '-webhook', []);
    }

    public function sendWebhookByType(string $type, string $username, string $author, string $reason) : bool 
    {
        if ($this->hasWebhook())
        {
            $webhook = $this->getWebhookFormat($type);
            self::replaceAll([
                '{name}',
                '{by}',
                '{reason}'
            ], [
                $username,
                $author,
                $reason
            ], $webhook);
            $this->sendWebhook($webhook);
            return true;
        }
        return false;
    }

    private function sendWebhook(array $webhook) : void 
    {
        $url = $this->getWebhook();
        Server::getInstance()->getAsyncPool()->submitTask(new SendWebhookTask($url, $webhook));
    }


}