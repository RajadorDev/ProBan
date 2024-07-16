<?php

declare(strict_types=1);

namespace RajadorBanSystem;

/*
  
  Rajador Developer

  ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
  ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
  ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█

  GitHub: https://github.com/RajadorDev

  Discord: Rajador#7070


*/

use pocketmine\utils\Config;

use pocketmine\Player;

use RajadorBanSystem\Commands\
{
	BanCommand,
	BanIpCommand,
	KickCommand,
	UnbanCommand,
	ProBanCommand
};

use RajadorBanSystem\Events\
{
	PlayerBannedEvent,
	PlayerExpelledEvent,
	AccountBannedEvent
};

use RajadorBanSystem\Discord\ProBanDiscord;

class ProBan extends \pocketmine\plugin\PluginBase 
{
	
	const PLAYERS_LIST = 'P';
	
	const ADDRESS_LIST = 'A';
	
	const PLAYER_EXPELL_EVENT = 10;
	
	const PLAYER_BANNED_EVENT = 20;
	
	private $bans;
	
	protected static $instance = null;
	
	protected static $messages = [];
	
	protected $commands = [];
	
	public function onEnable() 
	{
		$this->getLogger()->info("\n \n§eProBan§f Plugin\n \n§bCreated By: §eRajador Developer\n \n§eGitHub: §fhttps://github.com/RajadorDev \n \n§fYou§cTube: §fhttps://youtube.com/@RajadorTv\n \n§9Discord: §fhttps://discord.io/rajador\n \n");
		$this->getLogger()->info('§eOpenning folders...');
		$this->initFolders();
		$this->getLogger()->info('§eOppenning files...');
		$this->initFiles();
		$this->registerCommands();
		$this->initConfig();
		$this->initDiscord();
		$this->getServer()->getPluginManager()->registerEvents(new Events($this), $this);
		self::$instance = $this;
	}
	
	public function initDiscord() 
	{
		$this->discordSys = new ProBanDiscord($this);
	}
	
	public function getDiscordSystem() : ProBanDiscord 
	{
		return $this->discordSys;
	}
	
	public static function getInstance() : ProBan 
	{
		return self::$instance;
	}
	
	public static function getMessage(String $id) : String 
	{
		if(isset(self::$messages[$id]))
		{
			return self::$messages[$id];
		}else{
			self::getInstance()->getLogger()->critical('§cMessage: '.$id.' not found!');
		}
	}
	
	public function initFolders()
	{
		@mkdir($this->getDataFolder());
	}
	
	
	public function initFiles()
	{
		$this->saveResource('config.yml');
		$this->bans = new Config($this->getDataFolder().'bans.json', Config::JSON, [ProBan::PLAYERS_LIST => [], ProBan::ADDRESS_LIST => []]);
		$this->saveResource('messages.yml');
		self::$messages = (new Config($this->getDataFolder().'messages.yml', Config::YAML))->getAll();
		$this->saveResource('DiscordInfo.yml');
		$this->discordConfig = new Config($this->getDataFolder().'DiscordInfo.yml');
	}
	
	public function initConfig() 
	{
		if((bool) $this->getConfigValue('block-local-adr'))
		{
			$this->blockAddress('127.0.0.1');
		}
		if((bool) $this->getConfigValue('auto-block-adr') && isset($this->bans->getAll()[ProBan::ADDRESS_LIST]))
		{
			foreach ($this->bans->getAll()[ProBan::ADDRESS_LIST] as $address => $data)
			  $this->blockAddress($address);
		}
	}
	
	public function onDisable() 
	{
		$this->bans->save();
	}
	
	public static function isIp(String $adr) : bool 
	{
		return (count(explode('.', $adr)) > 2 && preg_match('/[A-Za-z]/', $adr) == 0);
	}
	
	public function getConfigValue(String $id, $defaultValue = true)
	{
		return isset($this->getConfig()->getAll()[$id]) ? $this->getConfig()->getAll()[$id] : $defaultValue;
	}
	
	/*
	* @param String $id
	* @param String | int | bool $value
	* @return bool
	*/
	public function setConfigValue(String $id, $value) : bool 
	{
		if(isset($this->getConfig()->getAll()[$id]))
		{
			$all = $this->getConfig()->getAll();
			$all[$id] = $value;
			$this->getConfig()->setAll($all);
			unset($all);
			if((bool) $this->getConfig()->getConfigValue('auto-save', true))
			  $this->getConfig()->save(true);
			return true;
		}
		return false;
	}
	

	
	public function getBansList($type = ProBan::PLAYERS_LIST) : array
	{
		return $this->bans->getAll()[$type];
	}
	
	/*
	* @param String $address
	* @return bool
	*/
	public function blockAddress(String $address) : bool
	{
		if(method_exists($this->getServer()->getNetwork(), 'blockAddress'))
		{
			$this->getServer()->getNetwork()->blockAddress($address, -1);
			return true;
		}else{
			$this->getLogger()->notice('API without block address method!');
		}
	return false;
	}
	
