<?php
namespace utils\traits;

trait magic {
	protected $properties = [];
	
	public function __get($key) {
		return $this->properties[$key];
	}
	
	public function __set($key,$val) {
		$this->properties[$key] = $val;
		return true;
	}
	
	public function __call($name, $arguments) {
		$mode = \substr(strtolower($name), 0, 3);
		if($mode != 'set' && $mode != 'get') {
			throw new Exception('method name must start get or set');
		}
		
		$property = trim(\substr($name, 3, (strlen($name) -3)));
		
		if($property == '') {
			throw new Exception('method name must be (s|g)et(.?)');
		}
		
		switch($mode) {
			case 'get':
				return $this->properties[$property];
				break;
			case 'set':
				$this->properties[$property] = $arguments;
				break;
		}
		return;
	}
}

