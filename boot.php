<?php

use Bitrix\Main;

const NOT_CHECK_PERMISSIONS = true;

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once __DIR__ . '/../vendor/autoload.php';

Main\Loader::includeModule('avito.export');
Main\Loader::registerNamespace('Tests\Unit', __DIR__ . '/unit');
