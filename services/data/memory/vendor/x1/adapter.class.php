<?php

namespace services\data\memory\vendor\x1;

class adapter extends \services\data\adapter {

	private $data = [];
	
	public function __construct( $settings = '') {

	}

	public function create($data, $id = false) {
		$this->data[$id] = $data;
		return true;
	}

	public function read($key) {
		return $this->data[$key];
	}

	public function update($data, $conditions = false) {
		$id = $conditions;
		$this->data[$id] = $data;
		return true;
	}

	public function delete($data, $conditions = false) {
		$id = $conditions;
		unset($this->data[$id]);
		return true;
	}
	
	public function exists($key) {
		return isset($this->data[$key]);
	}
	
	public function query($query, $parameters = false) {
		return $this->read($query);
	}
		
	private function write($path, $data) {
		return $this->create($data,$path);
	}

}
