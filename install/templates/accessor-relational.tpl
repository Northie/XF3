<?php

namespace models\data\accessor;

class {%table%} extends \models\data\relational {
	use relational_tools;

	private $fields = {%fileds%};
	private $data = [];
	
	public function __construct($data=false,$label=false) {
		if($label) {
			parent::__construct($label);
		} else {
			parent::__construct();
		}
		
		if($data) {
			$data = (array) $data;
			$this->map($data);
		}
	}

}