<?php

namespace services\data\cache\vendor\couchbase;

class factory {

	public static function Build($settings) {
			ksort($settings);
			$id = json_encode($settings);
			$a = \settings\registry::Load()->get($id);
			if(!$a) {
				$a = new adapter($settings);
				\settings\registry::Load()->set($id,$a);
			}
			\Plugins\EventManager::Load()->ObserveEvent("on".ucfirst(__METHOD__), $a);
			return $a;
	}

}
