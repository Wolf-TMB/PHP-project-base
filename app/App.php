<?php

namespace App;

use PDO;
use Exception;
use App\Models\Users;
use App\Helpers\Router;
use App\Helpers\Render;
use App\Helpers\Sender;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Database;
use App\Helpers\ErrorCatcher;
use App\Exceptions\BaseException;
use App\Exceptions\RoutesFileNotFound;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;

class App {
	public static string $site_path;
	public static array $parse_uri;
	public static string $redirect_url;
	public static string $method;
	public static Config $config;
	public static Constants $const;
	public static Session $session;
	public static Security $security;
	public static Sender $sender;
	public static Router $router;
	public static Render $render;
	public static PDO $db;

	public static Users $users;

	private static Database $database;

	public function __construct() {
		new ErrorCatcher();

		$this->initApp();
	}

	private function initApp() {
		ob_start();

		self::$config = new Config();
		self::$const = new Constants();


		self::$site_path = $_SERVER['DOCUMENT_ROOT'] . self::$config::PATH_BASE;
		self::$parse_uri = array_values(array_diff(explode('/', parse_url($_SERVER['REQUEST_URI'])['path']), array('')));
		self::$redirect_url = parse_url($_SERVER['REQUEST_URI'])['path'];
		self::$method = match (strtolower($_SERVER['REQUEST_METHOD'])) {
			'post' => Constants::REQUEST_TYPE_POST,
			'get' => Constants::REQUEST_TYPE_GET
		};

		self::$session = new Session();
		self::$security = new Security();
		self::$sender = new Sender();
		self::$database = new Database();

		self::$router = new Router();
		self::$users = new Users();


		self::$render = new Render();

	}

	/**
	 * @throws RoutesFileNotFound
	 */
	private function loadRouterFiles() {
		foreach (self::$config::ROUTES_LIST as $routeFile) {
			$path = self::$site_path . self::$config::PATH_ROUTES . $routeFile . '.php';
			if (file_exists($path)) {
				require $path;
			} else {
				throw new RoutesFileNotFound('File ' . $path . ' not found!', true);
			}
		}
		foreach (self::$config::MIDDLEWARES_LIST as $middlewareFile) {
			$path = self::$site_path . self::$config::PATH_MIDDLEWARES . $middlewareFile . '.php';
			if (file_exists($path)) {
				require $path;
			} else {
				throw new RoutesFileNotFound('File ' . $path . ' not found!', true);
			}
		}
	}



	private function runRouter() {
		try {
			$this->loadRouterFiles();
			self::$router->run();
		} catch (HttpRouteNotFoundException) {
			if (in_array(@self::$parse_uri[0], self::$config::RESPONSE_CODE)) {
				http_response_code(404);
				die(404);
			} else {
				self::$router::redirect(self::$const::URI_404);
			}
		} catch (HttpMethodNotAllowedException) {
			if (in_array(@self::$parse_uri[0], self::$config::RESPONSE_CODE)) {
				http_response_code(405);
				die(405);
			} else {
				self::$router::redirect(self::$const::URI_405);
			}
		}  catch (BaseException $e) {
			if ($e->isFatal()) {
				Render::fatalError($e);
			}
		} catch (Exception $e) {
			Render::fatalError($e);
		}
	}

	public function runApp() {
		self::$db = self::$database->getConnection();
		self::$security->run();
		$this->runRouter();
	}
}