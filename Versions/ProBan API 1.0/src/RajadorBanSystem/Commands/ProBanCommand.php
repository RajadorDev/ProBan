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


use pocketmine\command\{CommandSender, Command};

use RajadorBanSystem\ProBan;
use RajadorBanSystem\Discord\ProBanDiscord;

class ProBanCommand extends Command 
{
	
	public function __construct(ProBan $pl)
	{
		$this->plugin = $pl;
		parent::__construct(
			'proban',
			'Rajador Developer ProBan Plugin',
			"§8-==(§eProBan§8)==-\n§8> §bTo change the WebHook Address use: §f/{cmd} webhook <URL>\n§8> §bInformation about the plugin: §f/{cmd} about\n§8> §bTo see ban list use: §f/{cmd} banlist <accounts/ips> <page>\n§8>§b Information about an banishment use: §f/{cmd} seeban <player_banned>",
			['pb']
		);
		$this->setPermission('pro-ban.use');
	}
	
	public function execute(CommandSender $p, $label, array $args)
	{
		if(!$this->testPermission($p))
		  return false;
		
		if(!isset($args[0]))
		  return $this->sendUsage($p, $label);
		  
		switch(strtolower($args[0]))
		{
			case 'webhook':
			case 'setwebhook':
			  if(isset($args[1]))
			  {
			  	if(ProBanDiscord::isURL($args[1]))
			  	{
			  		if($this->plugin->getDiscordSystem()->setConfigValue('webhook-url', $args[1]))
			  		{
			  			$p->sendMessage('§aWebhook address set successfully.');
			  		}else{
			  			$p->sendMessage('§cAn error occurred while setting the webhook');
			  		}
			  	}else{
			  		$p->sendMessage('§c'.$args[1].'§e is not a valid §9Discord§e WebHook URL');
			  	}
			  }else{
			  	$p->sendMessage('§eUse: §f/'.$label.' webhook <URL>');
			  }
			break;
			case 'about':
			case 'sobre':
			case 'creator':
			case 'version':
			case 'versao':
				$p->sendMessage("§8-==(§eProBan§8)==-\n§8> §bCreated By: §fRajador Developer\n§8> §bDiscord: §fRajador#7070\n§8> §fYou§cTube§b: §fyoutube.com/@Rajadortv\n§8> §bRajador Developer Group: §fhttps://discord.io/rajador\n§8> §bVersion: §f0.1 Beta");
			break;
			case 'list':
			case 'lista':
			case 'bans':
			case 'banlist':
			case 'banslist':
				
				if(!isset($args[1]))
				{
					return $p->sendMessage('§eUse: §f/'.$label.' '.$args[0].' <ips/accounts> <page>');
				}
				$page = (isset($args[2]) && is_numeric($args[2])) ? (int) $args[2] : 1;
				
				switch(strtolower($args[1]))
				{
					case 'ip':
					case 'ips': 
					case 'address':
					case 'addres':
					  $type = ProBan::ADDRESS_LIST;
					break;
					case 'accounts':
					case 'account':
				  case 'players':
				  case 'player':
				    $type = ProBan::PLAYERS_LIST;
				  break;
				  default:
				  	return $p->sendMessage('§cInvalid list: §f'.$args[1].' §cYou can use: §fips, accounts');
				  break;
				}
				
				$list = array_keys($this->plugin->getBansList($type));
				
				if(count($list) <= 0)
				  return $p->sendMessage('§cThere are no bans at the moment.');
				
				if($page <= 0)
				  $page = 1;
				
				$banneds = [];
				$finish = $page * 4;
				$start = $finish - 4;
				$current = $start;
				$banType = $type == ProBan::PLAYERS_LIST ? 'Player' : 'Address';
				while ($current < $finish)
				{
					$currentId = $current + 1;
					if(isset($list[$current]))
					  $banneds[] = '§e[§c'.$currentId.'§e] §f'.$banType.': §e'.$list[$current];
					else 
					  break;
					  
					$current+=1;
				}
				if(count($banneds) <= 0)
				  return $p->sendMessage('§cThere are no bans to show at page§f: '.$page);
				
				$nextPage = $page + 1;
				$message = '§8-==(§eProBan§8)==-'.PHP_EOL.implode(PHP_EOL, $banneds).PHP_EOL.'§8> §eUse: §f/'.$label.' '.$args[0].' '.$args[1].' '.$nextPage.' §eto the next page.';
				if(mt_rand(1,4) == 4)
				  $message .= PHP_EOL.'§8> §eYou can use: §f/'.$label.' see <player_banned> §eTo view information about the banishment';
				
				$p->sendMessage($message);
				
			break;
			default:
				$this->sendUsage($p, $label);
		  break;
		  case 'see':
		  case 'seeban':
		  case 'aboutban':
		  case 'seepunish':
		    if(!isset($args[1]))
		      return $p->sendMessage('§eUse: §f/'.$labe.' '.$args[0].' <player_banned>');
		    
		  	if($this->plugin->isAccountBanned($args[1]))
		  	{
		  		$data = $this->plugin->getBansList(ProBan::PLAYERS_LIST)[strtolower($args[1])];
		  	}elseif($this->plugin->isAddressBanned($args[1]))
		  	{
		  		$data = $this->plugin->getBansList(ProBan::ADDRESS_LIST)[$args[1]];
		  	}else{
		  		$target = ProBan::isIp($args[1]) ? 'Address' : 'Player';
		  		
		  		return $p->sendMessage('§c'.$target.' §7'.$args[1].'§c not found in BanList');
		  	}
		  	$target = ProBan::isIp($args[1]) ? '§fAddress: §b'.$args[1] : '§fPlayer: §b'.$args[1];
		  	$p->sendMessage('§8-==(§eProBan§8)==-'.PHP_EOL.'§8= '.$target.PHP_EOL.'§8= §fAdmin: §b'.$data['adm'].PHP_EOL.'§8= §fReason: §b'.$data['reason'].PHP_EOL.'§8= §fDate: §b'.$data['date']);
		  	
		  break;
			
		}
	}
	
	public function sendUsage(CommandSender $p, String $label)
	{
		$p->sendMessage(str_replace('{cmd}', $label, $this->getUsage()));
	}
	
}