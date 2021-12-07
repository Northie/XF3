<?php

function xeneco_autoloader($cls) {
	
	$file = \settings\fileList::Load()->getFileForClass($cls);
		
	$c = \settings\general::Load()->get('XENECO', 'ENV');

	if ($c != 'DEV') {

		//if mode is stage or live

		if ($file != '') {
			require_once($file);
		} else {
			/*
			 * find top level request object,
			 * get request format
			 * issue 404 response if api or package resource requested, else 500
			 */

			$message = [
				'404'=>'Not Found',
			];

			header("HTTP/1.1 404 Not Found");
			echo "<h1>404 File Not Found</h1>";
			echo "<h3>If you're sure the file exists, try running again in DEV mode</h3>";
			die();
		}
	} else {

		if ($file != '') {
			require_once($file);
		} else {

			//scan files and recompile file list
			\utils\autoload\fileFinder::CompileFiles();

			\settings\fileList::Load()->includeFileList();

			$file = \settings\fileList::Load()->getFileForClass($cls);
			
			if ($file != '') {
				//include the file
				require_once($file);
			} else {
				//echo "<pre>",print_r(\debug_backtrace()),"</pre>";
				//die($cls . " Has not been defined yet or cannot be found");
				throw new \Exception($cls . " Has not been defined yet or cannot be found");
			}
		}
	}
}

spl_autoload_register('xeneco_autoloader');