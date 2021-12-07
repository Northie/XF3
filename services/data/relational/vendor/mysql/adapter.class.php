<?php

namespace services\data\relational\vendor\mysql;

class adapter extends \services\data\adapter {

	public function __construct($db) {
		$this->db = $db;
	}
	
	public function setModelName($model) {
		$this->model = $model;
	}

	public function create($data, $id = false) {
			
			$args = $data;
			
			if($id) {
				$args['id'] = $id;
			}
			
			$values = [];
			foreach($args as $key => $val) {
				$values[] = ":$key";
			}
			
			$fields = '`'.implode("`, `",array_keys($args)).'`';
			
			$sql = "
				INSERT INTO
					`$this->model` (
						".$fields."
					) VALUES (
						".implode(", ",$values)."
					)
				;
			";
                        
			$this->query($sql,$args);
	}

	public function read($args) {
			
			$where = $this->toWhere($args);
			
			$sql = "
				SELECT
					*
				FROM
					`".$this->model."`
				WHERE
					".implode(" AND ",$where['sql'])."
			";
			
			return $this->query($sql, $where['args'])->returnArray();
	}

	public function update($data, $conditions = false) {
			
			$sets = [];
			foreach($data as $key => $val) {
				$sets[] = "`".$key."` = :".$key;
				$args[$key] = $val;
			}
			
			$set = implode(", ",$sets);
			
			$sql = "
				UPDATE
					`".$this->model."`
				SET
					".$set."
			";
			
			if($conditions) {
				$where = $this->toWhere($conditions);
				array_merge($args,$where['args']);
				$sql.= " WHERE ".implode(" AND ",$where['sql']);
			}
			
			
	}

	public function delete($data, $conditions = false) {

	}

	public function query($query, $parameters = false) {
			
				$sql = $query;
				$args = $parameters ? $parameters : [];
			
		return $this->db->Execute($sql, $args);
	}

	public function getAdapter() {
		return $this->db;
	}
		
		private function toWhere($args) {
			$sql = $binds = [];
			
			foreach($args as $key => $val) {
				$sql[] = "`$key` = :$key";
				$binds[$key] = $val;
			}
			
			return['sql'=>$sql,'args'=>$binds];
		}

}
