<?php

namespace services\data\blackhole;

class factory {

	public static function Build($settings) {
				
				$cls = "\\services\\data\\blackhole\\vendor\\x1\\adapter";

		$o = new $cls($settings);

		return $o;
	}

}
