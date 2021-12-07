<?php

namespace utils\traits\rest;

trait read {

	public function Execute() {
		
		if(!$this->model  && $this->request->modules[0]) {
			$this->model = \libs\models\Resource::Load($this->request->modules[0]);
		}
		
		if($_GET['fields'][$this->request->modules[0]]) {
			 $this->model->setFields($_GET['fields'][$this->request->modules[0]]);
		}
		
		if($this->request->resources[0]) {
			
			if(is_numeric($this->request->resources[0])) {
			
				$where = [
					"id"=> (int) $this->request->resources[0]
				];
				
				$this->data[$this->model->resource] = $this->model->read($where)->getOne();
			}
			//*
			if($this->request->resources[0] == 'search') {
				$this->data[$this->model->resource] = $this->model->search('OR')->getMany();
			}
			
			if($this->request->resources[0] == 'filter') {
				$this->data[$this->model->resource] = $this->model->search('AND')->getMany();
			}
			//*/
			
			
		} else {
			$this->data[$this->model->resource] = $this->model->read()->getMany();
		}
		
		//*
		if($this->request->modules[1]) {
			for($i=1;$i<count($this->request->modules);$i++) {
				$context = $this->request->modules[$i-1];
				$model = \libs\models\Resource::Load($this->request->modules[$i]);
				
				if($this->request->resources[$i] == 'search'  || $this->request->resources[$i] == 'filter') {
					
					$mode = 'OR';
					
					if($this->request->resources[$i] == 'filter') {
						$mode = 'AND';
					}
						
					$this->data[$this->request->modules[$i]] = $model->forResource($context,$this->request->modules[$i],$this->request->resources[$i-1],true,$mode);
					
				} else {
					if(method_exists($model,"for".$context)) {
						$this->data[$this->request->modules[$i]] = call_user_func_array([$model,"for".$context], [$this->request->resources[$i-1]]);	
					} else {
						$this->data[$this->request->modules[$i]] = $model->forResource($context,$this->request->modules[$i],$this->request->resources[$i-1]);
					}
				}
			}
		}
		//*/
		
		\Plugins\EventManager::Load()->ObserveEvent("onAfterRestRead",$this);
		
	}
}