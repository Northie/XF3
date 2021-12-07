<?php

namespace libs\misc;

trait resourceShorthand {
	protected function RS($rs) {
		return \libs\models\Resource::Load($rs);
	}
}

