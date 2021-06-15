<?php

namespace App;

use Exception;

class App {

	public Context $context;
	public Router $router;

	public function init(): void {

		$this->context = new Context($this);

		$this->router = new Router($this);
		$this->router->init();
	}

	public function run(): bool {
		$controller = $this->router->resolve();
		if ($controller === null) {
			$this->showError('Cannot resolve route');
			return false;
		}

		return $controller->run();
	}

	public function report(Exception $ex): void {
	}

	public function showError(string $html): void {
		wl('<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Error happens</title>
</head>
<body>');
		wl($html);
		wl('</body>
</html>');
	}
}
