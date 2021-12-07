<?php

namespace services\data\filesystem\vendor\rackspacecdn;

class adapter extends services\data\adapter {

	
	public function __construct(string $namespace = '') {
		$this->httpClient = \services\data\http\client\factory::Build();
	}

	public function create($data, $key=false) {
		
	}

	public function read($key) {
		
	}


	public function update($data, $conditions=false) {

	}

	public function delete($key) {

	}
	
	public function exists($key) {

	}
	
}