	private function registerCommands()
	{
		$this->commands = [
		'ban' => new BanCommand($this, $this->getCommandConfig('ban')),
		'banip' => new BanIpCommand($this, $this->getCommandConfig('banip')),
		'kick' => new KickCommand($this, $this->getCommandConfig('kick')),
		'unban' => new UnbanCommand($this, $this->getCommandConfig('unban')),
		'proban' => new ProBanCommand($this)
		];
		$map = $this->getServer()->getCommandMap();
		foreach ($this->commands as $cmd)
		{
			$oldCmd = $map->getCommand($cmd->getName());
			if($oldCmd !== null)
			{
			  $oldCmd->setLabel($cmd->getName().'__');
			  $oldCmd->unregister($map);
			}
			  
		  $map->register('['.$cmd->getName().']', $cmd);
		}
	}
	
	/*
	* @param String $command
	* @return Command | null
	*/
	public function getCommand($name)
	{
		return isset($this->commands[$name]) ? $this->commands[$name] : null;
	}
	
	
	public function getCommandConfig(String $command) : array 
	{
		$preConfig = isset($this->getConfig()->getAll()['commands'][$command]) ? $this->getConfig()->getAll()['commands'][$command] : array();
		$config = array();
		$defaultPermission = $command.'.use';
		$config['permission'] = isset($preConfig['permission']) ? (string) $preConfig['permission'] : $defaultPermission;
		$config['aliases'] = isset($preConfig['aliases']) ? (array) $preConfig['aliases'] : [];
		$config['usage'] = isset($preConfig['usage']) ? $preConfig['usage'] : null;
		return $config;
	}
	
	
	/*
	* @param Player $player
	* @return bool
	*/
	public function isPlayerBanned(Player $p) : bool 
	{
		return (isset($this->bans->getAll()[ProBan::PLAYERS_LIST][strtolower($p->getName())]) || $this->isAddressBanned($p->getAddress()));
	}
	
	/*
	* @param String $playerName
	* @return bool
	*/
	public function isAccountBanned(String $playerName) : bool 
	{
		return isset($this->bans->getAll()[ProBan::PLAYERS_LIST][strtolower($playerName)]);
	}
	
	/*
	* @param String $address
	* @return bool
	*/
	public function isAddressBanned(String $address) : bool 
	{
		return isset($this->bans->getAll()[ProBan::ADDRESS_LIST][$address]);
	}
	
	/*
	* @param Player | String $target
	* @param CommandSender | Player $adm
	* @param String $reason
	* @return bool 
	*/
	public function banAccount($target, $adm, $reason = null) : bool 
	{
		$reason = $reason === null ? $reason = $this->getDefaultReason() : $reason;
		if(!($target instanceof Player))
		{
			$targetName = $target;
			$target = $this->getServer()->getPlayerExact($targetName);
		}else{
			$targetName = $target->getName();
		}
		$vars = [array('{name}', '{adm}', '{reason}', '{line}'), array($targetName, $adm->getName(), $reason, PHP_EOL)];
		
		$this->addBanData(ProBan::PLAYERS_LIST, $targetName,
		array(
			'adm' => $adm->getName(),
			'date' => date('d/m/Y H:i'),
			'reason' => $reason
			));
		
		if((bool) $this->getConfigValue('send-ban-message'))
		{
			$msg = str_ireplace($vars[0], $vars[1], ProBan::getMessage('ban-msg'));
			foreach ($this->getServer()->getOnlinePlayers() as $p)
			  $p->sendMessage($msg);
		}
		if($target instanceof Player)
		{
		  $this->getServer()->getPluginManager()->callEvent($e = new PlayerBannedEvent($target, $adm, $reason));
			$target->close('', str_ireplace($vars[0], $vars[1], ProBan::getMessage('ban-screen')));
		}else{
			$this->getServer()->getPluginManager()->callEvent($e = new AccountBannedEvent($targetName, $adm, $reason));
		}
		return true;
	}
	
	/* @return String */
	public function getDefaultReason() : String 
	{
		return $this->getConfigValue('default-reason', 'No Reason');
	}
		
		/*
		* @param int $type
		* @param String $target
		* @param array $data
		*/
		public function addBanData($type = ProBan::PLAYERS_LIST, String $target, array $data)
		{
			$target = ($type !== ProBan::ADDRESS_LIST) ? strtolower($target) : $target;
			$all = $this->bans->getAll();
			$all[$type][$target] = $data;
			$this->bans->setAll($all);
			if((bool) $this->getConfigValue('auto-save'))
			{
				$this->bans->save(true);
			}
			unset($all);
		}
	
