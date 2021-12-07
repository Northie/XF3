<?php

namespace services\data\cache\vendor\file;

class adapter extends \services\data\adapter {

	use \services\data\cache\cache;
	
	private $path;
	private $dir = 'cache';
	private $prefix = 'X1-cache-';
	private $suffix = '.json';
	
	
	public function __construct($settings) {
		$this->path = $settings['path'];
		$this->adapter = \services\data\filesystem\vendor\local\factory::Build($settings);
	}

	public function create($data, $id = false) {
			
		$key = $id;

		$expires = time() + $this->getLifetime();
	
		$meta = ['expires'=>$expires];
			
		$data = json_encode([
					 'meta'=>$meta
					,'data'=>$data
				],JSON_PRETTY_PRINT);
				
				//file put contents
		return $this->adapter->create($data, $this->prefix.$key.$this->suffix);
	}

	public function read($key) {

		$json = $this->adapter->read($this->prefix.$key.$this->suffix);
		//file get contents
				
		$data = json_decode($json,1);
		
		if(isset($data['meta']['expires']) && $data['meta']['expires'] < time()) {
			//cleanup
			$this->delete($key,true);
			return [];
		}
				
		return isset($data['data']) ? $data['data'] : $data;
	}

	/**	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc matching apc user cache behaviour
	 */
	public function update($data, $conditions = false) {
		$key = $conditions;
		return $this->create($data, $key);
	}

	/**
	 *
	 * @param string $key
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 */
	public function delete($key, $force = false) {
		$exists = 0;
		//unlink file

		if($force) {
			var_dump($key);
			$rs = $this->adapter->delete($key);
		} else {

			if ($this->read($key)) {
					$exists = 1;
					$rs = $this->adapter->delete($key);
					if (!$rs) {
							$exists = -1;
					}
			}
		}

		return $exists;
	}

	public function query($query, $parameters = false) {
		return false;
	}
		
}
