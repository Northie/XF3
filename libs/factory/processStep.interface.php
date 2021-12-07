<?php

namespace libs\factory;

interface iProcessStep {
	public function __construct($list, $parent, $controller);
	public function Build();
	public function Unbuild();
	public function __destruct(); //cleanup?

}

