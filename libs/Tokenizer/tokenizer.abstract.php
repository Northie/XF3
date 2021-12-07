<?php

namespace tokenizer;

abstract class tokenizer implements iTokenizer {

	protected $terminals = [];
	protected $stream = [];

	public final function init() {

		foreach ($this->lexicals as $key => $val) {
			$this->terminals[$val['regex']] = $key;
		}

		$this->bindFn('define', array($this, 'define'));
		$this->bindFn('each', array($this, 'each'));
	}

	public function bindFn($key, $callback, $args = array()) {
		$this->functions[$key] = [
			"callback" => $callback,
			"args" => $args,
		];
	}

	public function callFn($key, $callback, $args = array()) {
		return call_user_func_array($this->functions[$key], $args);
	}

	public function run() {
		$tokens = array();
		$str = $this->input;

		foreach ($this->terminals as $pattern => $token) {
			$p[trim($pattern, "/")] = $token;
		}

		$pattern = "/" . implode("|", array_keys($p)) . "/";


		$rs = preg_split($pattern, $str, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		//print_r($rs);

		$stream = [];

		foreach ($rs as $val) {
			$matched = false;
			foreach ($this->terminals as $regex => $token) {

				if (preg_match($regex, $val, $m)) {
					$stream[] = [$token => $this->lexicals[$token],'m'=>$m[1]];
					$matched = true;
					break;
				}
			}
			if (!$matched) {
				$stream[] = ['T_RAW' => $val];
			}
		}

		//print_r($stream);
		$this->stream = $stream;
	}

	public function getStream() {
		return $this->stream;
	}

	public function setInput($str) {
		$this->input = $str;
	}

	public function define() {
		
	}

	public function each() {
		
	}

}
