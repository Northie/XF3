<?php

$requiredConstants = [
	 "X1_PATH"
	,"X1_APP_PATH"
	,"X1_WEB_PATH"
	,"X1_DAT_PATH"
	,"X1_APP_NAME"
	,"APP_CLASS_LIST"
];

foreach($requiredConstants as $requiredConstant) {
	if(!defined($requiredConstant)) {
		die('constant '.$requiredConstant.' is not defined');
	}
}

include 'utils/traits/singleton.trait.php';
include 'settings/settings.trait.php';
include 'settings/filelist.settings.php';
include X1_APP_PATH.'/settings/general.trait.php';
include 'settings/general.settings.php';
include 'utils/autoload/fileFinder.class.php';
include 'utils/autoload/autoload.function.php';
