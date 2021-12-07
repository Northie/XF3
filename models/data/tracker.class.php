<?php

namespace models\data;

class tracker extends storage {
	use relational_tools {
		relational_tools::mapToDb as parentMapToDb;
	}

	protected $fields = ['id', 'session_start_id','url', 'verb','ajax','data','user_id'];
	protected $data = [];
	
	public function __construct($data=false,$label=false) {
		
		$this->setName(trim(str_replace(__NAMESPACE__, "", __CLASS__),"\\"));
		
		if($label) {
			parent::__construct($label);
		} else {
			parent::__construct();
		}
		
		if($data) {
			$data = (array) $data;
			$this->mapToDb($data);
		}
	}
	
	
	public function mapToDb($data) {
				
		$data['data'] = json_encode($data['password'],JSON_PRETTY_PRINT);
		
		return $this->parentMapToDb($data);
		
	}
}