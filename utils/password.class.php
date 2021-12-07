<?php

namespace utils;

/*

  hide salt in hashed password

  abcdef
  123456

  a		a
  1	1
  b		b
  2	2
  c		c
  3	3
  d		d
  4	4
  e		e
  5	5
  f		f
  6	6

  a1b2c3d4e5f6

  add iteraction count to stored value

  _____a1b2c3d4e5f6

 */

class password {

	public function generatePlain($qty = 1, $len = 12) {

		//return 'password';

		$confusing = array('0', '1', '2', '5', 'i', 'l', 'o', 's', 'z', 'I', 'L', 'O', 'S', 'Z');
		$lower = 'abcdefghijklmnopqrstuvwxyz';
		$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$number = '0123456789';
		$symbol = '!"£$%^&*()@~#?|\/,.-=\'';
		$hex = '0123456789abcdef';

		$str = "";

		//$str.=$upper;

		$str.=$lower;

		//$str.=$hex;

		$str.=$number . $number;

		//$str.=$symbol;

		$str = str_replace($confusing, '', $str);

		for ($j = 0; $j < $qty; $j++) {

			$pw = "";

			for ($i = 0; $i < $len; $i++) {
				$pw.=$str[mt_rand(0, strlen($str) - 1)];
			}

			$passwords[$j] = $pw;
		}

		if ($qty == 1) {
			return $pw;
		}

		return $passwords;
	}

	private function hash_method($str) {
		return hash('sha512', $str);
	}

	public function getHashToStore($plain) {
		$v = rand(100000, 200000); //randomly choose iteration count - approx 1-2 seconds on test machine
		//$v = rand(10,20);	//randomly choose iteration count - smaller for dev purposes

		$hashed = dechex($v); //init store value with hex version of iteration count

		$salt = $this->hash_method(microtime() . mt_rand()); //create pseudo random salt

		$H = $this->hash_method($salt . $plain); //hash password with salt

		for ($i = 0; $i < $v; $i++) {  //hash salt and and password $v times
			$H = $this->hash_method($salt . $H); //myhash abstracts hash method
		}

		for ($i = 0; $i < strlen($salt); $i++) {
			$hashed.=$H[$i] . $salt[$i]; //'interlace' final hash and salt
		}

		return $hashed;
	}

	public function checkPassword($plain, $hashed) {
		$v_len = strlen($hashed) - (2 * strlen($this->hash_method(''))); //get iteraction offset

		$v = substr($hashed, 0, $v_len); //get iteration value

		for ($i = $v_len; $i < strlen($hashed); $i+=2) { //untangle hash and salt
			$hash.=$hashed[$i];
			$salt.=$hashed[$i + 1];
		}

		$v = hexdec($v);   //get itercation decimal value

		$test = $this->hash_method($salt . $plain);  //perform same logic on password

		for ($i = 0; $i < $v; $i++) {   //iterate
			$test = $this->hash_method($salt . $test);
		}

		return (($test == $hash) ? true : false); //compare and return
	}

}
