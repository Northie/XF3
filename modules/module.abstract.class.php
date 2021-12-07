<?php

namespace modules;

abstract class module
{
//*
	use \settings\_settings {
		\settings\_settings::get as getSettings;
		\settings\_settings::set as setSettings;
	}
//*/
	protected $contexts;

	public final function __construct()
	{
		
		$class = get_called_class();
		$moduleFile = \settings\fileList::Load()->getFileForClass($class);

		$cacheKey = "context-module-endpoint--".str_replace('\\','-',$class);
		
		$cache = \settings\registry::Load()->get('APP_CACHE');

		$contexts = $cache->read($cacheKey);
		
		$this->contextDir = realpath(dirname($moduleFile) . DIRECTORY_SEPARATOR . 'contexts');
		
		if(!$this->contextDir) {
			return;
		}

		if ($contexts) {
			$this->contexts = $contexts;
		} else {

			$files = \utils\Tools::scanDirRecursive($this->contextDir);
			
			foreach ($files as $file) {

				if (strpos($file, ".php") === false || is_dir($file)) {
					continue;
				}
				
				if(strpos($file,'endpoints/') > -1) {
					
					$endpoint = str_replace([$this->contextDir,".class",".php"], "", $file);
					
					$e = explode(\DIRECTORY_SEPARATOR,trim($endpoint,\DIRECTORY_SEPARATOR));
					
					$context = \array_shift($e);
					\array_shift($e);
					$endpoint = implode("\\",$e);
					
					$this->contexts[$context][$endpoint] = true;
				}
				
			}

			$cache->create($this->contexts,$cacheKey);
		}
	}

	public function hasContext($context)
	{
		return isset($this->contexts[$context]);
	}

	public function hasContextEndPoint($context, $endPoint)
	{   
		return isset($this->contexts[$context][$endPoint]);
	}

	public function getContextEndpoints($context) {
	   
	   return $this->contexts[$context]; 
	   /*
	   $dir = $this->contextDir.DIRECTORY_SEPARATOR.$context.DIRECTORY_SEPARATOR.'endpoints';
	   
	   $files = scandir($dir);
	   
	   foreach ($files as $file) {
		   if(in_array($file,['.','..'])) {
			   continue;
		   }
		   
		   $filePath = $dir.DIRECTORY_SEPARATOR.$file;
		   
		   $endpoints[] = \settings\fileList::Load()->getClassForFile($filePath);
		   
	   }
	   
	   return $endpoints;
	   //*/
	   
	}
	
	abstract public function init();

}
