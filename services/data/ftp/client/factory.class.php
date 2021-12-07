<?php

namespace services\data\ftp\client;

class factory {

	public static function Build($settings) {

		if(!($settings instanceof \services\data\ftp\ConnectionSettings)) {
			$settings = new \services\data\ftp\ConnectionSettings($settings);
		}

		$o = new adapter($settings);

		\Plugins\EventManager::Load()->ObserveEvent("on".ucfirst(__METHOD__), $o);

		return $o;
	}

}
