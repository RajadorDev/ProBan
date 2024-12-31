
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

use pocketmine\player\Player;
use RajadorDev\ProBan\ProBanPlugin;

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

    public function setWebhook(string $webHook, bool $save = true) : void 
    {
        $this->webHookUrl = $webHook;
        if ($save)
        {
            file_put_contents($this->filePath, $webHook);
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

    public function sendKickWebhook(string $username, string $author, string $reason) : bool 
    {
        return false;
    }


}