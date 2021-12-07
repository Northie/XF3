<?php

namespace utils\autoload;

class fileFinder {
	public static function CompileFiles($vendorPaths=[],$ignore=[]) {

		$appPaths = [
			\X1_PATH,
			\X1_APP_PATH
		];
		
		$paths = array_merge($appPaths,$vendorPaths);

		$ignore = array_merge($ignore,['.htaccess', 'error_log', 'cgi-bin', 'php.ini', '.ftpquota', '.svn', '.git','.gitignore']);
				
		$all = [];
		
		foreach($paths as $path) {
		
			$dirTree = self::getDirectory($path, $ignore);

			foreach ($dirTree as $dir => $files) {
					foreach ($files as $file) {
							$a = $dir . DIRECTORY_SEPARATOR . $file;
							$a = str_replace('/', DIRECTORY_SEPARATOR, $a);
							$a = str_replace('\\', DIRECTORY_SEPARATOR, $a);

							$a = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $a);

							$all[] = $a;
					}
			}
		}
				
		$hooks = array();

		$error_level = ini_get('error_reporting');
		ini_set('error_reporting',(E_ALL ^ E_NOTICE));  //ignore notices;
		//print_r($all);

		foreach ($all as $file) {

			set_time_limit(30);

			$d = self::getContexts($file);
						
			if(!$d) {
				continue;
			}

			//echo "$file\n=========================\n\n".print_r($d,1)."\n";

			for ($i = 0; $i < count($d['classes']); $i++) {

				$class = $d['classes'][$i];

				$lines.='$classlist[\'' . trim($prefix . $d['namespaces'][0] . ($d['namespaces'][0] == '' ? '' : '\\') . $class, '\\') . '\'] = \'' . $file . '\';' . "\n";
				break;
			}

			for ($i = 0; $i < count($d['interfaces']); $i++) {
				$interface = $d['interfaces'][$i];

				$lines.='$classlist[\'' . trim($prefix . $d['namespaces'][0] . ($d['namespaces'][0] == '' ? '' : '\\') . $interface, '\\') . '\'] = \'' . $file . '\';' . "\n";
				break;
			}

			for ($i = 0; $i < count($d['traits']); $i++) {
				$trait = $d['traits'][$i];

				$lines.='$classlist[\'' . trim($prefix . $d['namespaces'][0] . ($d['namespaces'][0] == '' ? '' : '\\') . $trait, '\\') . '\'] = \'' . $file . '\';' . "\n";
				break;
			}

			for ($i = 0; $i < count($d['plugins']); $i++) {
				$hooks[$d['plugins'][$i]] ++;
			}
		}

		ini_set('error_reporting',$error_level);  //restore error_level
		
		$h = array_keys($hooks);
		sort($h);

		file_put_contents(\APP_CLASS_LIST, "<?php\n\n" . $lines);
		file_put_contents(dirname(APP_CLASS_LIST).'/hook-list.txt', implode("\n", $h));
				
		require \APP_CLASS_LIST;
		//file_put_contents(APP_CLASS_LIST_JSON, json_encode($classlist,JSON_PRETTY_PRINT));

		//echo "class list written";
	}

	public static function getDirectory($path = '.', $ignore = '') {
		$dirTree = array();
		$dirTreeTemp = array();
		$ignore[] = '.';
		$ignore[] = '..';

		$dh = @opendir($path);

		while (false !== ($file = readdir($dh))) {

			if (!in_array($file, $ignore)) {
				if (!is_dir("$path/$file")) {
					$dirTree["$path"][] = $file;
				} else {
					$dirTreeTemp = self::getDirectory("$path/$file", $ignore);
					if (is_array($dirTreeTemp)) {
						$dirTree = array_merge($dirTree, $dirTreeTemp);
					}
				}
			}
		}
		closedir($dh);
		return $dirTree;
	}

	public static function getContexts($path) {

		$c = file_get_contents($path);
		$c = trim($c);
		
		if(strpos($c,"<?") !== 0) {
			return [];
		}

		//echo "Scanning File ".$path."....\n";

		$a = token_get_all($c);

                $namespaces = $classes = $interfaces = $traits = $plugins = [];
                
		for ($i = 0; $i < count($a); $i++) {
			
			//echo "==<pre>\n".$a[$i][0]."\n".$a[$i][1]."\n</pre>==<br />\n";

			if (strtolower($a[$i][1]) == 'namespace') {
				$j = 1;
				$namespace = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$namespace.=$a[$i + $j][1];
					$j++;
				}
				$namespaces[] = trim($namespace);
				$i+=$j;
				//echo "Logging NameSpace ".$namespace.".....\n";
			}

			if (strtolower($a[$i][1]) == 'class') {
				$j = 1;
				$class = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$class.=$a[$i + $j][1];
					$j++;
				}

				$classes[] = trim($class);
				$i+=$j;
				//echo "Logging Class ".$class.".....\n";
			}

			if (strtolower($a[$i][1]) == 'interface') {
				$j = 1;
				$interface = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$interface.=$a[$i + $j][1];
					$j++;
				}

				$interfaces[] = trim($interface);
				$i+=$j;
				//echo "Logging Interface ".$interface.".....\n";
			}

			if (strtolower($a[$i][1]) == 'trait') {
				$j = 1;
				$trait = '';
				while (true) {
					if (trim($a[$i + $j][1]) == '') {
						if ($j != 1) {
							break;
						}
					}

					$trait.=$a[$i + $j][1];
					$j++;
				}

				$traits[] = trim($trait);
				$i+=$j;
				//echo "Logging Trait ".$trait.".....\n";
			}
		}

		$ps = preg_split("/DoPlugins\('|DoPlugins\(\"/", $c);

		for ($i = 1; $i < count($ps); $i++) {

			list($pi, $t) = preg_split("/'|\"/", $ps[$i]);

			if (strpos($pi, "==") > -1) {
				;
			} else {
				$plugins[] = $pi;
			}
		}

		return array('namespaces' => $namespaces, 'classes' => $classes, 'interfaces' => $interfaces, "traits" => $traits, "plugins" => $plugins);
	}
}