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

use pocketmine\Player;

abstract class PunishEvent extends \pocketmine\event\plugin\PluginEvent
{
	
	protected $target, $adm, $reason;
	
	public function __construct(Player $target, $adm, $reason = 'No Reason')
	{
		$this->target = $target;
		$this->adm = $adm;
		$this->reason = $reason;
	}
	
	/* @return Player */
	public function getTarget() : Player 
	{
		return $this->target;
	}
	
	/* @return Player | ConsoleCommandSender */
	public function getAdm() 
	{
		return $this->adm;
	}
	
	/* @return String | null */
	public function getReason() 
	{
		return $this->reason;
	}
	
	/* @return int */
	abstract public function getEventId() : int;
	
	
}

