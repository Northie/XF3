<?php
namespace libs\forms;

class Factory {
	public static function Load($cls) {
		return new $cls($cls);
	}
}