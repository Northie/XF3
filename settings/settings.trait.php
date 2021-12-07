<?php

namespace settings;

trait _settings {

	public function set($key, $value) {
		if (is_array($key)) {
			if(isset($key[1])) {
				$this->settings[$key[0]][$key[1]] = $value;
			} else {
				$this->settings[$key[0]] = $value;
			}
		} else {
			$this->settings[$key] = $value;
		}
	}

	public function get($key1 = false, $key2 = false) {

		if(is_array($key1)) {
			$aKeys = $key1;
			if(isset($aKeys[0])) {
				$key1 = $aKeys[0];	
			}
			if(isset($aKeys[1])) {
				$key2 = $aKeys[1];	
			}

		}
			
		if ($key1) {
			if ($key2) {
				return $this->settings[$key1][$key2];
			}
			
			return isset($this->settings[$key1]) ? $this->settings[$key1] : null ;
		}

		return $this->settings;
	}

}
