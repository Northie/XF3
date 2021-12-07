<?php

namespace services\data\cache;

trait cache {

	private $ttl = false;

	public function getLifetime() {

		if (!$this->ttl) {
			if (($cacheLifetime = \settings\general::Load()->get(['CACHE_LIFETIME']))) {
				$this->ttl = $cacheLifetime;
			} else {
				$this->ttl = 3600;
			}
		}

		return $this->ttl;
	}

	public function setLifetime($ttl) {
		$this->ttl = $ttl;
	}

}
