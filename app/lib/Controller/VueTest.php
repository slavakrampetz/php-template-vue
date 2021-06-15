<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

namespace Controller;

class VueTest extends PageBase {

	protected function init(): void {
		parent::init();

		$this->data['pageTitle'] = 'VueTest: how to use Vue files from PHP/MVC/Templates';
	}

	protected function getAction(): string {

		$nof = count($this->context->PARAMS);
		if ($nof === 0) {
			return 'index';
		}

		// TODO: later
//		$kind = $this->context->URL[0];
//		switch ($kind) {
//			case 'ajax':
//				return $this->getAjaxAction();
//		}

		// Not known
		return 'error';
	}

	protected function getTemplate(): string {

		switch ($this->action) {
			case 'error':
			case 'index':
				return 'view/VueTest/' . $this->action . '.php';
		}

		return '';
	}

	public function act_index(): bool {
		return $this->renderTemplate();
	}

}
