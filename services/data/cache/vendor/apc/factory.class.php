<?php

namespace services\data\cache\vendor\apc;

class factory {

	public static function Build() {
		$o = new adapter;
		return $o;
	}

}
