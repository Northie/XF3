<?php

namespace services\data;

class factory {

	public static $instance;
	private $adapters = [];

	private function __construct($settings, $type = null) {

	}

	public static function Build($settings, $type = null) {
		
		list($type,$vendor) = explode("/",$settings['type']);
		
		$factoryString = "\\services\\data\\$type\\vendor\\$vendor\\factory";
		
		return $factoryString::Build($settings);
		
	}

	public function getConnection() {
		
	}

}
