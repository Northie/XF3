<?php

namespace modules;

trait helper {
	/*
	public function getModuleSettings($moduleName=false) {
		
		if(!$moduleName) {
			$class = explode("\\",__CLASS__);
			if($class[0] == 'modules') {
				$moduleName = $class[1];
			}
		}

		$oModule = \settings\registry::Load()->get('modules',$moduleName);
		
		return $oModule->settings ? $oModule->settings : [];
		
	}
	//*/
}