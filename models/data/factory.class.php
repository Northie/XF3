<?php

namespace models\data;

class factory {
	public static function Build($type) {
		
		$class = '\models\data\\'.$type;
		
		$r = new \ReflectionClass($class);

		switch($r->getParentClass()->name) {
			case 'models\data\relational':
				$model = new $class(null,'default');
				break;
			default:
				;
		}
		
		return $model;
	}
}
		