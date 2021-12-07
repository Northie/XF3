<?php

namespace settings;

/**
 * \settings\registry::Load()->set('REALM','DEFAULT');
 */
class registry {

	use \utils\traits\singleton;
	use _settings;

	private $settings = [];

	private function __construct() {
		
	}

}
