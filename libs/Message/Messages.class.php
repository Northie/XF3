<?php

namespace libs;

class Messages {

	private $stack = [];
	private $queue = [];
	private $stack_count = 0;
	private $queue_count = 0;

	public function __construct() {
		;
	}

	public function Stack(Message $message, $persist = false) {
		$this->stack[] = $message;
		$this->stack_count++;
	}

	public function Queue(Message $message, $persist = false) {
		$this->queue[] = $message;
		$this->queue_count++;
	}

	public function getStack() {
		return array_reverse($this->stack);
	}

	public function getQueue() {
		return $this->queue;
	}

}