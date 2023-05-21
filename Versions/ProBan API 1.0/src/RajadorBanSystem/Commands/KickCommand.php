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

use pocketmine\{Server, Player};

use RajadorBanSystem\ProBan;


class KickCommand extends Command 
{
	
	public function __construct(ProBan $pl, array $config)
	{
		$this->plugin = $pl;
		if($config['usage'] == null || trim($config['usage']) == '')
		{
			$config['usage'] = 'use: /{cmd} <player> <reason>';
		}
		parent::__construct(
			'kick',
			'Rajador expel system :)',
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
		  
		$target = Server::getInstance()->getPlayer($args[0]);
		$reason = null;
		if(isset($args[1]))
		{
			$reason = $args;
			unset($reason[0]);
			$reason = implode(' ', $reason);
		}
		if($target instanceof Player && $this->plugin->kickPlayer($target, $p, $reason))
		{
			$p->sendMessage(str_replace('{target}', $target->getName(), ProBan::getMessage('kick-sucess')));
		}else{
			$p->sendMessage(str_replace('target', $args[0], ProBan::getMessage('player-notfound')));
		}
	}
	
	public function sendUsage(CommandSender $p, String $label)
	{
		$p->sendMessage(str_replace('{cmd}', $label, $this->getUsage()));
	}
	
}