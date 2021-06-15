<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

namespace Controller;

use App\ControllerAction;

abstract class PageBase extends ControllerAction {

	protected function init(): void {

		$links = [
			[ 'u' => '/',
			  't' => 'Home' ],

			[ 'u' => '/vue-test/',
			  't' => 'Vue load 1' ],
		];

		$this->data['links'] = $links;
	}

	protected function getAction(): string {
		return 'index';
	}

	public function act_index(): bool {
		return $this->renderTemplate();
	}
}
