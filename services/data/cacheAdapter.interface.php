<?php

namespace services\data;

interface iCacheAdapter extends iCrud {

	public function query($query,$parameters=false);
		
}
