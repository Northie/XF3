<?php

namespace libs\locale;

class locale {
	public function __construct($locale=FALSE) {
		if(!$locale) {
			//get default
			$locale = 'en_GB';
		}
		
		//load translations
	}
	
	/**
	 * 
	 * @param type $value
	 * $l = new Locale;
	 * $l->format($ts)->with('DATE');
	 */
	
	public function format ($value) {
		$this->formatValue = $value;
		return $this;
	}
	
	public function with($type) {
		$this->formatType = $type;
		//check we have a record to match $type
		//find callback formatter
		//call callback with value $this->formatValue
		//return output
		//else
		return $this->formatValue;
	}
	
	public function symbol($symbol) {
		return $this->symbols[$symbol];
	}
	
	public function token($token) {
		return $this->tokens[$token];
	}
	
}

