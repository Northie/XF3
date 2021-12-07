<?php

namespace libs\DoublyLinkedList;

class linkedList implements \Iterator { //which implements Traversable

	private $firstNode;
	private $lastNode;
	private $count;
	private $index = [];
	private $position = '';
	private $temp = [];

	function __construct() {
		$this->firstNode = NULL;
		$this->lastNode = NULL;
		$this->count = 0;
	}

	/**
	 *
	 * @param $key
	 * @param mixed $value
	 * @return boolean
	 * @desc checks to see if $key already exists in index and updates it if it is
	 */
	private function update($key, $value) {
		if ($this->index[$key]) {
			$this->index[$key]->setData($value);
			return true;
		}
		return false;
	}

	/**
	 * @desc is list empty? yes => true; no => false;
	 * @return boolean
	 */
	public function isEmpty() {
		return ($this->firstNode == NULL);
	}

	/**
	 *
	 * @param $key
	 * @param mixed $value
	 * @return type
	 */
	public function insertFirst($key, $value = false) {

		if ($this->update($key, $value)) {
			//$this->move($key)->toBefore($this->firstNode->label);
			return;
		}

		$newLink = new node($key);

		$this->index[$key] = $newLink;
		//$this->values[$key] = $value;
		$newLink->setData($value);

		if ($this->isEmpty()) {
			$this->lastNode = $newLink;
		} else {
			$this->firstNode->previous = $newLink;
		}

		$newLink->next = $this->firstNode;
		$this->firstNode = $newLink;

		$this->count++;
	}

	/**
	 *
	 * @param $key
	 * @param mixed $value
	 * @desc alias of insertLast
	 */
	public function push($key, $value) {
		$this->insertLast($key, $value);
	}

	/**
	 * @desc appends the node to the
	 * @param $key
	 * @param type $value
	 * @return void
	 */
	public function insertLast($key, $value = false) {

		//think about this - what should the behavior be? should this use $this->move($key)->toAfter($this->lastNode->label)?
		if ($this->update($key, $value)) {
			//$this->move($key)->toAfter($this->lastNode->label);
			return;
		}


		$newLink = new node($key);

		if ($value instanceof \libs\DoublyLinkedList\linkedList) {


			if ($value->getFirstNode() instanceof \libs\DoublyLinkedList\node) {
				$value->getFirstNode()->setPrevious($newLink);
			}
			if ($value->getLastNode() instanceof \libs\DoublyLinkedList\node) {
				$value->getFirstNode()->setPrevious($newLink);
			}
		}

		$this->index[$key] = $newLink;
		//$this->values[$key] = $value;

		$newLink->setData($value);

		if ($this->isEmpty()) {
			$this->firstNode = $newLink;
		} else {
			$this->lastNode->next = $newLink;
		}

		$newLink->previous = $this->lastNode;
		$this->lastNode = $newLink;
		$this->count++;
	}

	public function insertAfter($search, $key, $value = false) {

		//think about this - what should the behavior be?
		if ($this->update($key, $value)) {
			return;
		}

		$current = $this->index[$search];

		if ($current == NULL) {
			return false;
		}

		$newLink = new node($key);
		$newLink->setData($value);


		$this->index[$key] = $newLink;
		//$this->values[$key] = $value;

		if ($current == $this->lastNode) {
			$newLink->next = NULL;
			$this->lastNode = $newLink;
		} else {
			$newLink->next = $current->next;
			$current->next->previous = $newLink;
		}

		$newLink->previous = $current;
		$current->next = $newLink;
		$this->count++;

		return true;
	}

	public function insertBefore($search, $key, $value = false) {

		//think about this - what should the behavior be?
		if ($this->update($key, $value)) {
			return;
		}

		$current = $this->index[$search];

		if ($current == NULL) {
			return false;
		}
		$newLink = new node($key);
		$newLink->setData($value);

		$this->index[$key] = $newLink;
		//$this->values[$key] = $value;

		if ($current == $this->firstNode) {
			$newLink->next = NULL;
			$this->firstNode = $newLink;
		} else {
			$newLink->previous = $current->previous;
			$current->previous->next = $newLink;
		}

		$newLink->next = $current;
		$current->previous = $newLink;
		$this->count++;

		return true;
	}

	public function deleteFirstNode() {

		$temp = $this->firstNode;
		$save = clone $temp;

		unset($this->index[$this->firstNode->label]);

		if ($this->firstNode->next == NULL) {
			$this->lastNode = NULL;
		} else {
			$this->firstNode->next->previous = NULL;
		}

		$this->firstNode = $this->firstNode->next;
		$this->count--;

		return $save;
	}

