<?php

namespace libs\DoublyLinkedList;

class factory extends \libs\_factory {

	use \libs\factory;

	public static function Build($options = false) {
		$o = new linkedList();

		if (is_array($options)) {
			$list = $options['list'] ? $options['list'] : $options;
			if (\utils\validators::is_assoc($list)) {
				foreach ($list as $key=> $val) {
					$o->push($key, $val);
				}
			} else {
				foreach ($list as $val) {
					$o->push($val);
				}
			}
		}

		return $o;
	}

}
