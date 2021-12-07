<?php

namespace services\data;

class cacheException extends \Exception {

	/*
	public function __construct($message = "", $code = 0, $previous) {
		parent::__construct($message, $code, $previous);
	}
	//*/
	
	public function __construct($message = "", $code = 0) {
		parent::__construct($message, $code);
	}
}
