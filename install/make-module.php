<?php

function getArgs($startAt) {
	$c = $_SERVER['argc'];
	$args = [];
	for ($i = $startAt; $i < $c; $i += 2) {
		$args[preg_replace("/^\-{1,2}/", "", $_SERVER['argv'][$i])] = $_SERVER['argv'][$i + 1];
	}
	
	return $args;
}

$cmd = \getArgs(1);

extract($cmd);

$dirs = [
	$name,
	"$name/contexts",
	"$name/flow",
	"$name/plugins",
	"$name/settings",
];

\define('X1_APP_PATH', getcwd()."/server");

foreach($dirs as $dir) {
	$fullPaths[] = X1_APP_PATH."/modules/".$dir;
}

foreach ($fullPaths as $path) {
	\mkdir($path);
	\chmod($path, 0777);
}

$initPath = X1_APP_PATH."/modules/".$name.'/init.class.php';

$initStr = '<?php

namespace modules\\'.$name.';

class init extends \\modules\\module
{
	public function init()
	{
		$this->settings = \\settings\\modules\\'.$name.'::Load()->get();
	}
}
';

\file_put_contents($initPath, $initStr);


$settingsPath = X1_APP_PATH."/modules/".$name.'/settings/'.$name.'.settings.php';

$settingsStr = '<?php

namespace settings\\modules;

class '.$name.' {
	use \\utils\\traits\\singleton;
	use \\settings\\_settings;
	
	protected  $settings = [];
	
	private function __construct() {
		$this->readSettings();
	}
	
	public function readSettings() {
		$settings = [];
		
		$this->settings = &$settings;
	}
	
}
';

\file_put_contents($settingsPath, $settingsStr);

$pluginPath = X1_APP_PATH."/modules/".$name.'/plugins/Load'.ucfirst(strtolower($name)).'Module.plugin.class.php';

$pluginStr = '<?php

namespace Plugins;

class Load'.ucfirst(strtolower($name)).'Module extends DefaultHandler {

	public static function RegisterMe() {
		\Plugins\EventManager::Load()->RegisterHandler(__CLASS__, "onBeforeRequestConstruct");
	}

	public function Execute() {
		switch ($this->event) {
			case "onBeforeRequestConstruct":
				$this->setModulesToRequest();
				break;
		}

		return true;
	}

	private function setModulesToRequest() {

		$this->caller->addModule("'.$name.'");
	}

}

';

\file_put_contents($pluginPath, $pluginStr);

foreach ($fullPaths as $path) {
	\chmod($path, 0755);
}
