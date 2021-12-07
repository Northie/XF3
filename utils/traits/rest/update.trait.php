<?php

namespace utils\traits\rest;

trait update {

	public function Execute() {
		if(!$this->request->resources[0]) {
			throw new \Exception('error - id not supplied. ID is necessary for update');
		}
		
		if(!$this->model && $this->request->modules[0]) {
			$this->model = \libs\models\Resource::Load($this->request->modules[0]);
		}
		
		$this->data['meta']['count'] = $this->model->update($this->request->resources[0])->set($this->request->getData())->getData('count');
		
		\Plugins\EventManager::Load()->ObserveEvent("onAfterRestUpdate",$this);

	}
}
