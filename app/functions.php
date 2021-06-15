<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

if (!function_exists('isAjax')) {
	function isAjax(): bool {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
}

if (!function_exists('isPost')) {
	function isPost(): bool {
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
	}
}

function isHttps(): bool {
	return !empty($_SERVER['HTTPS']) && mb_strtolower($_SERVER['HTTPS']) !== 'off';
}

if (!function_exists('getFirstFromArray')) {
	function getFirstFromArray($array) {
		$keys = array_keys($array);
		if (!$keys) {
			return false;
		}
		$key = array_shift($keys);
		return $array[$key];
	}
}

if (!function_exists('getLastFromArray')) {
	function getLastFromArray($array) {
		$keys = array_keys($array);
		$key = array_pop($keys);
		return $array[$key];
	}
}

/** Wrapper around system json_encode, for get rid of linter warnings
 * @param $value
 * @param bool $isPretty Is Pretty print values, useful for debug
 * @return string
 */
function json_enc($value, bool $isPretty = false): string {
	$flags = JSON_UNESCAPED_UNICODE;
	if (IS_DEV && $isPretty) {
		$flags |= JSON_PRETTY_PRINT;
	}
	/** @noinspection JsonEncodingApiUsageInspection */
	return json_encode($value, $flags);
}

/**
 * Wrapper around system json_decode, for get rid of linter warnings
 * @param string $json JSON
 * @param int $flags Flags, if need
 * @return mixed
 */
function json_dec(string $json, int $flags = 0) {
	/** @noinspection JsonEncodingApiUsageInspection */
	return json_decode($json, true, 512, $flags);
}

/**
 * Fake 'random' int without exception
 *
 * @param int $min Minimum
 * @param int $max Maximum
 * @return int
 */
function rndFake(int $min, int $max): int {
	$mi = min($min, $max);
	$max = max($min, $max);
	$min = $mi;
	$diff = $max - $min;
	if ($diff < 1) {
		return $min;
	}

	static $last;
	if ($last === null) {
		$msec = (int) date_format(date_create(), 'v') + 1;
		$isOdd = ($msec % 2) === 0;
		$last = min((int) $diff / 8, 1) * ($isOdd ? 1 : -1);
	} else {
		$last *= -2;
	}
	if ($last < $min) {
		$last = $diff; // max
	} else if ($last > $max) {
		$last = 0; // min
	}
	return $min + $last;
}

function rnd(int $min, int $max): int {
	/** @noinspection PhpUnhandledExceptionInspection */
	return random_int($min, $max);
}

/**
 * Safe random
 * @param int $min Minimum
 * @param int $max Maximum
 * @return int random result
 */
function rndSafe(int $min, int $max): int {
	try {
		return random_int($min, $max);
	} catch (Exception $x) {
		return rndFake($min, $max);
	}
}

/**
 * Safe get value from array by key. If not exists, return $def
 * @param array|null $ar Array
 * @param mixed $key Array key
 * @param mixed $def Default value
 * @return mixed Value by key or $default
 */
function arGet(?array $ar, $key, $def) {
	if (!is_array($ar) || !array_key_exists($key, $ar)) {
		return $def;
	}
	return $ar[$key];
}

/**
 * Safe get constant, if it defined. If not, return $def
 * @param string $name Constant name
 * @param mixed $def Default value
 * @return bool|int|float|string|array Constant value
 */
function constGet(string $name, $def = false) {
	if (defined($name)) {
		return constant($name);
	}
	return $def;
}

function constSet(string $name, $val): void {
	if (defined($name)) {
		return;
	}
	// Deny statistics
	define($name, $val);
}

/**
 * Safe get variable from session. Remove if it not needed after that.
 * @param string $key      Session key
 * @param mixed  $default  Default value, which should be returned if no such data in session
 * @param bool   $isRemove Should data be removed from session?
 * @return mixed
 */
function sessionGet(string $key, $default, bool $isRemove = true) {
	if (!isset($_SESSION[$key])) {
		return $default;
	}

	$res = $_SESSION[$key];
	if ($isRemove) {
		unset($_SESSION[$key]);
	}
	return $res;
}

/**
 * Safe create directory if it doesn't exists
 *
 * @param string $path
 * @param int $mode
 * @param bool $recursive
 * @return bool
 */
function mkDirSafe(string $path, int $mode = 0777, bool $recursive = false): bool {
	if (is_dir($path)) {
		return true;
	}
	if (!mkdir($path, $mode, $recursive) && !is_dir($path)) {
		return false;
	}
	chmod($path, $mode);
	if (defined('ADX_VAR_FILES_OWNER_GROUP')) {
		chgrp($path, ADX_VAR_FILES_OWNER_GROUP);
	}
	return true;
}

if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($text): string {
		return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
	}
}

