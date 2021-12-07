<?php

namespace utils;

use services\data\cache\vendor\file\factory;

class Tools {

	public static function getRequest() {

		$str = $_SERVER['QUERY_STRING'];

		$str = preg_replace("/_dc=[0-9]+/", "", $str);

		$req = explode("/", $str);
		$module = array_shift($req);
		$action = array_shift($req);

		for ($i = 0; $i < count($req); $i+=2) {
			$_GET[$req[$i]] = $_GET[$i + 1];
		}

		return $_GET;
	}

	public static function generatePassword($len = 8, $selection = 'lower', $removeConfusing = true) {

		$lower = 'abcdefghijklmnopqrstuvwxyz';
		$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$number = '0123456789';

		$confusing = array('0', '1', '2', '5', 'i', 'l', 'o', 's', 'z', 'I', 'L', 'O', 'S', 'Z');

		switch ($selection) {
			case 'default':
				$str = $lower . $upper . $number;
				break;
			case 'lower':
				$str = $lower . $number;
				break;
			case 'upper':
				$str = $upper . $number;
				break;
			case 'alpha':
				$str = $lower . $number;
				break;
			default:
				$str = $lower . $upper . $number;
				break;
		}

		if ($removeConfusing) {
			$str = str_replace($confusing, '', $str);
		}

		$pw = "";

		for ($i = 0; $i < $len; $i++) {
			$pw.=$str[rand(0, strlen($str) - 1)];
		}

		return $pw;
	}

	public static function hashPassword($plain) {
		$password = new \utils\password;
		$hashed = $password->getHashToStore($plain);
		return $hashed;
	}

	public static function encryptStr($msg, $key) {
		$c = new \utils\Cryptor;
		return $c->encrypt($msg, $key);
	}

	public static function decryptStr($msg, $key) {
		$c = new \utils\Cryptor;
		return $c->decrypt($msg, $key);
	}

	public static function camel_to_title($str) {
		$basic = 
			trim(
			ucwords(
				strtolower(
					preg_replace(
						'/([0-9]+)|([A-Z])/', ' $0', $str
					)
				)
			)
		);

		/**
		preserve long strings of uppercase
		eg
		abcDEF     =>   Abc D E F      =>  Abc DEF
		abcDEFGhi  =>   Abc D E F Ghi  =>  Abc DEF Ghi
		//*/
		return preg_replace_callback("/(([A-Z])\s)(?![A-Z][a-z])/",function($m){return $m[2];},$basic);
		

	}
		
	public static function camel_to_field($str) {		
			return trim(
				strtolower(
					preg_replace(
						'/([0-9]+)|([A-Z])/', '_$0', $str
					)
				)
			, '_');
	}

	public static function to_camel_case($str) {

		$str = str_replace('_', ' ', $str);
		$str = strtolower($str);
		$str = ucwords($str);
		$str = str_replace(' ', '', $str);



		$str[0] = strtolower($str[0]);

		return $str;
	}

	public static function setCache($key, $data, $ttl = 3600) {
				$adapter = \settings\registry::Load()->get('APP_CACHE');
				return $adapter->create($data,$key);
	}

	public static function getCache($key) {
				$adapter = \settings\registry::Load()->get('APP_CACHE');
				return $adapter->read($key);
	}

	public static function html_escape($raw_input) {
		return htmlspecialchars($raw_input, ENT_QUOTES | ENT_HTML401, 'UTF-8');
	}

	public static function array2object($array) {
		return json_decode(json_encode($arr));
	}

	public static function object2array($object) {
		return json_decode(json_encode($object), 1);
	}
		
		public static function array2xml($array,&$xml=null) {
			if(is_null($xml)) {
				$xml =  new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
			}
			foreach($array as $key => $val) {
				if (is_numeric($key)) {
					$key = 'item' . $key;
				}
   
				if(!is_scalar($val) && !is_array($val)) {
					$val = self::object2array($val);
				}
				
				if(is_array($val)) {
					$subnode = $xml->addChild($key);
					self::array2xml($val,$subnode);
				} else {
					$xml->addChild($key, htmlspecialchars($val));
				}
			}
		}

