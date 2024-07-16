<?php

namespace RajadorBanSystem;

/*
  
  Rajador Developer

  ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
  ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
  ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█

  GitHub: https://github.com/RajadorDev

  Discord: Rajador#7070


*/

use pocketmine\event\player\PlayerPreLoginEvent;

use RajadorBanSystem\Events\{PlayerBannedEvent, PlayerExpelledEvent, AccountBannedEvent};

class Events implements \pocketmine\event\Listener 
{
	
	public function __construct(ProBan $pl)
	{
		$this->system = $pl;
	}
	
	public function onBan(PlayerBannedEvent $e)
	{
		if((bool) $this->system->getConfigValue('ban-webhook'))
		{
			$this->system->getDiscordSystem()->sendWebHook(ProBan::PLAYER_BANNED_EVENT, $e);
		}
	}
	
	public function onAccountBanned(AccountBannedEvent $e)
	{
		if((bool) $this->system->getConfigValue('ban-webhook'))
		{
			$this->system->getDiscordSystem()->sendWebHook(ProBan::PLAYER_BANNED_EVENT, $e);
		}
	}
	
	public function onKick(PlayerExpelledEvent $e)
	{
		if((bool) $this->system->getConfigValue('kick-webhook'))
		{
			$this->system->getDiscordSystem()->sendWebHook(ProBan::PLAYER_EXPELL_EVENT, $e);
		}
	}
	
	public function onPreLogin(PlayerPreLoginEvent $e)
	{
		$p = $e->getPlayer();
		if(!$e->isCancelled() && $this->system->isPlayerBanned($p))
		{
			$e->setCancelled(true);
			$banData = $this->system->getBanData($p);
			$vars = [array('{name}', '{adm}', '{reason}', '{line}'), array($p->getName(), $banData['adm'], $banData['reason'], PHP_EOL)];
			$e->setKickMessage(str_ireplace($vars[0], $vars[1], ProBan::getMessage('ban-screen')));
			unset($banData);
			unset($vars);
		}
		unset($p);
	}
	
}