if (!function_exists('mb_str_replace')) {
	/**
	 * @param array|string $search
	 * @param array|string $replace
	 * @param array|string $subject
	 * @return array|string
	 */
	function mb_str_replace($search, $replace, $subject) {
		if (is_array($subject)) {
			$ret = [];
			foreach ($subject as $key => $val) {
				$ret[$key] = mb_str_replace($search, $replace, $val);
			}
			return $ret;
		}

		if (!is_array($search)) {
			$search = [(string) $search];
		}

		foreach ($search as $key => $s) {
			/** @noinspection PhpStrictComparisonWithOperandsOfDifferentTypesInspection */
			if ($s === '' && $s !== 0) {
				continue;
			}
			if (!is_array($replace)) {
				$r = $replace;
			} else {
				$r = array_key_exists($key, $replace) ? $replace[$key] : '';
			}

			$parts = mb_split($s, $subject);
			$subject = implode($r, $parts);
		}
		return $subject;
	}
}

function str_replace_first($search, $replace, $subject) {
	$pos = strpos($subject, $search);
	if ($pos !== false) {
		$subject = substr_replace($subject, $replace, $pos, strlen($search));
	}
	return $subject;
}

function str_replace_last($search, $replace, $subject) {
	$pos = strrpos($subject, $search);
	if ($pos !== false) {
		$subject = substr_replace($subject, $replace, $pos, strlen($search));
	}
	return $subject;
}

if (!function_exists('array_remove')) {
	/**
	 * @param array $array
	 * @param mixed $value
	 */
	function array_remove(array $array, $value) {
		$needle = array_search($value, $array, true);
		if ( $needle !== false) {
			unset($array[$needle]);
		}
	}
}

/**
 * Makes path relative. Useful for output path
 * Example: path=/long/path/to/file.jpg, base=/long/path/, result is to/file.jpg
 * @param string $path Path to shorten
 * @param string $base Base path
 * @return string Shortened path
 */
function pathRel(string $path, string $base): string {
	if (empty($path) || empty($base)) {
		return $path;
	}
	$path2 = preg_replace('~[\\\]~u', '/', $path);
	$base = preg_replace('~[\\\]~u', '/', $base);
	$lp = mb_strlen($path2);
	$lb = mb_strlen($base);
	if ($lp <= $lb) {
		return $path;
	}
	$first = mb_substr($path2, 0, $lb);
	if ($first !== $base) {
		return $path;
	}
	return mb_substr($path, $lb);
}


function implodeFields(array $data, string $sep, string $def, string ...$fields): string {
	$res = [];
	foreach ($fields as $field) {
		if (empty($data[$field] ?? null)) {
			continue;
		}
		$res[] = $data[$field];
	}
	if (empty($res)) {
		return $def;
	}
	return implode($sep, $res);
}

function fsRmDirRecursive($d) {
	if (!is_dir($d)) {
		return true;
	}
	$dp = dir($d);
	$res = true;
	while (($f = $dp->read()) !== false) {
		if ($f === '.' || $f === '..') {
			continue;
		}
		$path = $d . $f;
		if (is_dir($path)) {
			$res &= fsRmDirRecursive($d . $f . '/');
		} else if (file_exists($path)) {
			$res &= @unlink($path);
		}
	}
	$dp->close();
	$res &= rmdir($d);
	return $res;
}

function frRmFile(string $fnm): bool {
	if (!file_exists($fnm)) {
		return true;
	}
	return @unlink($fnm);
}


function fsTouchFile($path, $mode = 0664) {
	if (!file_exists($path)) {
		touch($path);
		chmod($path, $mode);
		if (defined('PJR_FILES_OWNER_GROUP')) {
			chgrp($path, PJR_FILES_OWNER_GROUP);
		}
	}
}


