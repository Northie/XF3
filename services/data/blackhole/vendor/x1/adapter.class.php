<?php

namespace services\data\blackhole\vendor\x1;

class adapter extends \services\data\adapter {

	
	public function __construct( $settings = '') {

	}

	public function create($data, $id = false) {
			return true;
	}

	public function read($key) {
			return [];
	}

	public function update($data, $conditions = false) {		
			return true;
	}

	public function delete($data, $conditions = false) {
			return true;
	}
	
	public function exists($key) {
			return false;
	}
	
		public function query($query, $parameters = false) {
			
		}
		
	private function write($path, $data) {
			return true;
	}

}
