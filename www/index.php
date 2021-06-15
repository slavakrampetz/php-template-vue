<?php

require_once __DIR__ . '/../host.php';
require_once __DIR__ . '/../app/boot.php';

$app = new App\App();

try {
	$app->init();
	$res = $app->run();
	if ($res === false) {
		error_500(false);
		$app->showError('Error processing request, see logs for details');
	}
} catch (Exception $ex) {
	logErr('App', 'Exception', $ex);
	$app->report($ex);
}
