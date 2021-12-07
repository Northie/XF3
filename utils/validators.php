<?php

namespace utils;

class validators {

	public static function is_unid($str) {
		$pattern = "/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/";

		return preg_match($pattern, $str) ? true : false;
	}

	public static function is_email($str, &$email = false) {

		if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
			$email = $str;
			return true;
		}

		$str = filter_var($str, FILTER_SANITIZE_EMAIL);

		if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
			$email = $str;
			return false;
		}

		return false;
	}

	public static function is_url($str, $url = false) {
		if (filter_var($str, FILTER_VALIDATE_URL)) {
			$email = $str;
			return true;
		}

		$str = filter_var($str, FILTER_SANITIZE_URL);

		if (filter_var($str, FILTER_VALIDATE_URL)) {
			$email = $str;
			return false;
		}

		return false;
	}

	public static function is_ip($str) {
		return filter_var($str, FILTER_VALIDATE_IP);
	}

	public static function is_assoc($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

}
