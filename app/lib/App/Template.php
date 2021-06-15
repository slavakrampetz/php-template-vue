<?php

namespace App;

use Exception;

final class Template {

	public static function render(string $path, array $data): bool {

		$full = PRJ_APP_ROOT . 'templates/' . $path;
		if (!file_exists($full)) {
			return logErr(__METHOD__, 'cannot find template at path', $full);
		}

		$renderSaveErrorReporting = error_reporting(-1);
		try {
			// Draw
			extract($data, EXTR_OVERWRITE | EXTR_REFS);
			/** @noinspection PhpIncludeInspection */
			include $full;

			return true;
		} catch (Exception $ex) {
			logErr(__METHOD__, 'exception', $ex);
			return false;
		} finally {
			error_reporting($renderSaveErrorReporting);
		}
	}

}
