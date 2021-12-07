<?php

namespace flow\controllers;

class FrontController {
	
	use \Plugins\helper;

	const CONTEXT_TYPE_FOLDER = 1;
	const CONTEXT_TYPE_DOMAIN = 2;
	const CONTEXT_TYPE_SUBDOMAIN = 4;

	public $filters = ['dispatch', 'action'];
	public $request;
	public $response;
	public $filterList;
	public $endpoint = false;

	public function __construct($settings) {
		
		$this->setContextType($settings['contexts']['type']);
		
		$this->setContexts($settings['contexts']['names']);
		
		$this->setDefaultContext($settings['contexts']['default']);
		
		$this->basePath = $settings['contexts']['base'];
		
		$this->request = new \flow\Request;
		$this->response = new \flow\Response;
		
		\settings\registry::Load()->set('FrontController',$this);
		\settings\registry::Load()->set('ControllerType','WEB');
	}

	public function setContexts($contexts = false, $default = false, $active = false) {
		
		$this->before('FrontControllerSetContexts', $this);
		
		$contexts = $contexts ? $contexts : ['default'];

		if (!$contexts) {
			$this->defaultContext = 'default';
			$this->activeContext = 'default';
		}

		foreach ($contexts as $c) {
			$this->contexts[$c] = true;
		}

		if ($default && $this->contexts[$default]) {
			$this->defaultContext = $default;
		}

		if ($active && $this->contexts[$active]) {
			$this->activeContext = $active;
		}
		$this->after('FrontControllerSetContexts', $this);
	}

	public function setContextType($type) {
		$this->contextType = $type;
	}
	
	public function getContextType() {
		return $this->contextType;
	}

	public function setDefaultContext($context) {
		$this->defaultContext = $context;
	}

	//called by the app's html/index.php 
	public final function Init() {
		
		$this->before('FrontControllerInit', $this);
		
		$aCmds = preg_split("/\/|\\\/",str_replace($this->basePath,"",$this->request->REQUEST_URI));		
		
		$cmds = [];
		
		foreach($aCmds as $cmd) {
			if($cmd != '') {
				$cmds[] = trim($cmd,"/\\");
			}
		}
		
		
		switch($this->contextType) {
			case (self::CONTEXT_TYPE_FOLDER):
				if ($this->contexts[$cmds[0]]) {
					$context = array_shift($cmds);
				} else {
					$context = $this->defaultContext;
				}
				break;
			case (self::CONTEXT_TYPE_DOMAIN):
				break;
			case (self::CONTEXT_TYPE_SUBDOMAIN):
				$dm = explode(".",$this->request->SERVER_NAME);
				$context = $dm[0];
				//if subdomain does not exist in contexts list then assume default
				if(!$this->contexts[$context]) {
					$context = $this->defaultContext;
				}
				
				break;
		}
		
		$this->request->normalise(['context' => $context]);

		if ($this->moduleExists($cmds[0])) {

			$this->request->normalise(['module' => array_shift($cmds)]);
		}

		if ($cmds[0] == '') {
			$cmds[0] = 'index';
		}
		
		//$this->request->normalise(['endpoint' => $cmds[0]]);
		$this->request->normalise(['endpoint' => array_shift($cmds)]);

		$this->request->normaliseQuery($cmds);
		
		$this->notify('requestNormalised');
 
		$this->after('FrontControllerInit', $this);
				
	}

	public function makeEndpoint($request = false) {
		if(!$request) {
			$request = $this->request->getNormalisedRequest();
		}

		if($this->moduleExists($request['module'])) {
			
			//$module = \modules\factory::Build($request['module']);
			$module = \OS\App::Load()->getModule($request['module']);
			

			if($module->hasContextEndPoint($request['context'],$request['endpoint'])) {
				$this->createModuleEndpoint($module,$request['context'],$request['endpoint']);
			} else {
				//module exists but without endpoint, might mean that context has endpoint
				$this->createEndpoint($request['context'],$request['module']);
			}
		} else {
			$this->createEndpoint($request['context'],$request['endpoint']);
		}
	}
	
	public function Execute() {
		
		$this->makeEndpoint();
		
		if($this->endpoint) {
		
			$this->filters = $this->endpoint->getNamedFilterList();
			$this->filterList = \libs\DoublyLinkedList\factory::Build();

			foreach ($this->filters as $f) {
					$_f = '\\flow\\filters\\' . $f . 'Filter';

					$filterOptions = $this->endpoint->getFilterOptions($f);

					$filter = new $_f($this->filterList, $this->request, $this->response);
					$filter->setOptions($filterOptions);
					$this->filterList->push($f, $filter);

					$filter->init();

			}

			$start = $this->filterList->getFirstNode(true);
			$this->before('FilterListStart', $this);
			$start->in();
			$this->after('FilterListStart', $this);
		} else {
			$this->response->respond(["HTTP/1.1 404 Not Found"]);
		}
	}

	public function createEndpoint($context,$endpoint) {
		
		if ($endpoint == '') {
			$endpoint = 'index';
		}
		
		$endPointClass = $this->makeEndpointClassString('endpoints',$context,$endpoint);
		
		$this->before('FrontControllerCreateEndpoint', $this,['context'=>$context,'endpoint'=>$endpoint]);
		
		$this->endpoint = \endpoints\factory::Build($endPointClass, $this->request, $this->response, $this->filters);
		$this->request->setEndpoint($this->endpoint);
		
		$this->after('FrontControllerCreateEndpoint', $this);
		
	}
	
	public function makeEndpointClassString($ns,$context,$endpoint) {
	  
		$endPointClass = "\\".$ns."\\".$context."\\".$endpoint;
		
		$endpointFile = \settings\fileList::Load()->getFileForClass($endPointClass);

		if($endpointFile) {
			$this->notify('routeMatched');
		} else {
			$this->notify('routeNotMatched');
		}

		return $endPointClass;
		
	}
	
	public function createModuleEndpoint($module, $context, $endpoint) {
		$r = new \ReflectionObject($module);
		$ns = $r->getNamespaceName();

		if ($endpoint == '') {
			$endpoint = 'index';
		}

		$endPointClass = $this->makeEndpointClassString('endpoints\\'.$ns,$context,$endpoint);
		
		$this->before('FrontControllerCreateModuleEndpoint', $this, ['module'=>$module,'ns'=> $ns,'context'=>$context,'endpoint'=>$endpoint]);
		
		$this->endpoint = \endpoints\factory::Build($endPointClass, $this->request, $this->response, $this->filters);
		$this->request->setEndpoint($this->endpoint);

		$this->after('FrontControllerCreateModuleEndpoint', $this);
		
	}
	
	public function moduleExists($module) {		

		if(in_array($module, $this->request->getModules())) {
			return true;
		}
		
		return false;
	}

	public function getRequest() {
		return $this->request;
	}
	
	public function getResponse() {
		return $this->response;
	}
	
}
