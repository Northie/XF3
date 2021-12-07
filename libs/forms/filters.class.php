<?php

namespace libs\forms;

class filters {

	public function input($type, $data) {
		if ($type == 'password') {
			return $data;
		} else {

			if (is_array($data)) {
				$data = array_map('trim', $data);
				foreach ($data as $key => $val) {
					$data[$key] = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
				}
			} else {
				$data = trim($data);
				$data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
			}
		}

		return $data;
	}

	public function output($type, $data) {
		if ($type == 'password') {
			$data = '';
		} else {
			if (is_array($data)) {
				$data = array_map('trim', $data);
				foreach ($data as $key => $val) {
					$data[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
				}
			} else {
				$data = htmlentities(trim($data), ENT_QUOTES, 'UTF-8');
			}
		}

		return $data;
	}

}
