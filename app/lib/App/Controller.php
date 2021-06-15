<?php

namespace App;

abstract class Controller {

	protected App $app;
	protected Context $context;

	public function __construct(App $app) {
		$this->context = $app->context;
		$this->app = $app;
	}

	abstract public function run(): bool;

}
