<?php

namespace libs\DoublyLinkedList;

//take out non-list semantic methods from linked list and put in here

class manager {

	public function __construct(linkedList $list) {
		foreach ($list as $key=> $val) {
			$this->index[$key] = $val;
		}
	}

}