		public static function cleanHtml($html, $attr_black_list = false, $elem_black_list = false) {
		if (!$attr_black_list || !is_array($attr_black_list)) {
			$attr_black_list = ['onclick'];
		}

		if (!$elem_black_list || !is_array($elem_black_list)) {
			$elem_black_list = ['script', 'iframe'];
		}

		$remove_elems = [];

		$dom = new \DOMDocument();

		@$dom->loadHTML("<html><body>" . $html . "</body></html>");

		$els = $dom->getElementsByTagName('*');


		foreach ($els as $el) {

			foreach ($attr_black_list as $attr) {
				if ($el->hasAttribute($attr)) {
					$el->removeAttribute($attr);
				}
			}

			foreach ($elem_black_list as $elem) {
				if (strtolower($el->nodeName) == $elem) {
					$remove_elems[] = $el;
				}
			}
		}

		foreach ($remove_elems as $r) {
			$r->parentNode->removeChild($r);
		}

		$clean = $dom->saveHtml();

		$tidy_config = [
			'clean'=>true,
			'output-html'=>true,
			'bare'=>true,
			'drop-proprietary-attributes'=>false,
			'fix-uri'=>true,
			'merge-spans'=>false, //ensures editor can work
			'join-styles'=>false,
			'indent'=>true,
			'char-encoding'=>'utf8',
			'force-output'=>true,
			//'quiet'		=>	true,
			'tidy-mark'=>false
		];

		//$tidy = tidy_parse_string($clean,$tidy_config,'UTF8');
		//$tidy->cleanRepair();
		//$clean = (string) $tidy;
		//$fb = new FirePHP();
		//$fb->fb($clean);

		list($start, $trash) = explode("</body>", $clean);

		list($trash, $return) = explode("<body>", $start);

		return $return;
	}

	public static function UUID() {
		//return sha1(microtime(true) . uniqid() . mt_rand(0, mt_getrandmax()));
		//http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid#15875555

		$data = openssl_random_pseudo_bytes(16);
				
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

		$uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

		return $uuid;
	}

	/**
	 *
	 * @param array $a
	 * @param array $parent
	 * @return string
	 * @see http://stackoverflow.com/questions/17316873/php-array-to-a-ini-file
	 */
	public static function arr2ini(array $a, array $parent = array()) {
		$out = '';
		foreach ($a as $k=> $v) {
			if (is_array($v)) {
				//subsection case
				//merge all the sections into one array...
				$sec = array_merge((array) $parent, (array) $k);
				//add section information to the output
				$out .= '[' . implode('.', $sec) . ']' . PHP_EOL;
				//recursively traverse deeper
				$out .= arr2ini($v, $sec);
			} else {
				//plain key->value case
				$out .= "$k=$v" . PHP_EOL;
			}
		}
		return $out;
	}

	public static function filePathProtect($path) {
		
		$find = ["../","./"];
		$replace = "/";
		
		return str_replace("//","/",str_replace($find,$replace,$path));
	}
		
		public static function looksLikeSerialized($str) {
			$pattern = "/^[aOids]\:[0-9]/";
			$subject = substr($str, 0, 10);
			return preg_match($pattern, $subject);
		}
		
		public static function looksLikeJson($str) {
			$pattern = "/^(\[|\{|[0-9]|\")/";
			$subject = substr($str, 0, 10);
			return preg_match($pattern, $subject);
		}
		
