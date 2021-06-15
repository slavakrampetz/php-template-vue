<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

namespace Controller;

class Home extends PageBase {

//	protected function init(): void {
//		parent::init();
//	}

	protected function getTemplate(): string {
		return 'view/Home/index.php';
	}

}
