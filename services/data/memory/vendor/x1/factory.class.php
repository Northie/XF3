<?php

namespace services\data\memory\vendor\x1;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		\Plugins\EventManager::Load()->ObserveEvent("on".ucfirst(__METHOD__), $o);
		return $o;
	}

}
