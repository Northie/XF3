<?php

namespace flow\filters;

class actionFilter {
	use filter;

	public function in() {

		list($object,$method) = $this->request->getEndpoint()->getExecutable(); //usually defined as [$this,'Execute']

		//around - return false to prevent original from being invoked
		//before - return false to prevent object method from being invoked
		//after - will be invoked after object::method is invoked. 

		if(\Plugins\EventManager::Load()->ObserveEvent("onAround".ucfirst(get_class($object))."::".$method, $object)) {
			if(\Plugins\EventManager::Load()->ObserveEvent("onBefore".ucfirst(get_class($object))."::".$method, $object)) {
				$object->{$method}();
				\Plugins\EventManager::Load()->ObserveEvent("onAfter".ucfirst(get_class($object))."::".$method, $object);
			}
		}

		$this->FFW();
	}

	public function out() {

		$this->response->setData($this->request->getEndpoint()->getData());

		$this->RWD();
	}

}