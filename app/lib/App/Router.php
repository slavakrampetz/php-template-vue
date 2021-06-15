<?php


namespace App;


class Router {

	public App $app;
	private array $routes;

	/**
	 * Router constructor.
	 * @param App $app
	 */
	public function __construct(App $app) {
		$this->app = $app;
	}

	public function init(): void {
		$list = include PRJ_APP_ROOT . 'routes.php';
		if (empty($list)) {
			return;
		}
		$this->routes = $list;
	}

	public function resolve(): ?Controller {

		$ctx = &$this->app->context;
		$urlParts = $ctx->URL;

		$urlCount = count($urlParts);
		$found = null;
		for ($i = $urlCount; $i >= 0; $i--) {
			$tempUrlParts = [];
			/** @noinspection ForeachInvariantsInspection */
			for ($j = 0, $countj = $i; $j < $countj; $j++) {
				$tempUrlParts[] = $urlParts[$j];
			}
			$tempUrl = implode('/', $tempUrlParts);
			if (empty($tempUrl)) {
				$tempUrl = '/';
			} else {
				$tempUrl = '/' . $tempUrl . '/';
			}
			if (array_key_exists($tempUrl, $this->routes)) {
				$res = $this->routes[$tempUrl];

				// Controller
				if (!is_array($res)) {
					$route = [
						'is_exact_url' => false,
						'class' => $res,
					];
				} else {
					$route = $res;
				}
				$urlMatches = empty($route['is_exact_url']) || $tempUrl === $ctx->PATH;
				if ($urlMatches) {
					$params = [];
					for ($k = $j; $k < $urlCount; $k++) {
						$params[] = $urlParts[$k];
					}
					if (!empty($route['class'])) {
						$found = $route;
						$ctx->PARAMS = $params;
						break;
					}
				}
			}
		}

		if ($found === null) {
			return null;
		}

		// Look for controller
		$className = 'Controller\\' . $found['class'];
		if (!class_exists($className)) {
			return null;
		}
		return new $className($this->app);
	}
}
