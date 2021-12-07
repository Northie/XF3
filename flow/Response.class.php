<?php

namespace flow;

class Response {
	use \Plugins\helper;

	public function __construct() {

		if (!$this->before('ResponseConstruct', $this)) {
			return false;
		}
		$this->after('ResponseConstruct', $this);
	}

	public function setData($data) {
		if (!\Plugins\EventManager::Load()->ObserveEvent('onBeforeResponseSetData', $this)) {
			return false;
		}
		$this->data = $data;
		$this->after('ResponseSetData', $this);
	}
		
			public function addData($key,$data) {
		if (!\Plugins\EventManager::Load()->ObserveEvent('onBeforeResponseAddData', $this)) {
			return false;
		}
		$this->data[$key] = $data;
		$this->after('ResponseAddData', $this);
	}

	public function getData() {
		if (!$this->before('ResponseGetData', $this)) {
			return false;
		}
		return $this->data;
	}

	public function getResponseFormat() {
		if (!$this->before('ResponseGetResponseFormat', $this)) {
			return false;
		}
		return $this->format;
	}

	public function setResponseFormat($format) {
		if (!$this->before('ResponseSetResponseFormat', $this)) {
			return false;
		}

		$this->format = $format;

		$this->after('ResponseSetResponseFormat', $this);
	}

		
	public function respond($headers) {
		foreach($headers as $key => $val) {
			if(is_null($key) || is_int($key)) {
				header($val);
			} else {
				$header = "$key: $val";
				header($header);
			}
		}
	}

}