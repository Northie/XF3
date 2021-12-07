<?php

namespace utils;

class XSession {

	public static $instance;
	private $namespace = '_X';
	private $default_namespace = '_X';

	public function __construct($namespace, $prep = false) {
		if ($namespace == '') {
			$namespace = $this->default_namespace;
		}
		$this->namespace = '_'.$namespace;
		if (!is_array($_SESSION['_' . $namespace])) {
			$_SESSION['_' . $namespace] = array();
		}

		if ($prep && !is_array($_SESSION['_' . $namespace][$prep])) {
			$_SESSION['_' . $namespace][$prep] = array();
		}
	}

	public static function Load($ns='') {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($ns);
		}
		return self::$instance;
	}

	public function __set($key, $value) {
		$this->set($key, $value);
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function set($key, $value) {
		$_SESSION[$this->namespace][$key] = $value;
	}

	public function get($key) {
		return $_SESSION[$this->namespace][$key];
	}
	
	public function getSessionId() {
		return session_id();
	}
		
		public function append($key,$value) {
			if(!is_array($_SESSION[$this->namespace][$key])) {
				$_SESSION[$this->namespace][$key] = [];
			}
			
			$_SESSION[$this->namespace][$key][] = $value;
		}
		
		public function destroyNameSpace($ns) {
			$_SESSION[$ns] = [];
		}

}
