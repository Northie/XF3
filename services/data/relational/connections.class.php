<?php

namespace services\data\relational;

/**
 * manage single instances of XF_DMOs
 */
class connections {

	private static $instance;
	private $settings;
	private $connections = array();

	private function __construct() {

	}

	/**
	 * $rs = DB::Load('zest')->Execute($sql,$args)->returnArray();
	 */
	public static function Load($db = 'default') {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance->getConnection($db);
	}

	public function getConnection($dsn) {
		
		$this->settings = \settings\Database::Load()->get($dsn);
		
		if (!isset($this->connections[$dsn])) {

			$connection = new \services\data\relational\connector($this->settings);

			$link = $connection->Connect();

			$dmo = new \services\data\relational\accessor($link);

			$this->resources[$dsn] = $connection;

			$this->connections[$dsn] = $dmo;
		}

		return $this->connections[$dsn];
	}

	//CleanUp
	//DB::Load('zest')->closeConnections();
	public function closeConnections() {
		foreach ($this->resources as $key=> $val) {
			$val->DisConnect();
		}
	}

}
