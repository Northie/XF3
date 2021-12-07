<?php

namespace views;

class factory {
	 public static function Build($normalisedRequest) {
		  
		$normalisedRequest['endpoint'] = $normalisedRequest['endpoint'] ? $normalisedRequest['endpoint'] : 'index';
		 
		if($normalisedRequest['module']) {
			$targetView = "\\views\\modules\\".$normalisedRequest['module']."\\".$normalisedRequest['context']."\\".$normalisedRequest['endpoint'];
		} else {
			$targetView = "\\views\\".$normalisedRequest['context']."\\".$normalisedRequest['endpoint'];
		}
		
		if(class_exists($targetView)) {
			$view = new $targetView;
		} else {
			$view = new DefaultView;	
		}
		
		\Plugins\EventManager::Load()->observeEvent('onViewCreated',$view,$normalisedRequest);
				
		return $view;
		
	 }
}
