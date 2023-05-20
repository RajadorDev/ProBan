<?php

namespace RajadorBanSystem\Commands;

/*
  
  Rajador Developer

  ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
  ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
  ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█

  GitHub: https://github.com/RajadorDev

  Discord: Rajador#7070


*/

use pocketmine\command\{Command, CommandSender};

use RajadorBanSystem\ProBan;


class BanIpCommand extends Command 
{
	
	public function __construct(ProBan $pl, array $config)
	{
		$this->plugin = $pl;
		if($config['usage'] == null || trim($config['usage']) == '')
		{
			$config['usage'] = 'use: /banip <player/ip> <reason>';
		}
		parent::__construct(
			'banip',
			'Rajador ban-ip system :)',
			$config['usage'],
			$config['aliases']
		);
		$this->setPermission($config['permission']);
	}
	
	public function execute(CommandSender $p, $label, array $args)
	{
		if(!$this->testPermission($p))
		  return false;
		
		if(!isset($args[0]))
		  return $this->sendUsage($p, $label);
		  
		$adr = $args[0];
		$reason = null;
		if(isset($args[1]))
		{
			$reason = $args;
			unset($reason[0]);
			$reason = implode(' ', $reason);
		}
		if($this->plugin->banAddress($adr, $p, $reason))
		{
			$p->sendMessage(str_replace('{target}', $adr, ProBan::getMessage('ban-sucess')));
		}else{
			$p->sendMessage(ProBan::getMessage('adr-notfound'));
		}
		
	}
	
	public function sendUsage(CommandSender $p, String $label)
	{
		$p->sendMessage(str_replace('{cmd}', $label, $this->getUsage()));
	}
	
}