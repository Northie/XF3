<?php
namespace libs\locale\helpers;

class dateHelper {
	
	private $ts;
	private $format;
	
	public function __construct($format,$value) {
		$this->ts = strtotime($value);
		$this->format = $format;
	}
	 
	public function Execute() {
		 return date($this->format,$this->ts);
	}
	 
}