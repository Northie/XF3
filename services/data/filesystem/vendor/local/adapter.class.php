<?php

namespace services\data\filesystem\vendor\local;

class adapter extends \services\data\adapter {

	public function __construct($settings = '') {
		$this->path = rtrim($settings['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		if ($settings['namespace']) {
			$namespace = \utils\Tools::filePathProtect($settings['namespace']);
			$this->path .= DIRECTORY_SEPARATOR.'namespaces' .DIRECTORY_SEPARATOR . $namespace;
		}
	}

	public function create($data, $id = false) {

		$key = $id;
		//if ($this->read($key)) {
		//	throw new \Exception("Could not create " . __NAMESPACE__ . ": Key exists");
		//}

		$path = $this->path . \DIRECTORY_SEPARATOR . $key;

		return ($this->write($path, $data) ? $key : false);
	}

	public function read($key) {

		$path = \utils\Tools::filePathProtect($this->path . $key);

		if ($this->exists($key)) {
			$data = file_get_contents($path);
			return $data;
		} else {
			return false;
		}
	}

	public function update($data, $conditions = false) {

		$key = $conditions;
		if ($this->exists($key)) {

			$path = \utils\Tools::filePathProtect($this->path . \DIRECTORY_SEPARATOR . $key);

			return ($this->write($path, $data) ? $key : false);
		} else {
			throw new \Exception("Could not update " . __NAMESPACE__ . ": Key does not exist");
		}
	}

	public function delete($data, $conditions = false) {
		$exists = 0;
		$key = $conditions;

		if ($this->exists($key)) {
			$path = $this->path . $key;
			return unlink($path);
		} else {
			throw new \Exception("Could not delete " . __NAMESPACE__ . ": Key does not exist");
		}
	}

	public function exists($key) {
		$path = \utils\Tools::filePathProtect($this->path . $key);
		return file_exists($path);
	}

	private function write($path,$data) {

		if($this->makeDirForFile($path)) {
			return file_put_contents($path, $data);
		}
		return false;
	}

	public function query($query, $parameters = false) {
		return false;
	}

	private function makeDirForFile($path) {
		$path = \utils\Tools::filePathProtect($path);

		if(is_dir($path)) {
			if(is_writable($path)){
				return true;
			}
		}

		$path = \explode(\DIRECTORY_SEPARATOR,$path);

		array_pop($path);

		$path = implode(\DIRECTORY_SEPARATOR,$path);

		if(!is_dir($path)) {
			if(!\mkdir($path, 0777, true)) {
				throw new \Exception("Failed to make dir ".$path);
			}
			return true;
		} else {
			return chmod($path, 0777);
		}

	}

}
