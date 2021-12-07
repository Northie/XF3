<?php

namespace services\data;

interface iCrud {
	public function create($data,$id=false);
	public function read($data);
	public function update($data,$conditions=false);
	public function delete($data,$conditions=false);
}
