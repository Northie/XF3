<?php

namespace services\data\cache\vendor\apc;

class adapter extends services\data\adapter {

	public function __construct() {
		if (ini_get('apc.enabled') != 1) {
			throw new \services\data\cacheException('APC Not enabled');
		}
	}

	public function create($data, $key) {
		return apc_store($key, $data);
	}

	public function read($key) {
		return apc_fetch($key);
	}

	/**
	 * APC Update - updates the cache with new data
	 *
	 * This function will NOT create a new cache entry if the key does not exist. Use create instead
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc
	 */
	public function update($data, $conditions) {
		$exists = 0;
		$key = $conditions;

		if ($this->read($key)) {
			$exists = 1;
			$rs = $this->create($data, $key);
			if (!$rs) {
				$exists = -1;
			}
		}


		return $exists;
	}

	/**
	 *
	 * @param string $key
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 */
	public function delete($key) {
		$exists = 0;

		if ($this->read($key)) {
			$exists = 1;
			$rs = apc_delete($key, $data);
			if (!$rs) {
				$exists = -1;
			}
		}

		return $exists;
	}

}
