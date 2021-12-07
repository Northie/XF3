<?php

namespace services\data\cache\vendor\file;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		return $o;
	}

}
