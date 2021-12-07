<?php

namespace services\data\object\vendor\couchbase;

class adapter extends \services\data\adapter {

	private $keyService;
	private $queryService;
	private $bucket;
	private $v;


	public function __construct($settings)
	{

		if(class_exists("\\CouchbaseCluster",false)) {
			$this->initV2($settings);
			return;
		}
		if(class_exists("\\Couchbase\\Cluster",false)) {
			$this->initV3($settings);
			return;
		}

		throw new \Exception('Couchbase not enabled');
	}

	private function initV2($settings) {
		$host	   = $settings['host'];
		$port	   = $settings['port'];
		$user	   = $settings['user'];
		$password   = $settings['pass'];
		$this->bucket = $bucket = $settings['name'];
		
		$authenticator = new \Couchbase\PasswordAuthenticator();
		$authenticator->username($user)->password($password);

		$cluster = new \Couchbase\Cluster('couchbase://'.$host);
		$cluster->authenticate($authenticator);
		
		$this->queryService = $this->keyService = $cluster->openBucket($bucket);

		$this->v = 2;
	}

	private function initV3($settings) {
		$host	   = $settings['host'];
		$port	   = $settings['port'];
		$user	   = $settings['user'];
		$password   = $settings['pass'];
		$this->bucket = $settings['name'];

		$connectionString = "couchbase://".$host;
		$options = new \Couchbase\ClusterOptions();
		$options->credentials($user, $password);
		$cluster = new \Couchbase\Cluster($connectionString, $options);
		$bucket = $cluster->bucket($settings['name']);
		$this->keyService = $bucket->defaultCollection();
		$this->queryService = $cluster;

		$this->v = 3;

	}


	public function setModelName($model) {
		$this->model = $model;
	}
		
	public function create($data, $id = false) {
			
		$key = $id;

		try {
			$rs = $this->keyService->insert($key, $data);
		} catch (\Exception $e) {
			$rs = false;
		} finally {
			return $rs;
		}
		
	}

	public function readType($type) {
		$items = $this->query('SELECT * from '.$this->bucket.' where `type` = $type',['type'=>$type]);
		foreach($items->rows as $row) {
			yield \utils\Tools::object2array($row->{$this->bucket});
		}
	}
	
	public function read($key) {
		try {
			if($this->model) {
				$key = $this->model."_".$key;
			}
			
			$rs = $this->keyService->get($key);

			switch($this->v) {
				case 2:
					return \utils\Tools::object2array($rs->value);
					break;
				case 3:
					return $rs->content();
					break;
			}
		} catch (\Exception $e) {
			return [];
		}
	}

	/**	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc matching apc user cache behaviour
	 */
	public function replace($data, $conditions = false) {
			
		$key = $conditions;
			
		$exists = 0;
				
		if ($this->read($key) !== null) {
			$exists = 1;
						
			$rs = $this->keyService->upsert($key, $data);
			if (!$rs) {
				$exists = -1;
			}
		}


		return $exists;
	}
		
	public function update($data, $conditions = false, $createIfNotExists=true) {
		$key = $conditions;

		$exists = 0;

		$original = $this->read($key);

		if ($original !== null) {
			$exists = 1;
			if(!is_array($original)) {
				if(!is_array($data)) {
					$new = $data;
				} else {
					$original = [$original];
					$new = array_replace_recursive($original, $data);
				}
			} else {
				$new = array_replace_recursive($original, $data);
			}

			$rs = $this->keyService->upsert($key, $new);
			if (!$rs) {
				$exists = -1;
			}
		} else {
			if($createIfNotExists) {
				$this->create($data,$key);
			}
		}

		return $exists;
	}

	/**
	 *
	 * @param string $key
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 */
	public function delete($key,$force=false) {
		$exists = 0;

		if($force) {
			$rs = $this->keyService->remove($key);
		} else {

			if ($this->read($key)) {
				$exists = 1;
				$rs = $this->keyService->remove($key);
				if (!$rs) {
						$exists = -1;
				}
			}
		}

		return $exists;
	}
		
	public function query($query,$parameters=false) {

		if($this->v == 2) {

			$query = \CouchbaseN1qlQuery::fromString($query);
			if(is_array($parameters)){
				$query->namedParams($parameters);
			}
			
			$result = $this->queryService->query($query);
			return $result;
		}

		if($this->v == 3) {
			$options = new \Couchbase\QueryOptions();
			$options->namedParameters($parameters);
			
			$result = $this->queryService->query($query,$options);
			return $result;
		}
	}
	
	public function queryView($design,$view) {
		$query = \CouchbaseViewQuery::from($design,$view);			
		$result = $this->queryService->query($query);
		return $result;
	}
	
	public function getService() {
		return $this->queryService;
	}
}
