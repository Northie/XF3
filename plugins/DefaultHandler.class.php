<?php

namespace Plugins;

abstract class DefaultHandler implements iDefaultHandler {

	private $admin = false;
	protected $db = false;
	protected $setings = array();
	protected $persist = false;

	public $parent;
	public $options;
	public $event;

	public function Initiate($caller,$options,$event) {
		
		$this->caller = $caller;
		$this->options = $options;
		$this->event = $event;
		
		return $this->Execute();
	}
	
	public function SetDB(PluginDMO $db) {
		$this->db = $db;
	}
	
	public function SetSettings(Settings $s) {
		$this->settings = $s;
	}

	public function getPersistence() {
		return $this->persist;
	}

}