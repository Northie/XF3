<?php

namespace libs\RateLimiter;

interface Request {
    public function execute();
}