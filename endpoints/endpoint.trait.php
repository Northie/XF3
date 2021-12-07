<?php
namespace endpoints;

trait endpointHelper {
	protected $data = [];
	protected $appliedFilters = [];
	protected $executable = [];
	
	public function Init($request,$response,$filters) {
		$this->request = $request;
		$this->response = $response;
		$this->filters = $filters;
		
		$this->setExecutable($this,'Execute');

		$nr = $request->getNormalisedRequest();
		
		$this->modelName = '';
		
		if($nr['module']) {
			$this->modelName.=$nr['module']."_";
		}
		
		$this->modelName.=$nr['endpoint'];
				
	}
		
	public function getNamedFilterList() {
		return $this->filters;
	}

		
	public function getFilterOptions($filter) {
		return $this->filterOptions[$filter];
	}


	public function filterInsertBefore($filter, $before,$options=[]) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			$newList[] = $filterName;
			if ($before == $this->filters[$i + 1]) {
				$newList[] = $filter;
			}
		}
				$this->filterOptions[$filter] = $options;
		$this->filters = $newList;
	}
		
	public function filterInsertAfter($filter, $after,$options=[]) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			$newList[] = $filterName;
			if ($after == $this->filters[$i]) {
				$newList[] = $filter;
			}
		}
				$this->filterOptions[$filter] = $options;
		$this->filters = $newList;
	}

	public function removeFilter($filter) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			if($filter == $this->filters[$i]) {
				continue;
			}
			$newList[] = $filterName;
		}
		$this->filters = $newList;
	}

	public function getData() {
		return $this->data;
	}
	
	public function filteredBy($filter) {
		$this->appliedFilters[] = $filter;
	}
	
	public function getAppliedFilters() {
		return $this->appliedFilters;
	}

		
	public function Execute() {
		
		$action = key($this->request->getQuery()['path']);
		
		$action = $action ? $action : 'index';
		
		if(method_exists($this, $action)) {
			if($this->before(__CLASS__."::".$action,$this)) {
				$this->notify(__CLASS__."::".$action,$this);
				$this->{$action}();
			}
			$this->after(__CLASS__."::".$action,$this);
		} else {
			$this->notify('EndpointActionNotFound');
		}
	
	}
		
	public function addData($key,$value) {
		$this->data[$key] = $value;
	}
	
	public function isModule() {
		$r = new \ReflectionObject($this);
		$test = explode("\\",trim($r->getNamespaceName(),"\\"));
		if($test[1] == 'modules') {
			$this->moduleName = $test[2];
			$this->moduleContext = $test[3];
			$this->module = \settings\registry::Load()->get('modules',$this->moduleName);
			return true;
		}
		return false;
	}
	
	public function getModule() {
		if($this->isModule()) {
			return $this->module;
		}
	}

	public function getModuleName() {
		if($this->isModule()) {
			return $this->moduleName;
		}
	}

	public function getModuleSettings() {
		if($this->isModule()) {
			return $this->module->settings;
		}
		return [];
	}	

	public function getExecutable() {
		return $this->executable;
	}

	public function setExecutable($object,$method) {
		$this->executable = [$object,$method];
	}

}