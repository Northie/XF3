<?php

namespace libs\data;

class genericDataService {
	
	private $data = false;
	private $ready = false;
	private $valid = false;
	
	public function isReady() {
		return (bool) $data;
	}
	public function isValid() {
		return $this->valid;
	}
	public function getData() {
		return $this->data;
	}
	public function setData($data) {
		$this->valid = true;
		$this->data = $data;
	}
}
