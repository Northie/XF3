<?php

namespace views;

interface iView {
	public function __construct();
	public function setData($data);
	public function serve();
}

