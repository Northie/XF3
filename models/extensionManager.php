<?php

namespace models;

class extensionManager {
	private static $instances = [];
	
	private $extensions = [];

	
	private function __construct() {
		
		$this->model = trim($model,"\\");
		
		if(!isset($this->models[$this->model])) {
			$this->models[$this->model] = [];
		}

	}
	
	public static function Load($model) {
		$model = trim($model,"\\");
		if(!isset(self::$instances[$model])) {
			$c = __CLASS__;
			self::$instances[$model] = new $c;
		}
		return self::$instances[$model];
	}
	
	public function addExtension($extension) {				
		$this->$extensions[] = $extension;
		return $this;
	}
	
	public function getExtensions() {
		return $this->$extensions;
	}
}

