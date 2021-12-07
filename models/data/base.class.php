<?php

namespace models\data;

class base extends \models\data\model {
    public $structure = [
        'id' => [false, 'string',['\\models\\data\\base','generateId']],
		'type' => [false, 'string']
    ];
	
	public static function generateId() {
		return \utils\Tools::UUID();
	}
	
	
}