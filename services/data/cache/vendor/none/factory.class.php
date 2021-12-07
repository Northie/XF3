<?php

namespace services\data\cache\vendor\none;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		return $o;
	}

}
