<?php

namespace services\data\cache;

class factory {

	public static function Build($settings) {
				
				$cls = "\\services\\data\\cache\\vendor\\" . $settings['type'] . "\\adapter";

		$o = new $cls($settings);

		return $o;
	}

}
