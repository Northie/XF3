<?php

namespace services\data\relational\vendor\mysql;

class factory {

	public static function Build($settings) {
			
			ksort($settings);
			$id = json_encode($settings);
			$a = \settings\registry::Load()->get($id);
			if(!$a) {
				//not ambiguous at all!!!
				list($type,$settings['type']) = explode("/",$settings['type']); //$settings['type'] type will be something like 'relational/mysql' we needed all of it to get here, but only the last part to move forward!!!!
				
				$connector = new \services\data\relational\connector($settings); //relational / pdo uses a common connection mechanism
				//manually connect, legacy, refactor into connector above?

				$accessor = new \services\data\relational\accessor($connector->connect());
				
				//give the vendor specific adapter the pdo connection
				$a = new adapter($accessor);
				\settings\registry::Load()->set($id,$a);
			}
			\Plugins\EventManager::Load()->ObserveEvent("on".ucfirst(__METHOD__), $a);
			return $a;
	}

}
