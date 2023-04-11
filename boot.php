<?php

use Bitrix\Main;

const NOT_CHECK_PERMISSIONS = true;

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once __DIR__ . '/../vendor/autoload.php';

Main\Loader::includeModule('avito.export');

spl_autoload_register(static function($className) {
	static $namespace = 'Tests\\Unit\\';
	static $namespaceLength = null;

	if (!isset($namespaceLength))
	{
		$namespaceLength = strlen($namespace);
	}

	if (strpos($className, $namespace) === 0)
	{
		$classNameRelative = substr($className, $namespaceLength);
		$classRelativePath =  str_replace('\\', '/', $classNameRelative) . '.php';
		$classFullPath = __DIR__ . '/unit/' . $classRelativePath;

		if (file_exists($classFullPath))
		{
			require_once $classFullPath;
		}
	}
});