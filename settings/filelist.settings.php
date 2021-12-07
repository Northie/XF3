<?php

namespace settings;

class fileList {
	use \utils\traits\singleton;
	use _settings;
	
	private $classList = [];
	
	private function __construct() {
		$this->includeFileList();
		//$this->parseJsonFileList();
	}
	
	public function includeFileList() {
		
		include \APP_CLASS_LIST;
		$this->classList = $classlist;		
	}
	/*
	public function parseJsonFileList() {
		$json = file_get_contents(\APP_CLASS_LIST_JSON);
		$this->classList = json_decode($json,1);
	}
	//*/
	public function getFileForClass($cls) {
		$cls = trim($cls,'\\');
		return $this->classList[$cls];
		//return isset($this->classList[$cls]) ? $this->classList[$cls] : false;
	}
	
	public function getClassForFile($file) {
		return array_search($file, $this->classList);
	}

	public function getClassList() {
		return $this->classList;
	}
	
	 
}