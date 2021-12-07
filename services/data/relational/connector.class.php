<?php

namespace services\data\relational;

/**
 * class XF_PDO
 *
 * makes a pdo connection object
 */
class connector {

	private $db_type;
	private $db_host;
	private $db_name;
	private $db_user;
	private $db_pass;
	private $db_char = 'utf8';
	private $db_conf = [
		\PDO::ATTR_EMULATE_PREPARES=>false	   // important! use actual prepared statements (default: emulate prepared statements)
		, \PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION	// throw exceptions on errors (default: stay silent)
		, \PDO::ATTR_DEFAULT_FETCH_MODE=>\PDO::FETCH_ASSOC	  // fetch associative arrays (default: mixed arrays)
	];
	public $conn;

	/**
	 * get settings, noramalise database credentials attempt to connect
	 */
	public function __construct($options) {
			
		$this->db_type = $options['type'];
		$this->db_host = $options['host'];
		$this->db_name = $options['name'];
		$this->db_user = $options['user'];
		$this->db_pass = $options['pass'];
		$this->db_char = $options['char'] ? $options['char'] : $this->db_char;
		$this->db_conf = $options['conf'] ? $options['conf'] : $this->db_conf;
		
	}

	/**
	 * Attempt to Connect
	 */
	public function Connect() {

		try {
			$this->conn = new \PDO(
				$this->db_type . ":host=" . $this->db_host . ";dbname=" . $this->db_name . ";charset=" . $this->db_char
				, $this->db_user
				, $this->db_pass
				, $this->db_conf
			);
		} catch (\PDOException $e) {
			echo $e->getMessage();
			$this->conn = false;
			die(" in ".__FILE__."=>".__LINE__);
		}

		return $this->conn;
	}

	public function DisConnect() {
		$this->conn = null;
	}

}
