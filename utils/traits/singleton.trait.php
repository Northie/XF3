<?php
namespace utils\traits;

trait singleton {
	public static $_instance;
	
	public static function Load() {
		if(!isset(self::$_instance)) {
			$cls = __CLASS__;
			self::$_instance = new $cls;
		}
		
		return self::$_instance;
	}
	
}