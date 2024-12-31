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

class ProBanPluginCommand extends ProBanCommand
{

    protected function run(CommandSender $sender, string $label, array $args): void
    {
        $firstArgument = $args[0];
        switch (strtolower($firstArgument))
        {
            case 'webhook':
            case 'setwebhook':
            case 'sethook':
                $prefix = $this->plugin->getPrefix();
                if ($this->plugin->isWebHookEnabled())
                {
                    if (self::parseArg(1, $args))
                    {
                        $url = $args[0];
                        if ($this->plugin->getWebHookManager()->setWebhook($url))
                        {
                            $sender->sendMessage($prefix . "Discord WebHook url setted up to §f$url");
                        } else {
                            $sender->sendMessage("{$prefix}§cInvalid WebHook url!");
                        }
                    } else {
                        $sender->sendMessage($prefix . "Use: §f/$label $firstArgument <url: string>");
                    }
                } else {
                    $sender->sendMessage($prefix . 'WebHook is disabled!');
                }
            break;
            case 'deletehook':
            case 'deletewebhook':
                $prefix = $this->plugin->getPrefix();
                if ($this->plugin->isWebHookEnabled())
                {
                    $this->plugin->getWebHookManager()->setWebhook(null);
                    $sender->sendMessage($prefix . 'WebHook removed §a§lSuceffully§r§7!');
                } else {
                    $sender->sendMessage($prefix . 'WebHook is disabled!');
                }
            break;
            case 'reload':
                $prefix = $this->plugin->getPrefix();
                $this->plugin->reload();
                $sender->sendMessage($prefix . 'Plugin reloaded §l§aSuceffully§r§7!');
            break;
            default:
                $sender->sendMessage($this->plugin->getPrefix() . "Invalid Sub-command §f\"§c{$firstArgument}§f\" §7use: §f/$label help §7for list of sub-commands");
            break;
        }
    }

    protected function getFirstArgsNeedle(): ?int
    {
        return 0;
    }
    
}