<?php

\error_reporting(E_ALL & ~ \E_NOTICE);

function getArgs($startAt) {
	$c = $_SERVER['argc'];
	$args = [];
	for ($i = $startAt; $i < $c; $i += 2) {
		$args[preg_replace("/^\-{1,2}/", "", $_SERVER['argv'][$i])] = $_SERVER['argv'][$i + 1];
	}

	return $args;
}

$cmd = \getArgs(1);


$endpointTpl = \file_get_contents(realpath(implode(\DIRECTORY_SEPARATOR, [__DIR__, '..', 'endpoints', 'endpoint.php.tpl'])));
$viewTpl = \file_get_contents(realpath(implode(\DIRECTORY_SEPARATOR, [__DIR__, '..', 'views', 'view.php.tpl'])));
$templateTpl = \file_get_contents(realpath(implode(\DIRECTORY_SEPARATOR, [__DIR__, '..', 'views', 'templates', 'template.phtml.tpl'])));

$tokenPattern = "/(\{\{[0-9a-zA-Z_]{1,}\}\})/";

preg_match_all($tokenPattern, $endpointTpl, $EPmatches);
preg_match_all($tokenPattern, $viewTpl, $Vmatches);
preg_match_all($tokenPattern, $templateTpl, $Tmatches);

$find = \array_unique(\array_merge($EPmatches[0], $Vmatches[0], $Tmatches[0]));

$find = \array_flip($find);

extract($cmd);

if (isset($cmd['module'])) {
	$find['{{moduleNsComment}}'] = '';
	$find['{{defaultNsComment}}'] = '//';
	$find['{{module}}'] = $cmd['module'];

	if (!is_dir("server/modules/$module/contexts/$context")) {
		@mkdir("server/modules/$module/contexts/$context/");
		chmod("server/modules/$module/contexts/$context/",0777);
		
		@mkdir("server/modules/$module/contexts/$context/endpoints");
		@mkdir("server/modules/$module/contexts/$context/views");
		@mkdir("server/modules/$module/contexts/$context/templates");
	}
	
	@mkdir("server/modules/$module/contexts/$context/templates/$name");



	$targetEpPath = "server/modules/$module/contexts/$context/endpoints/$name.class.php";
	$targetVPath = "server/modules/$module/contexts/$context/views/$name.php";
	$targetTPath = "server/modules/$module/contexts/$context/templates/$name/index.phtml";
} else {
	$find['{{moduleNsComment}}'] = '//';
	$find['{{defaultNsComment}}'] = '';
	$find['{{module}}'] = '';

	if (!is_dir("server/contexts/$context")) {
		@mkdir("server/contexts/$context/");
		chmod("server/contexts/$context/",0777);
		@mkdir("server/contexts/$context/endpoints");
		@mkdir("server/contexts/$context/views");
		@mkdir("server/contexts/$context/templates");
	}
	
	@mkdir("server/contexts/$context/templates/$name");

	$targetEpPath = "server/contexts/$context/endpoints/$name.class.php";
	$targetVPath = "server/contexts/$context/views/$name.php";
	$targetTPath = "server/contexts/$context/templates/$name/index.phtml";
}

$find['{{name}}'] = $cmd['name'];
$find['{{context}}'] = $cmd['context'];

$replace = \array_values($find);
$find = \array_keys($find);

if (!\file_exists($targetEpPath)) {
	\file_put_contents($targetEpPath, str_replace($find, $replace, $endpointTpl));
}

if (!\file_exists($targetVPath)) {
	\file_put_contents($targetVPath, str_replace($find, $replace, $viewTpl));
}

if (!\file_exists($targetTPath)) {
	\file_put_contents($targetTPath, str_replace($find, $replace, $templateTpl));
}

echo "Made $module $context $name";

