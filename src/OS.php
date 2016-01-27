<?php

class OS
{
	static public function isWindows()
	{
		return strstr(strtolower(php_uname()), 'windows') !== false;
	}
	
	static public function run($command)
	{
		if (self::isWindows()) {
			$bashPath = 'C:\Program Files (x86)\Git\bin';
			$tmp = tempnam(__DIR__.'/tmp', 'cmd');
			file_put_contents($tmp, $command);
			system('cd '.$bashPath.' & sh.exe --login '.$tmp, $return);
			unlink($tmp);
		} else {
			system($command, $return);
		}
		
		return $return;
	}
	
	static public function tweakBuild(array &$build)
	{
		if (self::isWindows()) {
			foreach ($build as &$entry) {
				$entry = str_replace('cmake', 'cmake -G "Unix Makefiles"', $entry);
			}
		}
	}
	
	static public function bashize($path)
	{
		if (self::isWindows()) {
			$path = str_replace('\\', '/', $path);
			$path = preg_replace('#^([A-Z]{1}):#', '/$1', $path);
		}
		
		return $path;
	}
}