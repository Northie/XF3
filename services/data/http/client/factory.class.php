<?php

namespace services\data\http\client;

class factory {

	public static function Build() {

		$o = new adapter;
		\Plugins\EventManager::Load()->ObserveEvent("on".ucfirst(__METHOD__), $o);
		return $o;
	}

}
