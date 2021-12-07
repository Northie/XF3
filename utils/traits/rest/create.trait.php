<?php

namespace utils\traits\rest;

trait create {

	public function Execute() {
		if($this->request->resources[0] > 0) {
			throw new \Exception('error - id supplied. ID not necessary. Successful response will contain an id');
		}
		
		if(!$this->model && $this->request->modules[0]) {
			$this->model = \libs\models\Resource::Load($this->request->modules[0]);
		}
		
		$this->data['id'] = $this->model->create($this->request->getData())->returnLastInsertID();
		
		\Plugins\EventManager::Load()->ObserveEvent("onAfterRestCreate",$this);
	}
}
