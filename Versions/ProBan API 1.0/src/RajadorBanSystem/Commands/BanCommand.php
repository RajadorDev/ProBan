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


class BanCommand extends Command
{
	
	public function __construct(ProBan $proban, array $config)
	{
		$this->plugin = $proban;
		if($config['usage'] == null || trim($config['usage']) == '')
		{
			$config['usage'] = 'use: /ban <player> <reason>';
		}
		parent::__construct(
			'ban',
			'Rajador ban system :)',
			$config['usage'],
			$config['aliases']
		);
		$this->setPermission($config['permission']);
	}
	
	public function execute(CommandSender $p, String $label, array $args)
	{
		if(!$this->testPermission($p))
		  return false;
		
		if(!isset($args[0]))
		  return $this->sendUsage($p, $label);
		
		if(!ProBan::isIp($args[0]))
		{
			$reason = null;
			if(isset($args[1]))
			{
				$reason = $args;
				unset($reason[0]);
				$reason = implode(' ', $reason);
			}
			if($this->plugin->banAccount($args[0], $p, $reason))
			{
				$p->sendMessage(str_replace('{target}', $args[0], ProBan::getMessage('ban-sucess')));
			}else{
				$p->sendMessage('§cAn error occurred with this action.');
			}
		}else{
			$p->sendMessage('§cUse: §f/ban-ip §ccommand to ban an ip');
		}
		
	}
	
	public function sendUsage(CommandSender $p, String $labelUsed)
	{
		$p->sendMessage(str_replace('{cmd}', $labelUsed, $this->getUsage()));
	}
	
}
