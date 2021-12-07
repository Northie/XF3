<?php

namespace services\data;

class store {

	private $reader;
	private $writer;
	private $model;
	private $data;
	private $new = [];
	private $updates = [];
	private $deletes = [];

	public function __construct($model) {
		$this->setModel($model);
	}

	public function setModel(\models\data\model $model) {
		$this->model = $model;
		$model->setStore($this);
		return $this;
	}

	public function load($id=null) {
		if($id) {
			$data = $this->reader->read($id);

			if($this->model->getType() == $data['type']) {
				$this->id = $id;
				$this->data[$id] = $data;
			} else {
				
				$frontController = \settings\registry::Load()->get('FrontController');
				$frontController->response->respond(["HTTP/1,1 404 Not Found"]);
				
				if(!$data) {
					throw new \Exception('Resource not found');
				} else {
					throw new \Exception('Resoure ID and Type Mis Match. Resource should be of type '.$this->model->getType().', identifer returns type of '.$data['type']);
				}

			}
		} else {
			if(!$this->data) {
				//if no data, get all, no looping
				$this->data = $this->reader->readType($this->model->getType());
			} else {
				//if data, loop to append
				foreach ($this->reader->readType($this->model->getType()) as $id => $row) {
					$this->data[$id] = $row;
				}
			}
		}
		return $this;
	}

	public function addItem($item) {		
		
		$item = $this->integrate($item);
		
		if(is_a($this->data,'generator')) {
			$this->data = [];
			foreach ($this->reader->readType($this->model->getType()) as $id => $row) {
				$this->data[$id] = $row;
			}
		}
		
		$this->data[$item[$this->model->idParam]] = $item;

		$this->new[] = $item[$this->model->idParam];
		
		return $this;
	}
	
	public function updateItem($item) {
		$this->data[$item[$this->model->idParam]] = $this->integrate($item,$this->data[$item[$this->model->idParam]]);
		
		$this->updates[] = $item[$this->model->idParam];

		return $this;
	}
	
	public function destroyItem($item) {
		$id = $item[$this->model->idParam];
		unset($this->data[$item[$this->model->idParam]]);
		$this->deletes[] = $id;
		return $this;
	}

	public function getAll($generate=false) {
		
		if(is_a($this->data, 'Generator') && $generate) {
			$data = [];
			foreach($this->data as $key => $val) {
				$data[$key] = $val;
			}
			return $data;
		}
		
		return $this->data;
	}

	public function saveAll() {
		$this->saveNew();
		$this->saveUpdates();
		$this->commitDeletes();
	}
	
	public function saveNew() {	
		
		$errors = 0;
		try {
			foreach ($this->new as $id) {
				$this->writer->create($this->data[$id],$id);
				unset($this->new[$id]);
			}
		} catch (\Exception $e) {
			$errors++;
		} finally {
			return !$errors;
		}
	}
	
	public function saveUpdates() {
		$errors = 0;
		try {
			foreach ($this->updates as $id) {				
				$this->writer->update($this->data[$id],$id);
				unset($this->updates[$id]);
			}
		} catch (\Exception $e) {
			$errors++;
		} finally {
			return !$errors;
		}
	}
	
	public function commitDeletes() {
		$errors = 0;
		try {
			foreach ($this->deletes as $id) {				
				$this->writer->delete($id,true);
				unset($this->deletes[$id]);
			}
		} catch (\Exception $e) {
			$errors++;
		} finally {
			return !$errors;
		}
	}

	public function sync() {
		$this->saveAll();
		$this->load();
	}
	
	public function getOne($id=false) {
		$id = $id ? $id : $this->id;
		if(!$id) {
			throw new \Exception('No resource ID supplied');
		}
		return $this->data[$id];
	}

	public function getModel() {
		return $this->model;
	}

	public function setWriter(\services\data\iAdapter $writer) {
		$this->writer = $writer;
		return $this;
	}

	public function setReader(\services\data\iAdapter $reader) {
		$this->reader = $reader;
		return $this;
	}
	
	/**
	 * @return dataservice
	 */
	
	public function getWriter() {
		return $this->writer;
	}
	
	/**
	 * @return dataservice
	 */
	public function getReader() {
		return $this->reader;
	}

	public function integrate($src,$dest=[]) {

		$structure = $this->model->getStructure();
		
		$src['type'] = $src['type'] ? $src['type'] : $this->model->getType();
		
		foreach($structure as $field => $properties) {
			if(!$src[$field] && $properties[2]) {
				$dest[$field] = isset($dest[$field]) ? $dest[$field] : \call_user_func_array($properties[2],[$src]);
			}
			if(isset($src[$field])) {
				$dest[$field] = $src[$field];
			} else {
				if(!$dest[$field]) {
					$dest[$field] = $properties[3] ? $properties[3] : null;
				}
			}
			unset($src[$field]);
		}
		
		foreach($src as $key => $val) {
			if($key == 'additional') {
				foreach($val as $k => $v) {
					$dest['additional'][$k] = $v;
				}
			} else {
				$dest['additional'][$key] = $val;
			}
			
		}		
		//recurse through data, put matching keys from model structire into item data and any other values in an attributes array??
		
		return $dest;
	}
	
}