/**
 * Write error to log
 *
 * @param string|null $me Method (or other codepoint) definition
 * @param mixed $args Text or object
 * @return false
 */
function logErr(?string $me, ...$args): bool {
	return logWr($me, 'error> ', ...$args);
}

/**
 * Write to log
 * @param string|null $me Method description
 * @param array $args message/data
 * @return false
 */
function logWr(?string $me, ...$args): bool {
	$text = vs(PHP_EOL, ...$args);
	$fnm = PRJ_ROOT . 'var/app.log';
	fsTouchFile($fnm);
	file_put_contents($fnm,
		date( 'Y-m-d H:i:s' ) . ': ' .
		($me === null ? '' : $me . ': ') .
		$text . PHP_EOL,
		FILE_APPEND);
	return false;
}

/**
 * Write to log if condition met
 * @param string|null $me Method description
 * @param bool|string $condition If bool, look for condition. If string, look for such define
 * @param mixed $args message/data
 * @return true if condition not met, false otherwise
 */
function logIf(?string $me, $condition, ...$args) : bool {
	if( (is_bool($condition) && !$condition) || (is_string($condition) && !defined($condition))) {
		return true;
	}
	return logWr($me, ...$args);
}

/**
 * Dump some variables to string, separate lines at length 80 by <code>\n</code>
 *
 * @param mixed $me Method or other mark
 * @param array $args Variables
 * @return string
 */
function vv($me, ...$args): string {
	return $me . ':' . vs(PHP_EOL, ...$args);
}

/**
 * Dump some variables to string, separate lines at length 80 by $separator
 *
 * @param string $separator Separator
 * @param array $args Variables
 * @return string
 */
function vs(string $separator, ...$args): string {
	return vsl($separator, 80, ...$args);
}

/**
 * Dump some variables to string, separate lines at length $maxLen by $separator
 *
 * @param string $separator Separator
 * @param int $maxLen Maximum length of characters at one line
 * @param array $args Variables
 * @return string
 */
function vsl(string $separator, int $maxLen, ...$args): string {
	$res = '';
	$nof = 0;
	if ($maxLen < 10) {
		$maxLen = 80;
	}
	foreach ($args as $arg) {
		$len = 0;
		$s = vdl($arg, $len);
		if ($len + 1 + $nof > $maxLen) {
			$sep = $separator;
			$nof = 0;
		} else if ($nof > 0) {
			$sep = ' ';
			$nof += 1 + $len;
		} else {
			$sep = '';
			$nof += $len;
		}
		$res .= $sep . $s;
	}
	return $res;
}

/**
 * Dump one variable to string
 *
 * @param mixed $arg Variable
 * @return string
 */
function vd($arg): string {
	$len = 0;
	return vdl($arg, $len);
}

/**
 * Dump one variable to string,
 *
 * @param mixed $arg Variable
 * @param int $len Length of result string, returned
 * @return string
 */
function vdl($arg, int &$len): string {
	if (null === $arg) {
		$msg = 'null';
	} else if (is_string($arg)) {
		$msg = $arg;
	} else if (is_bool($arg)) {
		$msg = $arg ? 'true' : 'false';
	} else if (is_scalar($arg)) {
		$msg = $arg;
	} else {
		$msg = var_export($arg, true);
		$msg = preg_replace('/(\s*\n)?\s*array\s*\(/', ' [', $msg);
		$msg = preg_replace('/,?\s*\)(,\s*\n)?/', ']\1', $msg);
	}
	$len = mb_strlen($msg);
	return $msg;
}

/** @noinspection HttpUrlsUsage */
function getUrlWithProtocol(string $value): string {
	if (empty(trim($value))) {
		return '';
	}

	if (mb_strpos($value, 'http://') === false && mb_strpos($value, 'https://') === false) {
		return 'http://' . $value;
	}

	return $value;
}


function o($var) {
	echo $var;
}

function oif(bool $condition, $var) {
	if (!$condition) {
		return;
	}
	o($var);
}

function f($var) {
	echo str_replace('.', ',', (float)$var);
}

function e($var) {
	/** @noinspection ArgumentEqualsDefaultValueInspection */
	echo htmlspecialchars($var, ENT_QUOTES/*, 'UTF-8'*/);
}

function eif(bool $condition, $var) {
	if (!$condition) {
		return;
	}
	e($var);
}

