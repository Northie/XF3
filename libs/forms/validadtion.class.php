<?php

namespace libs\forms;

class validation {
	public static $result = null;
	public static function Email($key) {
		if(filter_var($_POST[$key],FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}

	public static function URL($key) {
		return filter_var($_POST[$key], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
	}
	
	public static function none($key) {
		return true;
	}
	
	public static function match($key,$field) {
		
		return ($_POST[$key] == $_POST[$field]) ? true : false;
	}
	
	public static function recaptcha($key) {
		//$privatekey = \core\System_Settings::Load()->getSettings('recaptcha','privatekey');
		//$resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		//self::$result = $resp;
		
		$isvalid = false;
		if( isset( $_POST['recaptcha'] ) && isset( $_SESSION['random_number'] ) ) {
			$isvalid = ( $_POST['recaptcha'] == $_SESSION['recaptcha_code'] );
		}
		return $isvalid;
	}
}