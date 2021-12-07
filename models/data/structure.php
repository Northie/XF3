<?php

namespace models\data;

class structure {

	private static $instance;

	private function __construct($label) {

		//$this->db = \libs\pdo\DB::Load();
		$this->db = \services\data\relational\connections::Load($label);
		
		$this->generate();
	}

	public static function Load($label = 'default') {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($label);
		}
		return self::$instance;
	}

	public function table_exists($table, $retry = true) {
		$e = $this->structure[$table] ? true : false;

		if ($e) {
			return true;
		} else {

			if ($retry) {
				$this->generate();
				return $this->table_exists($table, false);
			} else {
				return false;
			}
		}
	}

	public function field_exists($table, $field, $retry = true) {
		$e = $this->structure[$table][$field] ? true : false;

		if ($e) {
			return true;
		} else {
			if ($retry) {
				$this->generate(true);
				return $this->field_exists($table, $field, false);
			} else {
				return false;
			}
		}
	}

	public function getStructure() {
		return $this->structure;
	}

	private function generate($retry = false) {

		$key = sha1(__CLASS__ . "-" . __METHOD__);

		$data = \utils\Tools::getCache($key);
		//$data = false;

		set_time_limit(0);
		
		if ($retry || !$data) {

			\OS\App::Load()->setTimeLimit(0);
			
			$this->db->Execute("SHOW TABLES")->fetchArray($table_data);

			foreach ($table_data as $tables) {

				foreach ($tables as $table) {

					$this->db->Execute("DESCRIBE `" . $table . "`;")->fetchArray($fields);

					foreach ($fields as $field) {
						$field = $field['Field'];
						//$this->structure[$table][$field] = true;
						$this->structure[$table][$field] = array("table" => $table, "field" => $field);
					}
				}
			}
			
			\OS\App::Load()->resetTimeLimit();

			\utils\Tools::setCache($key, $this->structure, (24 * 60 * 60));
		} else {
			$this->structure = $data;
		}
	}

}
