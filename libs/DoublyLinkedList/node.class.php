<?php

namespace libs\DoublyLinkedList;

class node {

	public $data;
	public $label;
	public $next;
	public $previous;

	function __construct($label) {
		$this->label = $label;
	}

	public function readNode() {
		return $this->label;
	}
	
	//new methods below
	
	public function setLabel($label) {
		$this->label = $label;
	}
	
	public function setPrevious($previous) {
		$this->previous = $previous;
	}
	
	public function setNext($next) {
		$this->next = $next;
	}
	
	public function setData($data) {
		$this->data = $data;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function getPrevious() {
		return $this->previous;
	}
	
	public function getNext() {
		return $this->next;
	}
	
	public function getData() {
		return $this->data;
	}

}
