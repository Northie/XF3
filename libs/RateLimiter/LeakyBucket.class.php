<?php

namespace libs\RateLimiter;

class LeakyBucket extends RateLimiter
{

    private $delay = 0;
    private $cost = 0;
    public $bucketSize = 1;
    public $dropSize = 0;
    private $completedAt = 0;
    private $startedAt = 0;

    public function __construct($dropSize, $bucketSize)
    {
        $this->dropSize = 1000000 * $dropSize;
        $this->bucketSize = 1000000 * $bucketSize;
    }

    /**
     * will allow a burst until cost is reached, then introduces a sleep between requests to stay within the limits
     */

    public function request(\libs\RateLimiter\Request $request, $args = [], $maxExecutionTime = 30)
    {

        \OS\APP::Load()->setTimeLimit($maxExecutionTime);

        if ($this->delay) {
            if ($this->cost > $this->bucketSize) {
                usleep($this->delay);
            }
        }

        $this->startedAt = $t1 = microtime(true);

        //between calls to ->request(), the appliction may be doing other things - recover that latency into the cost
        if ($this->complatedAt > 0) {
            $externalLatency = $this->startedAt - $this->complatedAt; //start of this cycle, less end of last cycle
            $this->cost -= $externalLatency;
        }

        $result = $request->execute($args);

        $this->completedAt = $t2 = microtime(true);

        $dt = $t2 - $t1;

        //minimum cost, or duration of this cycle if greater
        $this->cost += (($dt > $this->dropSize) ? $dt : $this->dropSize);

        $delay = $this->dropSize - $dt;

        $this->delay = (($delay > 0) ? $delay : 0);

        \OS\APP::Load()->resetTimeLimit();

        return $result;
    }
}
