<?php

namespace libs\RateLimiter;

class RateLimiter
{

	private $delay = 0;
	public $interval = 0;

	public function __construct($interval)
	{
		$this->interval = 1000000 * $interval;
	}

	public function request(\libs\RateLimiter\Request $request, $args = [], $maxExecutionTime = 30)
	{

		\OS\APP::Load()->setTimeLimit($maxExecutionTime);

		if ($this->delay) {
			usleep($this->delay);
		}

		$t1 = microtime(true);

		$result = $request->execute($args);

		$t2 = microtime(true);

		$dt = $t2 - $t1;

		$delay = $this->interval - $dt;

		$this->delay = $delay > 0 ? $delay : 0;

		\OS\APP::Load()->resetTimeLimit();

		return $result;
	}
}
