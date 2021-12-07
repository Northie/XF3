<?php

namespace utils\traits\rest;

trait destroy {

	public function Execute() {
		if(!$this->request->resources[0]) {
			throw new \Exception('error - id not supplied. ID is necessary for destruction');
		}
		
		if(!$this->model && $this->request->modules[0]) {
			$this->model = \libs\models\Resource::Load($this->request->modules[0]);
		}
		
		$this->data['meta']['count'] = $this->model->destroy($this->request->resources[0])->getData('count');
		
		\Plugins\EventManager::Load()->ObserveEvent("onAfterRestDestroy",$this);
		
	}
}
