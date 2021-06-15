<?php

// INIT
const PRJ_APP_ROOT = PRJ_ROOT . 'app/';
const PRJ_LIB_ROOT = PRJ_APP_ROOT . 'lib/';
const PRJ_TML_LAY_ROOT = PRJ_APP_ROOT . 'templates/layout/';
const PRJ_TML_VIEW_ROOT = PRJ_APP_ROOT . 'templates/view/';

if (!defined('PRJ_ENV')) {
	define('PRJ_ENV', 'debug');
}
const IS_RELEASE = PRJ_ENV === 'release';
const IS_TEST = PRJ_ENV === 'test';
const IS_DEV = PRJ_ENV === 'debug';
const IS_CLI = PHP_SAPI === 'cli';

function autoload ($name) {
	$fnm = str_replace(['\\', '_'], '/', $name) . '.php';
	$path = PRJ_LIB_ROOT . $fnm;
	if (file_exists($path)) {
		/** @noinspection PhpIncludeInspection */
		include_once $path;
		return;
	}
	if (!IS_RELEASE) {
		wl('Cannot find file: ' . $path);
	}
}
spl_autoload_register('autoload');


require_once PRJ_APP_ROOT . 'functions.php';

