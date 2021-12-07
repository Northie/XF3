<?php

namespace services\data\relational;

/**
 * DMO
 *
 * Given a connection to a database (PDO, XF_PDO), manage SQL execution returning data where applicable
 * 'Return' values are passed by reference and all public methods return an instance of it's self for chainability
 *
 * @author Chris Rutledge
 * @package Libs
 * @see DB
 */
class accessor {

	public $conn;
	private $SQL;
	private $RSarray;
	private $RSrow;
	private $RSval;

	public function __construct($link) {
		$this->conn = $link;
	}

	public function beginTransaction() {
		$this->conn->beginTransaction();
	}

	public function commit() {
		$this->conn->commit();
	}

	public function rollBack() {
		$this->conn->rollBack();
	}

	/**
	 * Execute SQL
	 */
	public function Simple($sql) {

		$sortSQL = '';

		$sort = json_decode(urldecode($_GET['sort']), 1);

		if (count($sort) > 0) {
			$sortSQL = "ORDER BY ";
			foreach ($sort as $s) {
				$ss[] = $s['property'] . ' ' . $s['direction'];
			}

			$sortSQL.=implode(', ', $ss);
		}

		$limit = '';

		if ($_GET['limit'] > 0) {

			$page = (int) $_GET['page'];
			$start = (int) $_GET['start'];
			$limit = (int) $_GET['limit'];

			//$start = ($page - 1) * $limit;



			$limit = "
				LIMIT
					" . $start . ", " . $limit . "
			";
		}

		$sql = trim($sql, ';');

		$sql.="
			" . $sortSQL . "
			" . $limit . "
		;";

		return $sql;
	}

	public function Execute($sql = false, $args = array(), $simple = false) {

		if (!$sql) {
			if (!$this->sql) {
				throw new \Exception("No SQL var Set");
			} else {
				$sql = $this->sql;
			}
		} else {
			$this->sql = $sql;
		}

		if ($simple) {
			$sql = $this->simple($sql);
		}


		/**
		 * Prepare SQL
		 */
		try {
			if ($this->conn != null) {
				try {
					$this->SQL = $this->conn->prepare($sql);
				} catch (\PDOExecption $e) {
					echo $e->getMessage();
					die();
				}
			} else {
				throw new Exception('SQL Execution failed - Connection Closed');
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}

		/**
		 * Execute prepared statement with supplied arguments, $args
		 */
		try {

			$this->SQL->execute($args);

			$error_info = $this->SQL->errorInfo();

			if ($error_info[1] > 0) {
				print_r($error_info);
				print_r($args);
				echo $sql;
				//throw new \Exception('SQL Error => <pre>'.print_r($error_info,1).'</pre>');
				throw new \libs\models\SQLError(json_encode($error_info));
			}

			/*
			  } catch( Exception $e ) {
			  echo $e->getMessage();

			  $f = $this->formatSQL($sql,$args);

			  echo '<pre>'.$f."</pre>\n\n";

			  echo '<pre>'.preg_replace('/\s+/',' ',str_replace(array("\n","\t"),' ',$f)).'</pre>';

			  $errors++;

			  // */
		} catch (\PDOExecption $e) {
			echo $e->getMessage();

			$f = $this->formatSQL($sql, $args);

			echo '<pre>' . $f . "</pre>\n\n";

			echo '<pre>' . preg_replace('/\s+/', ' ', str_replace(array("\n", "\t"), ' ', $f)) . '</pre>';

			$errors++;
		}

		if ($errors > 0) {
			die('Script Ended Early');
			return false;
		}

		return $this;
	}

	/**
	 * Get an associative array of ALL ROWS from the SQL and store in local variable RSArray
	 */
	public function returnArray() {
		return $this->SQL->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Get an associative array of ONE ROW values from the SQL and store in local variable RSArray
	 */
	public function returnRow($row = 0) {
		if ($row == 0) {
			$rs = $this->SQL->fetch(\PDO::FETCH_ASSOC);
		} else {
			$t = $this->fetchArray();
			$rs = $t[$row];
		}
		return $rs;
	}

	/**
	 * Get a single column valaue
	 */
	public function returnVal($col, $row = 0) {
		if ($row == 0) {
			$t = $this->SQL->fetch(\PDO::FETCH_ASSOC);
		} else {
			$array = $this->fetchArray();
			$t = $array[$row];
		}
		$rs = $t[$col];
		return $rs;
	}

	/**
	 * Put the value of RSArray into output variable, passed by reference
	 */
	public function fetchArray(&$output) {
		$output = $this->returnArray();
		return $this;
	}

	/**
	 * Put the value of RSRow into output variable, passed by reference
	 */
	public function fetchRow(&$output, $row = 0) {
		$output = $this->returnRow($row);
		return $this;
	}

	/**
	 * Put the value of RSVal into output variable, passed by reference
	 */
	public function fetchVal(&$output, $col, $row = 0) {
		$output = $this->returnVal($col, $row);
		return $this;
	}

	/**
	 * get the number of affected rows and put into output variable, passed by reference
	 */
	public function returnNumAffectedRows() {
		return $this->SQL->rowCount();
	}

	public function fetchNumAffectedRows(&$count) {
		$count = $this->returnNumAffectedRows();
		return $this;
	}

	/**
	 * get the last insert ID it put into output variable, passed by reference
	 */
	public function returnLastInsertID() {
		return $this->conn->lastInsertId();
	}

	public function fetchLastInsertID(&$id) {
		$id = $this->returnLastInsertID();
		return $this;
	}

	public function returnQueryString() {
		return $this->SQL->queryString;
	}

	private function formatSQL($sql, $args) {
		foreach ($args as $key=> $val) {
			$find[] = ':' . $key;
			$replace[] = "'" . $val . "'";
		}

		return str_replace($find, $replace, $sql);
	}

	/**
	 * //for large datasets where retrieving all rows takes too much memory
	 * foreach(XF_DBA::Load($conn_name)->Execute($sql,$args)->Generate() as $column => $value) {
	 *
	 * }
	 */
	public function Generate($mode = \PDO::FETCH_ASSOC) {
		while ($row = $this->SQL->fetch($mode)) {
			yeild($row);
		}
	}

}
