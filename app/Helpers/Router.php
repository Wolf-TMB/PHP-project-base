<?php

namespace App\Helpers;

use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;

class Router {

	private RouteCollector $router;

	public function __construct() {
		$this->router = new RouteCollector();
	}

    /**
     * @param string $url URL, на который необходимо совершить переадресацию
     */
	public static function redirect(string $url) {
		header('Location: ' . $url);
	}

	public function get($route, $handler, $filters = []) {
		$this->router->get($route, $handler, $filters);
	}

	public function post($route, $handler, $filters = []) {
		$this->router->post($route, $handler, $filters);
	}

	public function filter($name, $handler) {
		$this->router->filter($name, $handler);
	}

	public function group($filters, $handler) {
		$this->router->group($filters, $handler);
	}

	/**
	 * @throws HttpMethodNotAllowedException
	 * @throws HttpRouteNotFoundException
	 */
	public function run() {
		$dispatcher = new Dispatcher($this->router->getData());
		$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		echo $response;
	}

}