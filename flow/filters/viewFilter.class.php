<?php

namespace flow\filters;

class viewFilter {

	use filter;

	public function in() {

		if (!$this->before(__METHOD__, $this)) {
			$this->out();
		}

		$this->normalisedRequest = $this->request->getNormalisedRequest();

		if (!$this->after(__METHOD__, $this)) {
			return false;
		}
		$this->FFW();
	}

	public function out() {

		if (!$this->before(__METHOD__, $this)) {
			$this->RWD();
		}

		$data = $this->response->getData();

		if (is_null($this->request->ext)) {
			if ($this->request->isAjax()) {
				$renderer = 'JSON';
			} else {
				$renderer = 'HTML';
			}
		} else {
			$renderer = strtoupper($this->request->ext);
		}

		$view = \views\factory::Build($this->normalisedRequest);

		if ($this->normalisedRequest['path']) {
			$view->setPath($this->normalisedRequest['path']);
		}

		$view->setData($data);

		$view->serve();

		if (!$this->after(__METHOD__, $this)) {
			return false;
		}

		$this->RWD();
	}

}
