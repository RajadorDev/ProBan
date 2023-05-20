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


class UnbanCommand extends Command 
{
	
	public function __construct(ProBan $pl, array $config)
	{
		$this->plugin = $pl;
		if($config['usage'] == null || trim($config['usage']) == '')
		{
			$config['usage'] = 'use: /unban <player> <reason>';
		}
		parent::__construct(
			'unban',
			'Rajador ProBan system',
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
		
		if($this->plugin->isAccountBanned($args[0]) || $this->plugin->isAddressBanned($args[0]))
		{
			if($this->plugin->unban($args[0]))
			  $p->sendMessage('§aYou unbaned§f '.$args[0].' §aSuceffully.');
			else 
			  $p->sendMessage('§cAn error occurred while unbanning.');
		}else{
			$p->sendMessage(str_replace('{target}', $args[0], ProBan::getMessage('player-notfound')));
		}
		
	}
	
	public function sendUsage(CommandSender $p, String $label)
	{
		$p->sendMessage(str_replace('{cmd}', $label, $this->getUsage()));
	}
	
}