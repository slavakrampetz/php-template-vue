<?php

namespace App;

abstract class ControllerAction extends Controller {

	protected array $data;
	protected string $action;

	public function __construct(App $app) {
		$this->data = [];
		$this->action = 'none';
		parent::__construct($app);
	}

	protected function init(): void {
		$this->data['app'] = $this->app;
	}

	/** Parse context and return an action */
	abstract protected function getAction(): string;

	/** Get template */
	protected function getTemplate(): string {
		return 'index';
	}

	protected function renderTemplate(): bool {
		$tml = $this->getTemplate();
		return Template::render($tml, $this->data);
	}

	final public function run(): bool {

		$this->init();

		$this->action = $this->getAction();
		$method = 'act_' . $this->action;
		if (!method_exists($this, $method)) {
			return logErr(__METHOD__, 'unknown handler for action', $this->action);
		}

		return $this->$method();
	} // run

} // ControllerAction
