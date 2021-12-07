<?php

namespace models\data;

trait relational_tools {

	protected $_class = '';
	
	public function describe() {
		$this->_class = trim(str_replace(__NAMESPACE__,"", __CLASS__),"\\");
		
		return [$this->_class=>$this->fields];
	}

	public function getResourceTypes() {
		return $this->resources;
	}

	public function map($data) {
		foreach ($this->fields as $key => $val) {
			$this->data[$key] = $data[$key];
		}
	}

	public function get() {
		return $this->data;
	}

	public function getById($id) {
			return $this->db->read(['id'=>$id]);
	}

	public function __get($field) {			   
		if ($this->fields[$field]) {
			if (isset($this->data[$field])) {
				return $this->data[$field];
			}
			return null;
		}

		throw new RelationalExeption('Attempt to get non-existant field, '.$field.', from '.implode(", ",array_keys($this->fields)).' or '.implode(", ",array_keys($this->data)));
	}

	public function __set($field, $value) {
		if (isset($this->fields[$field])) {
			$this->data[$field] = $value;
			return true;
		}

		throw new RelationalExeption('Attempt to set non-existant field');
	}

	public function __call($name, $args = false)  {
			
		$test = ['set'=>1, 'get'=>1];

		$mode = $name[0] . $name[1] . $name[2];
		$opperator = strtolower($name[3] . $name[4]);
				
		if ($test[$mode] && $opperator != 'by') {
			return $this->setGet($mode, substr($name, 3), $args);
		}

		if ($mode == 'get' && $opperator == 'by') {
			$field = substr($name, 5);
						$field = \utils\Tools::camel_to_field($field);

			if ($this->fields[$field]) {
				//$this->map($this->db->read([$field=>$args[0]]));
								return $this->db->read([$field=>$args[0]]);
			}
		}
	}

	private function setGet($mode, $name, $args) {
		if ($mode == 'get') {
			if (isset($this->fields[$name])) {
				$this->data[$name];
			}

			if (isset($this->resources[$name])) {
				if (isset($this->data['id'])) {
					$resources = new $this->resources[$name]['class'];
					$resources->getByUserId($this->data['id']);
				}
			}
		}

		if ($mode == 'set') {
			if (isset($this->fields[$name])) {
				$this->data[$name] = (string) $args;
				return true;
			}

			return false;
		}
	}

}
