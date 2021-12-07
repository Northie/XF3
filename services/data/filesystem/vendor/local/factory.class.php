<?php

namespace services\data\filesystem\vendor\local;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		\Plugins\EventManager::Load()->ObserveEvent("on".ucfirst(__METHOD__), $o);
		return $o;
	}

}
