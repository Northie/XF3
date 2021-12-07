<?php

namespace settings;

class database {
	use \utils\traits\singleton;
	use _database;
	use _settings;
	
	private function __construct() {
		$this->readSettings();
	}
 
}
