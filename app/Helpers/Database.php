<?php

namespace App\Helpers;

use PDO;

class Database {
	public function getConnection(): PDO {
		global $app;
		return new PDO($app::$config::DB_DRIVER.':host='. $app::$config::DB_HOST . ';port=' . $app::$config::DB_PORT . ';dbname=' . $app::$config::DB_NAME . ';charset=utf8;', $app::$config::DB_USER, $app::$config::DB_PASS, array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
		));
	}
}