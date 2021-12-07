<?php


namespace services\data;

trait xdb {
	
	private $mapped = false;
	private $map = [];
	
	public function integrate($data) {
		foreach($data as $key => $val) {
			$engine = $this->getEngineForKey($key,$val);
			$storage[$engine][$key] = $val;
		}
	}
	
	public function getEngineForKey($key,$val) {
	   
		if(!is_scalar($var) && array_key_exists('object', $this->storage)) {
			return 'object';
		}
		
		if(!$this->mapped) {

			foreach($this->storage as $engine => $scheme) {
				foreach($scheme as $modelName => $fields) {
					foreach($fields as $fieldName => $definition) {
						$this->map[$fieldName] = [$engine];
					}
				}
			}
			$this->mapped = true;
		}
		
		if(isset($this->map[$key])) {
			return $this->map[$key];
		} else {
			if(array_key_exists('object', $this->storage)) {
				return 'object';
			} else {
				return 'blackhole';
			}
		}
		
	}
	
}