		public static function lace(array $strings) {
			foreach($strings as $i => $string) {
				$length = strlen($string);
				$lengths[$i] = $length;
			}
			$maxLength = max($lengths);
			
			foreach($strings as $i => $string) {
				$length = strlen($string);
				if($length < $maxLength) {
					//$strings[$i] = str_pad($string, $maxLength,"=");
					$strings[$i] = $string.self::generatePassword($maxLength-$length);
				}
			}
			
			$output = '';
			
			for($i = 0;$i<$maxLength;$i++) {
				foreach($strings as $j => $string) {
					$output.=$string[$i];
				}
			}
			
			$prepend = base64_encode(count($strings).",".implode(",",$lengths));
			
			return strlen($prepend)."|".$prepend."|".$output;
		}
		
		public static function unlace($string) {
			list($a,$b,$c) = explode("|",$string);
			
			$instructions = explode(",",base64_decode($b));
			
			$stringCount = array_shift($instructions);
			$stringlengths = $instructions;
			
			$strings = [];
			
			for($i=0;$i<strlen($c);$i++) {
				$strings[$i%$stringCount].=$c[$i];
			}
			
			foreach($strings as $i => $string) {
				$strings[$i] = substr($string,0,$stringlengths[$i]);
			}
			
			return $strings;
		}
		
		public static function encryptData($data,$key) {
			$data = [
				'data'=>$data,
				'ts'=> microtime(true),
				'data_is_scalar'=> is_scalar($data)
			];
			$json = json_encode($data,JSON_PRETTY_PRINT);
			
			return self::encryptStr($json,$key);
		}
		
		public static function decryptData($source,$key) {
			$json = self::decryptStr($source, $key);
			$data = json_decode($json,1);
			return $data['data'];
		}
		
		/**
		 * 
		 * @return mixed the first non-null and non-empty argument supplied
		 */
		
		public static function coalesce() {
			//get a list of values to test on
			$args = func_get_args();
			
			//if an array of values has been sent instead
			if(count($args) == 1 && is_array($args[0])) {
				$args = $args[0];
			}
			
			$return = null;
			
			foreach($args as $arg) {
				if(!is_null($arg) && trim($arg) != '') {
					$return = $arg;
					break;
				}
			}
			
			return $return;
		}
		
		public static function coalesceFalsy() {
			//get a list of values to test on
			$args = func_get_args();
			
			//if an array of values has been sent instead
			if(count($args) == 1 && is_array($args[0])) {
				$args = $args[0];
			}
			
			$return = null;
			
			foreach($args as $arg) {
				if(!$arg) {
					continue;
				}
				if(!is_null($arg) && trim($arg) != '') {
					$return = $arg;
					break;
				}
			}
			
			return $return;
		}

		/**
		 * @desc take a 2D assoc array containing IDs (specified by key, $idKey) and parent Ids (specified by $parentKey) and return a nested structure
		 * @param array $data the input array
		 * @param integer $parentId the id of the parent to find children for
		 * @param string $idKey the array key containing the row id
		 * @param string $parentKey the array key containing the row's parent id
		 * @param string $childrenKey the array key to use for populating the children into
		 * @return array
		 */
		public static function getNestedChildren($data,$parentId,$idKey='id',$parentKey='parentId',$childrenKey='children') {
			$nestedTreeStructure = [];
			$length = count($data);

			for($i=0;$i<$length;$i++) {
				$row = $data[$i];
				if($row[$parentKey] == $parentId) {
					$children = self::getNestedChildren($data,$row[$idKey],$idKey,$parentKey,$childrenKey);
					if(count($children) > 0) {
						$row[$childrenKey] = $children;
					}
					$nestedTreeStructure[] = $row;
				}
			}

			return $nestedTreeStructure;

		}
		
		/**
		 * 
		 * @param string $singular Singular form, eg item or quantity
		 * @param string $plural plural form, eg s or quantities
		 * @param numeric $count number to compare for
		 * @param bool $append append the plural form to the singular (eg item+s) or not (eg quantities)
		 * @return string 
		 * @example \utils\tools::pluralise('Item','s',$qty);
		 */
		
