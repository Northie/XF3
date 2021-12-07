<?php

namespace models\data;

abstract class storage extends data {

	protected $db;
		protected $storage = [];
		protected $storageEngine = [];

	public function __construct($label='default') {
			
			$this->db = \services\data\relational\factory::Build($label);
			$this->setProvider($this->db);
			
			foreach($this->storage as $engine => $fields) {
				$this->storageEngine[$engine] = call_user_func_array(["\\services\\data\\$engine\\factory","Build"], [$label]);
			}
			
			if(!$this->storageEngine['blackhole']) {
				$this->storageEngine['blackhole'] = \services\data\blackhole\factory::Build($label);
			}
			
	}

}
