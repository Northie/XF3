<?php

namespace models\data;

abstract class data implements iData {

	protected $provider;

	public function setProvider(\services\data\adapter $adapter) {
		$this->provider = $adapter;
	}

}
