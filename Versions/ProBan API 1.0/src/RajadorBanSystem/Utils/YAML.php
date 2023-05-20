<?php

declare(strict_types=1);

namespace RajadorBanSystem\Utils;

/*
  
  Rajador Developer

  ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
  ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
  ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█

  GitHub: https://github.com/RajadorDev

  Discord: Rajador#7070


*/


/** Rajador Developer A small YAML Lib :) **/

class YAML 
{

/*
* @param String $path
* @return array
*/

public static function load(String $path) : array
{
	if(file_exists($path))
	{
		return explode("\n", file_get_contents($path));
	}else{
		return [];
	}
}

/*
* @param String $index
* @param array $yaml
* @return mixed
*/
public static function getYamlIndex(String $index, array $yaml)
{
	$len = strlen($index);
	foreach ($yaml as $lines)
	{
		$key = YAML::getKeyFormat($lines, $len);
		if($key == $index)
		{
			return substr($lines, strlen($key) + 1);
		}
		
	}
	
}

/*
* @param String $line
* @param int $len
* @return String
*/
public static function getKeyFormat(String $line, int $len) : String 
{
	return substr($line, 0, $len);
}


/*
* @param String $index
* @param array $content
* @return int | null
*/
public static function getIndexId(String $index, array $content) 
{
	$len = strlen($index);
	foreach ($content as $i => $lines)
	{
		if(YAML::getKeyFormat($lines, $len) == $index)
		  return $i;
	}
	return null;
}

/*
* @param String $content
* @param String $index
* @param mixed $value
* @param bool $save
*/
public static function setYamlIndex(String $path, String $index, $value = null, bool $save = true)
{
	$yaml = YAML::load($path);
	$currentIndexValue = YAML::getYamlIndex($index, $yaml);
	$id = YAML::getIndexId($index, $yaml);
	if($currentIndexValue !== ' '.$value)
	{
		$yaml[$id] = $index.": '{$value}'"; 
	}
	YAML::save($path, implode("\n", $yaml));
}

/*
* @param String $path
* @param String $content
*/

public static function save(String $path, String $content)
{
	file_put_contents($path, $content);
}


}
