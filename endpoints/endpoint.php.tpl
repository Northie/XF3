<?php

{{defaultNsComment}} namespace endpoints\{{context}};
{{moduleNsComment}} namespace endpoints\modules\{{module}}\{{context}};

class {{name}} {

	use \endpoints\endpointHelper;
	use \Plugins\helper;

	
	public function __construct($request, $response, $filters) {
		
		$this->notify(__METHOD__);
		
		$this->Init($request, $response, $filters);
		
		$filters = $this->filterInsertBefore('view', 'action');
	}
	
	public function Execute() {
	
	}

}