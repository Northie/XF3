<?php

namespace flow;

class Request {
	use \Plugins\helper;

	private $requestKey = '';
	private $ajax = false;
	private $https = false;
	private $dynamic = [];
	private $endpoint = false;

	private $modules = [];
	private $query = [];
	
	private $normalisedRequest = [
		'context'=>false,
		'endpoint'=>false,
		'module'=>false,
		'path'=>false,
	];
		


	public function __construct($server = false) {

		if (!$this->before('RequestConstruct', $this)) {
			return false;
		}
				
		if (!$server) {
			//if no $_SERVER then throw exception? should come from cli.php?
			$server = $_SERVER;
		}

		foreach ($server as $key=> $val) {
			$this->__set($key, $val);
		}
		
		$this->__set('server',$server);

		$this->requestKey = uniqid();

		$this->HTTP_SCHEME = 'http://';

		if ($this->HTTPS == 'on') {
			$this->HTTP_SCHEME = 'https://';
			$this->setIsHTTPS();
		}


		$url = $this->HTTP_SCHEME . $this->HTTP_HOST . $this->REQUEST_URI;
		
		$this->__set('URI',$url);

		$request = parse_url($url);

		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->ajax = true;
			$this->notify('DetectingAjax', $this);
		}

		//set requested file extension
		$ext = explode(".", $server['REQUEST_URI']);
		if (count($ext) > 1) {
			$this->ext = array_pop($ext);
		}
				
				if($this->REQUEST_METHOD == 'POST') {
					$this->POST_DATA = $_POST;
					$this->notify('postDataReceived',$this);
				}
				if(!!$_COOKIE) {
					$this->COOKIE_DATA = $_COOKIE;
					$this->notify('cookieDataReceived',$this);
				}

		$this->after('RequestConstruct', $this);
	}


	public function setActiveContext($context) {			
		$this->context = $context;
	}

	public function isHTTPS() {

		return $this->https;
	}

	public function setIsHTTPS($set = true) {
		$this->https = $set;
		$this->notify('SetIsHttps', $this);
	}

	public function isAjax() {
		return $this->ajax;
	}

	public function setIsAjax($set = true) {
		$this->ajax = $set;
	}

	public function __get($key) {
		return $this->dynamic[$key];
	}

	public function __set($key, $val) {
		$this->dynamic[$key] = $val;
	}

	public function setEndpoint($endpoint) {


		\settings\registry::Load()->set(['REQUEST', 'CONTEXT'], $this->context);
		\settings\registry::Load()->set(['REQUEST', 'ENDPOINT'], $endpoint);
		
		$this->ENDPOINT = $endpoint;
		$this->endpoint = $endpoint;
	}


	public function getEndpoint() {
		return $this->endpoint;
	}
	
	public function getRequestType() {
		return $this->requestType;
	}
	
	private function setRequestType($verb) {
		$this->requestType = strtoupper($verb);
	}

		
	public function addModule($module) {
		//$this->modules[] = $module;
	}
	
	public function getModules() {
		return array_keys(\OS\App::load()->getModules());
	}
	
	public function getNormalisedRequest() {
		return $this->normalisedRequest;
	}
		
	public function normalise($array) {
		foreach($array as $key => $val) {
			if(isset($this->normalisedRequest[$key])) {
				if($key == 'endpoint') {
					if(strpos($val,'?') > -1) {
						list($endpoint,$query) = explode('?',$val);
						$val = $endpoint;
					}
					if(strpos($val,'.') > -1) {
						list($endpoint,$view) = explode('.',$val);
						$val = $endpoint;
					}						
					if($query) {
						$this->normalisedRequest['query_string'] = $query;
					}
					if($view) {
						$this->normalisedRequest['view_format'] = $view;
					}
				}
				$this->normalisedRequest[$key] = $val;
			}
		}
	}
		
	public function normaliseQuery($arr) {
		
		list($path,$query) = explode("?",array_pop($arr));
		
		parse_str($query, $get);
		
		if(!$get) {
			$get = [];
		}
		
		$arr[] = $path;
		$pQuery = [];
		for($i=0; $i<count($arr);$i+=2) {
			$pQuery[$arr[$i]] = $arr[$i+1];
		}
		
		$this->setQuery(['path'=>$pQuery,'queryString'=>$get]);
		
	}
	
	public function setQuery($query) {
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
	
	public function getCombinedQuery() {
		return array_merge($this->query['queryString'],$this->query['path']);
	}
	
	public function addPathQuery($newPathQuery) {
		for($i=0;$i<count($newPathQuery);$i+=2) {
			$this->query['path'][$newPathQuery[$i]] = $newPathQuery[$i+1];
		}
	}
	
	public function getRequest() {
		return $this->dynamic;
	}

}