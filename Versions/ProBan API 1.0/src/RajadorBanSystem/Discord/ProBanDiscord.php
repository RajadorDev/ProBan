<?php

namespace RajadorBanSystem\Discord;

/*
  
  Rajador Developer

  ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
  ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
  ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█

  GitHub: https://github.com/RajadorDev

  Discord: Rajador#7070


*/


use RajadorBanSystem\ProBan;

use RajadorBanSystem\Event\{AccountBannedEvent, PlayerBannedEvent};

use RajadorBanSystem\Utils\YAML;

use pocketmine\event\Event;

use pocketmine\{Player, Server};

class ProBanDiscord 
{
	
	const CONNECTION_FAIL = 0;
	
	const WEBHOOK_FAIL = 1;
	
	const WITHOUT_WEBHOOK_FAIL = 2;
	
	const INVALID_WEBHOOK_URL_FAIL = 3;
	
	const DISCORD_DISABLED = 4;
	
	
	private $isConnected = true;
	
	private $webhook = null;
	
	private $config;
	
	private static $instance = null;
	
	private $errors = array(
		ProBanDiscord::WITHOUT_WEBHOOK_FAIL => 'DiscordInfo.yml without WebHook Link!',
		ProBanDiscord::INVALID_WEBHOOK_URL_FAIL => 'Disabling ProBanDiscord WebHook System! invalid WebHook Link in DiscordInfo.yml',
		ProBanDiscord::CONNECTION_FAIL => 'Disabling ProBanDiscord WebHook System! Connection Fail, make sure the webhook address is correct!'
	);
	
	public function __construct(ProBan $pl)
	{
		$this->plugin = $pl;
		if((bool) $pl->getConfigValue('enable-discord', false))
		{
		  $this->config = $pl->getDiscordConfig()->getAll();
		  $webhook = $this->config['webhook-url'];
		  if($webhook == 'your_webhook')
		  {
		    $this->disconnect(ProBanDiscord::WITHOUT_WEBHOOK_FAIL);
		  }elseif(!ProBanDiscord::isURL($webhook))
		  {
		  	$this->disconnect(ProBanDiscord::INVALID_WEBHOOK_URL_FAIL);
		  }
		  if($this->isConnected())
		    $this->webhook = $webhook;
		}else{
		  $pl->getLogger()->notice('Discord WebHook System Disabled!' . PHP_EOL . '§bTo turn on the discord system put §fenable-discord§b in §fconfig.yml §bas §atrue');
		}
	self::$instance = $this;
	}
	
	public function disconnect($cause = ProBanDiscord::CONNECTION_FAIL)
	{
		$this->isConnected = false;
		isset($this->errors[$cause]) 
		? 
		$this->plugin->getLogger()->critical($this->errors[$cause])
		: 
		$this->plugin->getLogger()->critical('An unknown error occurred in the DiscordWebHook system');
	}
	
	public function enable()
	{
		$this->isConnected = true;
	}
	
	public function isConnected() : bool 
	{
		return $this->isConnected;
	}
	
	public function canSend() : bool 
	{
		return $this->isConnected() && $this->webhook !== null;
	}
	
	/*
	* @param array $webHook
	* @return bool
	*/
	public function sendWebHook($type, Event $e) : bool 
	{
		if($this->canSend())
		{
			$typeName = ($e->getEventId() == ProBan::PLAYER_BANNED_EVENT) ? 'ban' : 'kick';
			$msg = $this->getConfig();
			$description = isset($msg[$typeName.'-description']) ? $msg[$typeName.'-description'] : 'Punish'. $typeName. ' without description';
			$title = isset($msg[$typeName.'-title']) ? $msg[$typeName.'-title'] : 'WebHook Alert';
			$color = isset($msg['color']) ? $msg['color'] : 'red';
			$target = $e->getTarget();
			$webHook = array(
				'target' => $target instanceof Player ? $target->getName() : $target,
				'adm' => $e->getAdm()->getName(),
				'reason' => $e->getReason(),
				'description' => $description,
				'date' => date('h/m/Y H:i'),
				'username' => isset($msg['username']) ? $msg['username'] : 'Rajador Developer ProBan Plugin',
				'avatar-url' => isset($msg['avatar-url']) ? $msg['avatar-url'] : 'https://www.pngall.com/wp-content/uploads/2017/05/Alert-Download-PNG.png',
				'title' => $title,
				'c_hex' => $color
				);
		  Server::getInstance()->getScheduler()->scheduleAsyncTask(new WebHookTask($this->getWebHookAddress(), $webHook));
		  return true;
		}
	return false;
	}
	
	public function getWebHookAddress()
	{
		return $this->webhook;
	}
	
	public function getConfig() : array 
	{
		return $this->config;
	}
	
	public function getConfigValue(String $id, $default = 'Unknown')
	{
		if(isset($this->getConfig()[$id]))
		{
			return $this->getConfig()[$id];
		}else{
			$this->plugin->getLogger()->critical('DiscordInfo.yml without '.$id.' settup');
			return $default;
		}
	}
	
	/*
	* @param String $id
	* @param String | int | bool $value
	* @return bool
	*/
	public function setConfigValue(String $id, $value) : bool
	{
		$all = $this->plugin->getDiscordConfig()->getAll();
		if(isset($all[$id]))
		{
			$this->config[$id] = $value;
			if($id == 'webhook-url' && ProBanDiscord::isURL($value))
			{
			  $this->webhook = $value;
			  if(!$this->isConnected)
			    $this->enable();
			}
			
			$all[$id] = $value;
			$this->plugin->getDiscordConfig()->setAll($all);
			//$this->plugin->getDiscordConfig()->save(true);
			$path = $this->plugin->getDataFolder().'DiscordInfo.yml';
			YAML::setYamlIndex($path, $id, $value, true);
			unset($all);
			return true;
		}
	unset($all);
	return false;
	}
	
	public static function callResponse($response = true) 
	{
		if($response !== true)
		{
			self::getInstance()->getPlugin()->getLogger()->critical('An unknown error occurred in the DiscordWebHook system');
			self::getInstance()->getPlugin()->getLogger()->critical($response);
		}
	}
	
	public function getPlugin() : ProBan 
	{
		return $this->plugin;
	}
	
	public static function isURL(String $url) : bool
	{
    $url = trim($url);
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        $urlParts = parse_url($url);
        return (isset($urlParts['scheme']) && isset($urlParts['host']));
    }
    
    return false;
  }

	
	public static function getInstance() : ProBanDiscord 
	{
		return self::$instance;
	}
	
	
}

