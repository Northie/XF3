<?php

{{defaultNsComment}} namespace views\{{context}};
{{moduleNsComment}} namespace views\modules\{{module}}\{{context}};

class {{name}}
	implements \views\iView {
	
	use \views\view;
		 
	public function serve()
	{
		include dirname(__FILE__).'/../templates/{{name}}/index.phtml';
		
	}
	 
}

