<?php
namespace state;

class manager {
	
	const INIT = 0;
	const START = 1;
	const IN_PROGRESS = 2;
	const COMPLETE = 4;
	
	private static $instance;

	private $ids = [];
	private $id;
	
	private function __construct($type,$id=false) {
		$state = new \utils\XSession('state','items');

		if(!$id) {
			$id = \utils\Tools::UUID();
		}
		
		$this->id = $id;
		$this->ids[$id]++;
		
		if(!isset($state->items[$id])) {
			$state->items[$id] = [
				'type'=> $type,
				'status' => self::INIT
			];
		}
	}
	
	/**
	 * 
	 * @param string $type
	 * @param string $id
	 * @return \state\manager
	 */
	
	public static function Load($type,$id=false) {
		if(!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($type,$id);
		}
		return self::$instance;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getType() {
		$state = new \utils\XSession('state','items');
		return $state->items[$this->id]['type'];
	}
	
	public function getStatus() {
		$state = new \utils\XSession('state','items');
		return $state->items[$this->id]['status'];
	}

	public function setStatus($status) {
		$state = new \utils\XSession('state','items');
		$state->items[$this->id]['status'] = $status;
		return $this;
	}
	
	public function get() {
		$state = new \utils\XSession('state','items');
		return $state->items[$this->id];
	}
	
	public function set($data) {
		$state = new \utils\XSession('state','items');
		$state->items[$this->id]['data'] = $data;
		return $this;
	}
}