function wl($var) {
	echo $var;
	echo PHP_EOL;
}

function ne($var) {
	/** @noinspection ArgumentEqualsDefaultValueInspection */
	echo nl2br(htmlspecialchars($var, ENT_QUOTES/*, 'UTF-8'*/));
}

function neif(bool $condition, $var) {
	if (!$condition) {
		return;
	}
	ne($var);
}

function n($var) {
	echo nl2br($var);
}

function nif(bool $condition, $var) {
	if (!$condition) {
		return;
	}
	n($var);
}


function cut_txt_by_words($text, $end = '...', $limit = 20): string {
	$i = 0;
	$limit = (int) $limit;
	$text = str_replace(['\r\n', '<br>', '<br/>', '<p>', '</p>', '<br />', '&nbsp;', ' '], ' ', $text);
	$text = strip_tags($text);

	$pool = '';
	$txt = explode(' ', $text);
	if (count($txt) <= $limit) {
		return $text;
	}
	foreach ($txt as $val) {
		if (trim($val) !== '') {
			$pool .= $val . ' ';
			$i++;
		}
		if ($i === $limit) {
			break;
		}
	}

	return $pool . $end;
}

function empty_with_trim(&$str, bool $is_html = false): bool {
	if (empty($str)) {
		return true;
	}
	if ($is_html) {
		return empty(trim(strip_tags($str)));
	}
	$str = trim($str);
	return empty($str);
}

function cut_txt_by_letters($text, $dots = '...', $limit = 60, $dotsInTheMiddle = false): string {
	$limit = (int) $limit;
	$text = str_replace(['\r\n', '<br>', '<br/>', '<p>', '</p>', '<br />', '&nbsp;'], ' ', $text);
	$text = strip_tags($text);
	$length = mb_strlen($text);

	if ($length <= $limit) {
		return $text;
	}

	if (!$dotsInTheMiddle) {
		return mb_substr($text, 0, $limit) . $dots;
	}

	$limitHalf = floor($limit / 2);
	return mb_substr($text, 0, $limitHalf) . $dots . mb_substr($text, $length - $limitHalf);
}


function redirect($url, $is_exit = true) {
	header('Location: ' . $url);
	if ($is_exit) {
		exit;
	}
}

function redirect301($url, $is_exit = true) {
	header_status('301 Moved Permanently');
	redirect($url, $is_exit);
}

function header_status($header) {
	if (PHP_SAPI === 'cgi') {
		header('Status: ' . $header);
	} else {
		header('HTTP/1.0 ' . $header);
	}
}

function error_503(bool $isExit = true) {
	header_status('503 Service Unavailable');
	$dieMessage = 'Our website is under maintenance. Sorry for the inconvenience. Please try again later.';
	if ($isExit) {
		exit($dieMessage);
	}
	echo $dieMessage;
}

function error_500(bool $isExit) {
	header_status('500 Internal error');
	if ($isExit) {
		exit();
	}
}

function error_400(bool $isExit = true) {
	header_status('400 Bad Request');
	$dieMessage = 'Неверный запрос.';
	if ($isExit) {
		exit($dieMessage);
	}
	echo $dieMessage;
}


/**
 * @noinspection PhpDocSignatureInspection
 *
 * Статистика времени выполнения. Варианты вызова:
 *
 * saveTime()
 *   начало отсчёта общего времени выполнения (слот "default"). Вызывается в main.php
 * saveTime(true)
 *   закончить отсчёт общего времени выполнения (слот "default")
 * saveTime(true, 'all')
 *   закончить отсчёт общего времени выполнения
 *   и вернуть всё время накопленное в слоте "default"
 * saveTime(true, 'current')
 *   закончить отсчёт общего времени выполнения
 *   и вернуть время накопленное за последнюю сессию подсчёта в слоте "default"
 *
 * saveTime('sql')
 *   начало отсчёта общего времени выполнения в слот "sql"
 * saveTime('sql', true)
 *   закончить отсчёт времени выполнения в слоте "sql"
 * saveTime('sql', true, 'all')
 *   закончить отсчёт общего времени выполнения
 *   и вернуть всё время накопленное в слоте "sql"
 * saveTime('sql', true, 'current')
 *   закончить отсчёт общего времени выполнения
 *   и вернуть время накопленное за последнюю сессию подсчёта в слоте "sql"
 *
 * @param false $stop
 * @param null $return
 * @return bool|float
 */
