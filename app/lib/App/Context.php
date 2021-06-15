<?php


namespace App;


class Context {

	public App $app;

	public string $PATH;

	public array $GET;
	public array $POST;

	public array $URL;
	public array $PARAMS;

	/**
	 * Context constructor.
	 * @param App $app
	 */
	public function __construct(App $app) {
		$this->app = $app;

		if (IS_CLI) {
			$this->GET = [];
			$this->POST = [];
			return;
		}

		$this->GET = $_GET;
		$this->POST = $_POST;

		$parts = [];
		$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if (!empty($path)) {
			$path = trim($path, '/');
			$path = rawurldecode ($path);
			$parts = explode('/', $path);
			foreach ($parts as $k => $v) {
				if (empty($v)) {
					unset($parts[$k]);
				}
			}
			$path = '/' . $path . '/';
		}
		$this->PATH = $path;
		$this->URL = $parts;

		$this->scripts = [];
	}

}
