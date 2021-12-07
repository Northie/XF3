<?php

namespace modules;

class factory {
	public static function Build($sModule) {

		if(($module = \OS\App::Load()->getModule($sModule))) {
			return $module;
		}

		$sModuleCls = "\\".implode("\\",['modules',$sModule,'init']);
		$oModule = new $sModuleCls;
		$oModule->init();
		
		\settings\registry::Load()->set(['modules',$sModule],$oModule);
		\OS\App::Load()->addModule($oModule);
		return $oModule;
	}
}

