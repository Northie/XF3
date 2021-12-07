<?php

namespace flow\filters;

class trackingFilter {
	use filter;

	public function in() {
		
		if(!$this->before(__METHOD__, $this)) {
			$this->out();
		}
		
		$this->session = new \utils\XSession('TRACKING');
		
		if(!$this->session->get('INITIAL_SESSION_ID')) {
			$session->set('INITIAL_SESSION_ID', $this->session->getSessionId());
		}

		if(!$this->after(__METHOD__, $this)) {
			return false;
		}
		$this->FFW();
	}

	public function out() {
		
		if(!$this->before(__METHOD__, $this)) {
			$this->RWD();
		}
		
		$tracked = [
			'session_start_id'=>$this->session->get('INITIAL_SESSION_ID'),
			'url'=>$this->request->URI,
			'verb'=>$this->request->REQUEST_METHOD,
			'ajax'=>$this->request->isAjax(),
			'data'=>$_{$this->request->REQUEST_METHOD},
			'user_id'=>0
		];
		
		\models\data\factory::build('tracker')->save($tracked);
		
		if(!$this->after(__METHOD__, $this)) {
			return false;
		}
		
		$this->RWD();
	}

}