		public static function pluralise($singular,$plural,$count,$append=true) {
			if($append) {
				$plural = $singular.$plural;
			}
			
			if($count == 1) {
				return $singular;
			}
			
			return $plural;
		}
		
		public static function scanDirRecursive($path) {
			$directory = new \RecursiveDirectoryIterator($path);
			$iterator = new \RecursiveIteratorIterator($directory); 
			$files = [];
			foreach ($iterator as $info) {
			   $files[] = $info->getPathname();
			}
			return $files;
		}
		
	public static function path2array($path) {
		$parts = explode('/', $path);

		$arr = [];
		while ($bottom = array_pop($parts)) {
			$arr = [$bottom => $arr];
		}
		return $arr;
	}
	
	public static function API($method,$endpoint) {
		
		$fc = \settings\registry::Load()->get('FrontController');
		
		$req = $fc->request;
		
		$newController = clone $fc;
		$newRequest = clone($req);
		
		$newRequest->REQUEST_METHOD = $method;
		$newRequest->REQUEST_URI = $endpoint;
		
		$newController->request = $newRequest;
		
		$newController->init();
		$newController->makeEndpoint();
		if($newController->endpoint) {
			$newController->endpoint->Execute();
			return $newController->endpoint->getData();
		}
		
		return [];
	}

	/**
	 * @desc build up an array from a string path (eg url)
	 * [], store/widgets/blue, '1' becomes ['store'=>['widgets'=>['blue'=>1]]]
	 * @param $target array passed by reference. This array will get the value inserted at the key path from $path
	 * @param $path array|string the path in the $target array to make/modify with $value
	 * @param $value mixed the value to upsert in $target at $path
	 * @param $delimiter string default / delimiter to split $path on when $path is a string
	 */

	public static function setArrayPathValue(&$target,$path,$value,$delimter='/') {
		if(!\is_array($path)) {
			$path = explode($delimter,$path);
		}

		$ref = &$target;

		foreach($path as $step) {
			$ref = &$ref[$step];
		}
		$ref = $value;
	}

	/**
	 * @desc convert a sting or array to nested array, any depth. eg a/b/c becomes $arr['a']['b']['c'] = true;
	 * @param $path string|array 
	 */

	public static function arrayFromPath($path,$delimiter='/',$value=true) {

		if(!is_array($path)) {
			$path = explode($delimiter,$path);
		}

		$paved = [];
		$track   = &$paved;
		foreach ($path as $step) {
		  if (empty($track[$step])) {
			$track[$step] = [];
			$track = &$track[$step];
		  }
		}
		$track = $value;
		unset($track); 
		return $paved;
	}

	public static function unparse_url($parts_arr) 
	{
        if (!empty($parts_arr['scheme'])) {


			if(strpos($parts_arr['scheme'],"://") === false) {
				$parts_arr['scheme'].="://";
			}

			$ret_url = $parts_arr['scheme'];

        }
        if (!empty($parts_arr['user'])) {
            $ret_url .= $parts_arr['user'];
            if (!empty($parts_arr['pass'])) {
                $ret_url .= ':' . $parts_arr['pass'];
            }
            $ret_url .= '@';
        }
        $ret_url .= $parts_arr['host'];
        if (!empty($parts_arr['port'])) {
            $ret_url .= ':' . $parts_arr['port'];
        }
        $ret_url .= $parts_arr['path'];
        if (!empty($parts_arr['query'])) {
            $ret_url .= '?' . $parts_arr['query'];
        }
        if (!empty($parts_arr['fragment'])) {
            $ret_url .= '#' . $parts_arr['fragment'];
        }
        return $ret_url;
	}
	
	public static function looksLikeId($maybeId) {
		if($maybeId > 0 && (int) $maybeId == $maybeId) {
			return true; //numeric id
		}

		if(\preg_match("/[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}/",$maybeId)) {
			return true; //uuid
		}

		return \false;
	}
}
