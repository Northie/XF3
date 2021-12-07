<?php

namespace flow\filters;

class dispatchFilter {
	use filter;

public function in() {
		
		if(!$this->before(__METHOD__, $this)) {
			$this->out();
		}
		
		/**
		 * Do in / pre-endpoint stuff here
		 */

		
		
		
			/*
			 * 
		**********/
		
		if(!$this->after(__METHOD__, $this)) {
			return false;
		}
		
		$this->FFW();
	}

	public function out() {
		
		if(!$this->before(__METHOD__, $this)) {
			$this->RWD();
		}
		
		/**
		 * Do out / post-endpoint stuff here
		 */		

		
		
		
			/*
			 * 
		**********/
		
		if(!$this->after(__METHOD__, $this)) {
			return false;
		}
		
		$this->RWD();
	}

}