function saveTime() {
	$stop = false;
	$return = null;
	$args = func_get_args();
	$name = 'default';
	if (array_key_exists(0, $args)) {
		if (is_string($args[0])) {
			$name = $args[0];
			if (array_key_exists(1, $args)) {
				$stop = $args[1];
				if (array_key_exists(2, $args)) {
					$return = $args[2];
				}
			}
		} else {
			$stop = (bool)$args[0];
			if (array_key_exists(1, $args)) {
				$return = $args[1];
			}
		}
	}

	$mt = microtime(true);

	static $depot = [];
	if (!array_key_exists($name, $depot)) {
		$depot[$name] = [
			'all' => 0,
			'current' => $mt,
		];
	}

	$current = $depot[$name]['current'];

	if ($current === 0 && $stop === true && $return !== 'all') {
		return false;
	}

	$diff = $mt - $current;
	if ($diff <= PHP_FLOAT_EPSILON) {
		$diff = 0;
	}

	if ($stop === true && $current > 0) {
		$depot[$name]['all'] += $diff;
		$depot[$name]['current'] = $mt;
	} elseif ($stop === false) {
		$depot[$name]['current'] = $mt;
	}

	if ($return === 'current') {
		$ret = $diff;
	} elseif ($return === 'all') {
		$ret = $depot[$name]['all'];
	} else {
		return true;
	}
	return round($ret, 6);
}

function executedFromBrowser() {
	if (PHP_SAPI === 'cli') {
		return false;
	}
	if (!empty($_SERVER['DOCUMENT_ROOT'])) {
		return true;
	}
	return false;
}

/**
 * Ensure array contain all keys
 * @param array $ar Array
 * @param array $keys Keys
 * @return bool
 */
function arCheck(array $ar, array $keys): bool {
	foreach($keys as $key) {
		if (!array_key_exists($key, $ar)) {
			return false;
		}
	}
	return true;
}

/**
 * Checks is $v an float or can be converted to it
 * @param $v
 * @return bool
 */
function isFloat($v): bool {
	return is_int($v) || is_float($v) ||
		(is_string($v) && preg_match('~^[-,.0-9]+$~', $v) > 0);
}

/**
 * Checks is $v an int or can be converted to it
 * @param $v
 * @return bool
 */
function isInt($v): bool {
	return is_int($v) ||
		(is_string($v) && preg_match('~^\d+$~', $v) > 0);
}

/**
 * Checks is $v an unsigned int or can be converted to it
 * @param mixed $v
 * @param int $min Minimum value. 0 by default
 * @return bool
 */
function isId($v, int $min = 0): bool {
	if (is_int($v)) {
		return (int) $v >= $min;
	}
	if (!is_string($v)) {
		return false;
	}
	if (preg_match('~^\d+$~', $v) < 1) {
		return false;
	}
	return (int) $v >= $min;
}

/**
 * Checks is $v an float or can be converted to it. If so, convert to float
 * @param $v
 * @return float|false
 */
function toFloat($v) {
	if (!isFloat($v)) {
		return false;
	}
	if (is_string($v)) {
		static $LOCALE;
		if (null === $LOCALE) {
			$LOCALE = localeconv();
		}
		$dot = $LOCALE['decimal_point'];
		$comma = ($dot === '.') ? ',' : '.';
		$v = str_replace($comma, $dot, $v);
	}
	return (float) $v;
}

function toInt($val): int {
	return (int) $val;
}

function curlGet(string $url) {
	try {
		$ch = curl_init($url); // such as http://example.com/example.xml
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);

		if (false === $data) {
			logErr(__METHOD__, 'failed CURL request for url ' . $url);
			return false;
		}

		return $data;
	} catch (Exception $e) {
		logErr(__METHOD__, 'failed CURL request for url ' . $url . PHP_EOL .
			'  exception: ' . $e->getMessage());
		return false;
	}
} // CURL_GET

/**
 * Read $max lines from end of the file $path
 *
 * @param string $path Path to file
 * @param int $max Maximum number of lines to read
 * @param string $separator Separator characters
 * @param int $bufferSize Read buffer size
 * @return Generator|void
 */
