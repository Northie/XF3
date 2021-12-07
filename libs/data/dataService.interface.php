<?php

namespace libs\data;

interface dataService {
	public function isReady();
	public function isValid();
	public function getData();
	public function setData($data);
}