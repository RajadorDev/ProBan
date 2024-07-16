<?php

namespace RajadorBanSystem\Events;


/*
  
  Rajador Developer

  ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
  ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
  ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█

  GitHub: https://github.com/RajadorDev

  Discord: Rajador#7070


*/

use RajadorBanSystem\ProBan;

class AccountBannedEvent extends \pocketmine\event\plugin\PluginEvent 
{
	
	public static $handlerList = null;
	
	public function __construct(String $targetAccount, $adm, $reason = 'No reason')
	{
		$this->targetAccount = $targetAccount;
		$this->adm = $adm;
		$this->reason = $reason;
	}
	
	public function getTarget() : String 
	{
		return $this->targetAccount;
	}
	
	public function getAdm() 
	{
		return $this->adm;
	}
	
	public function getEventId() : int 
	{
		return ProBan::PLAYER_BANNED_EVENT;
	}
	
	public function getReason() 
	{
		return $this->reason;
	}
	
	
	
}