	public function deleteLastNode() {

		$temp = $this->lastNode;
		$save = clone $temp;

		unset($this->index[$this->lastNode->label]);

		if ($this->firstNode->next == NULL) {
			$this->firstNode = NULL;
		} else {
			$this->lastNode->previous->next = NULL;
		}

		$this->lastNode = $this->lastNode->previous;
		$this->count--;
		return $save;
	}

	public function deleteNode($search) {

		$current = $this->index[$search];
		$save = clone $current;

		if ($current == $this->firstNode) {
			$this->firstNode = $current->next;
		} else {
			$current->previous->next = $current->next;
		}

		if ($current == $this->lastNode) {
			$this->lastNode = $current->previous;
		} else {
			$current->next->previous = $current->previous;
		}

		unset($this->index[$search]);

		$this->count--;
		return $save;
	}

	public function exportForward($withValues = false) {

		$current = $this->firstNode;

		$a = array();

		while ($current != NULL) {
			if ($withValues) {
				//$a[$current->getLabel()] = $this->values[$current->getLabel()];
				$a[$current->getLabel()] = $current->getData();
			} else {
				$a[] = $current->getLabel();
			}
			$current = $current->next;
		}

		return $a;
	}

	public function exportBackward($withValues = false) {

		$current = $this->lastNode;

		$a = array();

		while ($current != NULL) {
			if ($withValues) {
				//$a[$current->getLabel()] = $this->values[$current->getLabel()];
				$a[$current->getLabel()] = $current->getData();
			} else {
				$a[] = $current->getLabel();
			}
			$current = $current->previous;
		}

		return $a;
	}

	public function total() {
		return $this->count;
	}

	public function getNode($search) {
		//$current = $this->index[$search];
		//return $current;
		return $this->index[$search];
	}

	public function getNodeValue($search) {
		//return $this->values[$search];
		if ($this->index[$search]) {
			return $this->index[$search]->getData();
		}
		return false;
	}

	public function getLastNode($value = false) {
		if ($value) {
			//return $this->values[$this->lastNode->label];
			return $this->lastNode->getData();
		}
		return $this->lastNode;
	}

	public function getFirstNode($value = false) {

		if ($value) {
			//return $this->values[$this->firstNode->label];
			return $this->firstNode->getData();
		}

		return $this->firstNode;
	}

	//methods to implemnet Iterator and Traversable:

	public function current() {
		if (!$this->position) {
			$this->position = $this->firstNode->getLabel();
		}
		return $this->index[$this->position];
	}

	public function key() {
		if (!$this->position) {
			$this->position = $this->firstNode->getLabel();
		}
		return $this->position;
	}

	public function next() {
		if (!$this->position) {
			$this->position = $this->firstNode->getLabel();
		}

		$return = $this->index[$this->position]->getNext();

				if($return) {
					$this->position = $return->getLabel();
				}

		return $return;
	}

	public function rewind() {
		$this->position = $this->firstNode->getLabel();
	}

	public function valid() {
		return isset($this->index[$this->position]);
	}

	/**
	 *
	 * @param $key
	 * @return \libs\DoublyLinkedList\linkedList
	 *
	 * $list->move('foo')->toBefore('bar');
	 * $list->move('bar')->toAfter('baz');
	 *
	 */
	public function move($key) {
		//get node value and key - hold in temp
		//delete node
		if (isset($this->index[$key])) {
			$this->temp = ['key'=>$key, 'value'=>$this->deleteNode($key)->getData()];
		} else {
			//error
		}
		return $this;
	}

	public function toBefore($key) {

		//check this is called by self?
		//forward on to insertBefore
		$this->insertBefore($key, $this->temp['key'], $this->temp['value']);
		$this->temp = [];
	}

	public function toAfter($key) {

		//check this is called by self?
		//forward on to insertAfter
		$this->insertAfter($key, $this->temp['key'], $this->temp['value']);
		$this->temp = [];
	}

		public function replace($oldKey,$newKey,$newValue,$label=false) {
			if($oldKey ==  $newKey) {
				//get old key and replace node value
				$this->update($oldKey,$newValue);
			} else {
				//look for old key
				$oldNode = $this->index[$oldKey];
				if(!$label) {
					$label = $oldNode->getLabel();
				}
				
				$newNode = new node($label);
				
				$newNode->setData($newValue);
				$newNode->setNext($oldNode->getNext());
				$newNode->setPrevious($oldNode($get->Previous));
				
				$this->update($newKey,$newValue);
				
			}
		}

}