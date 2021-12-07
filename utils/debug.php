<?php

namespace utils;

class debug {

	public static function print_me($var, $return = false) {
		if (is_scalar($var)) {
			$var = [$var];
		}
		$stack = debug_backtrace();

		$trace = $stack[1];

		if ($stack[1]['class'] == __CLASS__) {
			$trace = $stack[2]; //some other debug methods call this method
		}

		if ($stack[2]['class'] == __CLASS__) {
			$trace = $stack[3]; //some other debug methods call this method
		}

		$var['trace']['file'] = $trace['file'];
		$var['trace']['line'] = $trace['line'];
		$var['trace']['function'] = $trace['function'];
		$var['trace']['class'] = $trace['class'];

		return print_r($var, $return);
	}

	public static function printNice($var) {
		echo "<pre>", self::print_me($var, 1), "</pre>";
	}

	public static function printNiceAndDie($var) {
		self::printNice($var);
		die();
	}

	public static function printComment($var) {
		echo "<!-- \n", self::print_me($var, 1), "\n -->";
	}

	public static function FirePHP($var) {
		if (is_scalar($var)) {
			$var = [$var];
		}

		$stack = debug_backtrace();

		$trace = $stack[1] ? $stack[1] : $stack[0];

		$var['trace']['file'] = $trace['file'];
		$var['trace']['line'] = $trace['line'];
		$var['trace']['function'] = $trace['function'];
		$var['trace']['class'] = $trace['class'];

		\libs\FirePHP\FirePHP::getInstance(true)->log($var);
	}

	public static function printJson($var) {

		$stack = debug_backtrace();

		$trace = $stack[1];

		$var['trace']['file'] = $trace['file'];
		$var['trace']['line'] = $trace['line'];
		$var['trace']['function'] = $trace['function'];
		$var['trace']['class'] = $trace['class'];

		echo json_encode($var, JSON_PRETTY_PRINT);
	}

}