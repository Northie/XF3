<?php

namespace services\data\cache\vendor\couchbase;

class adapter extends \services\data\adapter {

	use \services\data\cache\cache;
	
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
		$bucket = $settings['name'];
		
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

		$connectionString = "couchbase://".$host;
		$options = new \Couchbase\ClusterOptions();
		$options->credentials($user, $password);
		$cluster = new \Couchbase\Cluster($connectionString, $options);
		$bucket = $cluster->bucket($settings['name']);
		$this->keyService = $bucket->defaultCollection();
		$this->queryService = $cluster;
		$this->v = 3;
	}


	public function create($data,$id = false) {
			
		$key = X1_APP_NAME.'-'.$id;

		if($lifetime) {
			$expires = time() + $lifetime;
		} else {
			$expires = time() + $this->getLifetime();
		}
	
		$meta = ['expires'=>$expires];
			
		$data = [
					 'meta'=>$meta
					,'data'=>$data
				];
		
		
		try {
			$rs = $this->keyService->insert($key, $data);
		} catch (\Exception $e) {
			$rs = false;
		} finally {
			return $rs;
		}
		
		
	}

	public function read($key) {
		$key = X1_APP_NAME.'-'.$key;
	
		try {
			$rs = $this->keyService->get($key);
			
			switch($this->v) {
				case 2:
					$data = \utils\Tools::object2array($rs->value);
					break;
				case 3:
					$data = $rs->content();
					break;
			}

			if(isset($data['meta']['expires']) && $data['meta']['expires'] < time()) {
				\Plugins\EventManager::Load()->ObserveEvent("onCacheStaleHit", $this,['cache_key'=>$key]);
				//cleanup
				$this->delete($key,true);
				return [];
			}
			if(isset($data['data'])) {
				\Plugins\EventManager::Load()->ObserveEvent("onCacheHit", $this,['cache_key'=>$key]);
				return $data['data'];
			}
		} catch (\Exception $e) {
			\Plugins\EventManager::Load()->ObserveEvent("onCacheMiss", $this,['cache_key'=>$key]);
			return [];
		}
	}

	/**	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc matching apc user cache behaviour
	 */
	public function update($data, $conditions = false) {

		$key = $key = X1_APP_NAME.'-'.$conditions;
				
		$exists = 0;

		if ($this->read($key)) {
			$exists = 1;
						
			if($lifetime) {
				$expires = time() + $lifetime;
			} else {
				$expires = time() + $this->getLifetime();
			}

			$meta = ['expires'=>$expires];

			$data = json_encode([
				 'meta'=>$meta
				,'data'=>$data
			]);
						
			$rs = $this->keyService->upsert($key, $data);
			if (!$rs) {
				$exists = -1;
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
				$key = X1_APP_NAME.'-'.$key;
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
}
