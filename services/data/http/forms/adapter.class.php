<?php

namespace services\data\http\form;

class adapter extends \services\data\adapter {

	public function __construct($namespace = '', $method = 'POST') {
		$this->namespace = $namespace;
		$this->method = $method;

		switch ($method) {
			case 'POST':
				$data = $_POST;
				break;
			case 'GET':
				$data = $_GET;
				break;
			default:
				$data = [];
		}

		if ($this->namespace) {
			$data = $data[$this->namespace];
		}

		$this->data = $data;
	}

	public function create($data, $key) {
		//create request for data?
	}

	public function read($key = false) {
		//return 
		if ($key) {
			return $this->query($key);
		}
		return $this->data;
	}

	public function update($data, $conditions) {
		
	}

	public function delete($key) {
		
	}

	public function readRawData() {
		$fp = fopen("php://input");
		$c = false;
		if ($fp) {
			$c = '';
			while ($line = fgets($fp)) {
				$c .= $line;
			}
			fclose($fp);
		}
		return $c;
	}

	public function query($query, $parameters = false) {
		return $this->data[$query];
	}

}
