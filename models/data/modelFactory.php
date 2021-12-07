<?php

namespace models\data;

class modelFactory {
	
	/**
	 * 
	 * @param string $name
	 * @return model
	 */
	
	public static function Build($name) {		
		return new $name;
	}
}