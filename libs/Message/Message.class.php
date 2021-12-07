<?php

namespace libs;

class Message {

	public $message;
	public $type;

	const ERROR = 1;
	const WARN = 2;
	const INFO = 4;
	const SUCCESS = 8;

	public function __construct($message, $type = false) {
		$this->message = $message;
		$this->type = $type ? $type : self::INFO;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function geType() {
		return $this->type;
	}

	public function __sleep() {
		return ['message', 'type'];
	}

	public function __wakeup() {
		;
	}

	public function __toString() {
		return (string) $this->message;
	}

}