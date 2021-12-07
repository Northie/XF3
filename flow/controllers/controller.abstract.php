<?php

namespace flow;

abstract class controller {

	//public $filters = ['default', 'domain', 'security', 'data', 'view', 'action'];
	public $filters = ['test', 'action'];
	public $request;
	public $response;
	protected $filterList;

	public function __construct() {
		$this->request = new \flow\Request;
		$this->response = new \flow\Response;
	}
		
		/*
	public final function Init() {

		$this->filters = $this->request->getEndpoint()->getNamedFilterList();

		$this->filterList = \libs\DoublyLinkedList\factory::Build();

		foreach ($this->filters as $f) {
			$_f = '\\flow\\filters\\' . $f . 'Filter';
			$filter = new $_f($this->filterList, $this->request, $this->response);
			$this->filterList->push($f, $filter);
			$filter->init();
		}
	}
		//*/
	public function Execute() {
		$start = $this->filterList->getFirstNode(true);
		$start->in();
	}

}