<?php

namespace services\data;

class storeFactory {

	/**
	 * 
	 * @param string $dsn Data Source Name as defined in settings/database
	 * @param \models\data\model $model
	 * @return \services\data\store
	 */
	
	public static function Build($dsn, \models\data\model $model) {
		
		//create a store instance and set the model
		$store = new \services\data\store($model);
		
		//create a data adapter from the data source name (dsn)
		$dataApdpter = \services\data\factory::Build(
			\settings\database::Load()->get($dsn)
		);
		
		//set the data adapter on the store
		$store->setReader($dataApdpter)->setWriter($dataApdpter);
		
		return $store;
	}
	
}
		
		