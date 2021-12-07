<?php

namespace services\data\memory;

class factory {

	public static function Build($settings) {
				
				$cls = "\\services\\data\\memory\\vendor\\x1\\adapter";

		$o = new $cls($settings);

		return $o;
	}

}
