<?php

namespace libs;

trait factory {

	public static $instance;
	private $registry = [];

	private function __construct() {

	}

	/**
	 *
	 * @param string $key
	 * @param array $list
	 * @return \libs\DoublyLinkedList\linkedList
	 * \libs\DoublyLinkedList\factory::Load('some-list');
	 * \libs\DoublyLinkedList\factory::Load('some-list',array(...));
	 */
	public static function Load($key, $options = false) {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance->get($key, $options);
	}

	public function get($key, $options = false) {
		if (!$this->registry[$key]) {
			$this->registry[$key] = self::Build($options);
		}

		return $this->registry[$key];
	}

}
