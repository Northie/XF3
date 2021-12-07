<?php

namespace tokenizer;

class syntaxTree {

	//accept token stream
	//parse into an "Abstract Syntax Tree"

	private $closeAt = -1;
	private $tree;
	private $processed = [];

	public function __construct() {
		//$this->tree = \libs\DoublyLinkedList\factory::Build();
		$this->tree = [];
	}

	public function setStream($stream) {
		$this->stream = $stream;
		return $this;
	}

	/**
	 * 
	 * @param int $idx the origian stream array key
	 * @param array $token parent (opening) token
	 * @return array
	 * @throws \Exception
	 * @desc when an opening token is found, build linear segment of the stream until the close
	 */
	private function buildSegment($idx, $token) {
		$ignore = 0;
		$segment = [];

		$key = key($token);

		$foundClose = false;

		for ($i = $idx + 1; $i < $this->treeSize; $i++) {

			if (key($this->stream[$i]) == $key) {   //nesting in
				$ignore++;
			}
			
			if (key($this->stream[$i]) == $token[$key]['closed_by']) {  //closing token
				if ($ignore == 0) {
					$foundClose = true;
					$segment[$i] = $this->stream[$i];
					break;
				}

				if ($ignore > 0) {  //nesting out
					$ignore--;
				}
			}
			$segment[$i] = $this->stream[$i];
		}

		if (!$foundClose) { //open/close syntax error
			throw new \Exception("Could not close " . $key . " opened at " . $idx . ".");
		}

		$this->closeAt = $i+1;

		return $segment;
	}

	public function toTree() {

		$this->treeSize = count($this->stream);

		$branch = $this->toBranch($this->stream);

		//$this->tree->push(-1, $branch);
		$this->tree = $branch;
	}

	private function toBranch($segment) {
		//$branch = \libs\DoublyLinkedList\factory::Build();
		$branch = [];

		$j = 0;
		foreach ($segment as $idx => $token) {

			$j++;
			if ($j > $this->treeSize) {
				break;  //safety net
			}

			//if ($idx < $this->closeAt) {
			//	continue;
			//}
			if ($this->isProcessed($idx)) {
				continue;
			}

			$key = key($token); //token name
			
			$obj = new \stdClass();
			$obj->token = $key;
			$obj->data = $token[$key];
			$obj->leaf = true;
			$obj->branch = false;
			$obj->original = $token['m'];

			if (isset($token[$key]['is_opener']) && $token[$key]['is_opener']) {
				$nestedSegment = $this->buildSegment($idx, $token);

				$obj->branch = $this->toBranch($nestedSegment);
				
				$obj->branchRaw = $nestedSegment;
				$obj->leaf = false;
			}
			
			//$branch->push($idx, $obj);
			$branch[$idx] = json_decode(json_encode($obj),1);
			$this->processed[$idx] = true;
		}
		
		//$this->tree->push($idx, $branch);
		return $branch;
	}

	public function getTree() {
		return $this->tree;
	}

	private function isProcessed($key) {
		return $this->processed[$key];
	}

}