function reverseFileRead(string $path, int $max = 0, string $separator = PHP_EOL, int $bufferSize = 4096): Generator {

	if (!file_exists($path)) {
		return;
	}

	$filesize = filesize($path);
	if ($filesize === 0) {
		return;
	}

	$fh = fopen($path, 'rb');
	if (false === $fh) {
		return;
	}

	$pos = $filesize;
	$buffer = [];
	$key = -1;

	while(true) {

		$rest = count($buffer) === 1 ? $buffer[0] : '';
		// Size and seek
		$sz = (($pos - $bufferSize) < 0) ? $pos : $bufferSize;
		$pos -= $sz;
		fseek($fh, $pos);


		// Read / split to lines
		$raw = fread($fh, $sz);
		$buffer = explode($separator, $raw . $rest);

		// There is something in buffer, get last one
		while (count($buffer) > 1) {
			++$key;
			$value = array_pop($buffer);
			if ($key === 0 && empty($value)) {
				--$key;
				continue; // skip last empty line
			}
			yield $key => $value;
			if ($max > 0 && $key >= $max) {
				return;
			}
		}

		if ($pos > 0) { // Not reach start of file
			continue;
		}

		$nofLines = count($buffer);
		if ($nofLines > 0) {
			++$key;
			$value = array_pop($buffer);
			fclose($fh);
			yield $key => $value;
			return;
		}

	}
}

function formatCurrency(float $num): string {
	// &#8239; -- unicode narrow non-breaking space -- не работает на iOs
	// &#8381; -- unicode RUR
	//return number_format($num, 0, '.', '&#8239;'). '&#8239;&#8381;';
	return number_format($num, 0, '.', '&nbsp;'). '&nbsp;&#8381;';
}

function flatPhone(string $phone, bool $allow8 = false) {
	$strNumber = preg_replace('/\D+/', '', trim($phone));
	if (!$allow8 && 8 === (int) $strNumber[0]) {
		$strNumber[0] = '7';
	}
	return $strNumber;
}

function formatPhone(string $phone): string {
	$normalized = preg_replace('/\D+/', '', trim($phone));
	$len = strlen($normalized);
	if ($len < 11 || $len > 16) {
		return $phone;
	}
	$countryCodeLen = 1;
	if ($len > 11) {
		$countryCodeLen = 2;
	}
	$blockNumLen = 7;

	$matches = [];
	preg_match('/^(\d{' . $countryCodeLen . '})(\d+)(\d{' . $blockNumLen . '})$/', $normalized, $matches);

	$cityCode = $matches[2];
	if (strlen($cityCode) > 3) {
		$cityCode = phoneBlocks($matches[2]);
	}
	if ((int)$matches[1] === 8) {
		$firstDigit = 8;
		$sign = '';
	} else {
		$firstDigit = 7;
		$sign = '+';
	}
	return $sign . $firstDigit . '-' . $cityCode . '-' . phoneBlocks($matches[3]);
}

function verifyPhone($v): bool {
	$number = flatPhone($v);
	return preg_match('/^\d{11,16}$/', $number) === 1;
}

// Make XX-XX ... | XXX-XX-XX ...
function phoneBlocks($number): string {
	$add = '';
	if (strlen($number) % 2) {
		$add = $number[ 0];
		$add .= (strlen($number) <= 5 ? '-': '');
		$number = substr($number, 1);
	}
	return $add.implode('-', str_split($number, 2));
}

function getTimeZoneTitle(int $tzSeconds, int $relative = 0): string {
	$tzSeconds -= $relative;

	$tzRest = $tzSeconds % 3600;
	$tzHours = abs((int)(($tzSeconds - $tzRest) / 3600));
	$tzMinutes = (int)($tzRest / 60);

	return ($tzSeconds < 0 ? '-' : '+') . $tzHours . ':' . ($tzMinutes < 1 ? 0 : '') . $tzMinutes;
}

function dmp(string $ctx, ...$args) {
	if (!IS_CLI) {
		echo '<pre class="dump">';
	} else {
		echo PHP_EOL;
	}
	echo vsl(PHP_EOL, 96, $ctx . ':', ...$args);
	if (!IS_CLI) {
		echo '</pre>';
	}
	echo PHP_EOL;
}
