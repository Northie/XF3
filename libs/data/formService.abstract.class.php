<?php

namespace libs\data;

abstract class formService implements dataService {
	public function setData($data) {
		$this->data = $data;
	}
}