	/*
	* @param Player | String $target
	* @param CommandSender | Player $adm
	* @param String $reason
	* @return bool
	*/
	public function banAddress($target, $adm, $reason = null) : bool 
	{
		$reason = $reason === null ? $this->getDefaultReason() : $reason;
		if($target instanceof Player)
		{
			$address = $target->getAddress();
		}elseif(ProBan::isIp($target))
		{
			$address = $target;
			foreach ($this->getServer()->getOnlinePlayers() as $p)
			  if($p->getAddress() == $target)
			    	$target = $p;
		}elseif(is_string($target))
		{
			foreach($this->getServer()->getOnlinePlayers() as $p)
			{
			  if(strtolower($p->getName()) == strtolower($target))
			  {
			    $target = $p;
			    $address = $p->getAddress();
			  }
			}
		}
		
		if(isset($address))
		{
			$targetName = $target instanceof Player ? $target->getName() : $target;
			$this->addBanData(ProBan::ADDRESS_LIST, 
			$address,
			array(
			  'adm' => $adm->getName(),
			  'date' => date('d/m/Y H:i'),
			  'reason' => $reason,
			  'name' => ProBan::isIp($targetName) ? 'Not Found' : $targetName
			)
			);
			$vars = [array('{name}', '{adm}', '{reason}', '{line}'), array($targetName, $adm->getName(), $reason, PHP_EOL)];
			if(!ProBan::isIp($targetName) && $this->getConfigValue('send-ban-message'))
			{
				$vars = [array('{name}', '{adm}', '{reason}', '{line}'), array($targetName, $adm->getName(), $reason, PHP_EOL)];
				$msg = str_ireplace($vars[0], $vars[1], ProBan::getMessage('ban-msg'));
				foreach ($this->getServer()->getOnlinePlayers() as $p)
				  $p->sendMessage($msg);
			}
			
			if($target instanceof Player)
			{
				$this->getServer()->getPluginManager()->callEvent($e = new PlayerBannedEvent($target, $adm, $reason));
				$target->close('', str_ireplace($vars[0], $vars[1], ProBan::getMessage('ban-screen')));
			}
			if((bool) $this->getConfigValue('auto-block-adr', true))
			  $this->blockAddress($address);
			  
			unset($vars);
			return true;
		}else{
			return false;
		}
	return false;
		
	}
	
	/*
	* @param Player $target
	* @param CommandSender $adm
	* @param String $reason
	* @return bool 
	*/
	public function kickPlayer(Player $target, $adm, String $reason = null) : bool 
	{
		$reason = $reason === null ? $this->getDefaultReason() : $reason;
		$this->getServer()->getPluginManager()->callEvent($e = new PlayerExpelledEvent($target, $adm, $reason));
		$vars = [array('{name}', '{adm}', '{reason}', '{line}'), array($target->getName(), $adm->getName(), $reason, PHP_EOL)];
		$target->close('', str_ireplace($vars[0], $vars[1], ProBan::getMessage('kick-screen')));
		if((bool) $this->getConfigValue('send-kick-message'))
		{
			$msg = str_ireplace($vars[0], $vars[1], ProBan::getMessage('kick-msg'));
			foreach($this->getServer()->getOnlinePlayers() as $all)
			  $all->sendMessage($msg);
		}
		return true;
	}
	
	
	/*
	* @param Player | String $user
	* @return null | array
	*/
	public function getBanData($user) 
	{
		if($user instanceof Player && $this->isPlayerBanned($user))
		{
			if($this->isAccountBanned($user->getName()))
			{
				return $this->bans->getAll()[ProBan::PLAYERS_LIST][strtolower($user->getName())];
			}elseif($this->isAddressBanned($user->getAddress()))
			{
				return $this->bans->getAll()[ProBan::ADDRESS_LIST][$user->getAddress()];
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	
	/*
	* @param String $target
	* @return bool
	*/
	public function unbanAccount(String $account) : bool 
	{
		$all = $this->bans->getAll();
		$account = strtolower($account);
		if($this->isAccountBanned($account))
		{
		  unset($all[ProBan::PLAYERS_LIST][$account]);
		  $this->bans->setAll($all);
		  if((bool) $this->getConfigValue('auto-save'))
		    $this->bans->save(true);
		  
		  return true;
		}
		return false;
	}
	
	public function unbanAddress(String $adr) : bool 
	{
		if($this->isAddressBanned($adr) && ProBan::isIp($adr))
		{
			$all = $this->bans->getAll();
			unset($all[ProBan::ADDRESS_LIST][$adr]);
			$this->bans->setAll($all);
			if((bool) $this->getConfigValue('auto-save'))
			  $this->bans->save(true);
			  
			return true;
		}
		return false;
	}
	
	public function getDiscordConfig()
	{
		return $this->discordConfig;
	}
	
	
	public function unban(String $banned) : bool
	{
		if($this->isAccountBanned($banned))
		{
			$this->unbanAccount($banned);
			return true;
		}elseif($this->isAddressBanned($banned))
		{
			$this->unbanAddress($banned);
			return true;
		}
		
		return false;
	}
	
}
