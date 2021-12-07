<?php
namespace libs\factory;

abstract class processStep implements iProcessStep {
	public function start() {
		$this->build();
	}

}