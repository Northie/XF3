<?php

namespace services\data\cache\vendor\none;

class adapter extends \services\data\adapter {

	use \services\data\cache\cache;
	
	public function __construct($settings) {
		
	}

	public function create($data, $id = false) {
		return true;
	}

	public function read($key) {
		return false;
	}

	public function update($data, $conditions = false) {
		$key = $conditions;
		return $this->create($data, $key);
	}

	public function delete($key, $force = false) {
		return true;
	}
	
	public function query($query, $parameters = false) {
		return [];
	}

}
