<?php

namespace services\data;

interface iAdapter extends iCrud {

	public function query($query,$parameters=false);
		
}
