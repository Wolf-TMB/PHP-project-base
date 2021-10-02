<?php

namespace App\Http\Controllers;

use App\Constants;
use JetBrains\PhpStorm\NoReturn;
use App\Exceptions\RenderException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;

class BaseController {
    /**
     * @param string  $type  Какие данные возвращать. <br>
     *                       Допустимо: <br>
     *                          Constants::REQUEST_TYPE_POST <br>
     *                          Constants::REQUEST_TYPE_GET <br>
     *                          Constants::REQUEST_TYPE_ANY
     *
     * @return array
     */
    protected function getRequestData(string $type): array {
        $post = $get = [];
        foreach ($_POST as $key => $value) {
            $post[strip_tags(trim($key))] = strip_tags(trim($value));
        }
        foreach ($_GET as $key => $value) {
            $get[strip_tags(trim($key))] = strip_tags(trim($value));
        }
        return match ($type) {
            Constants::REQUEST_TYPE_POST => $post,
            Constants::REQUEST_TYPE_GET => $get,
            Constants::REQUEST_TYPE_ANY => array_merge($post, $get),
            default => [],
        };
    }

    /**
     * @param string  $template  Шаблон, который будет отрисован.
     * @param string  $layout    Слой, который будет использован при отрисовке шаблона
     * @param array   $params    Переменные, которые будут переданы в шаблон в виде 'var_name' => 'value'. Необязательно, по умолчанию: [].
     * @param string  $dir       Папка для поиска элементов, необходимых для рендера. Необязательно, по умолчанию: Constants::DEFAULT_RENDER_DIR.
     * @param bool    $notfound  Если истинно, то при отсутствии шаблона будет выдано исключение HttpRouteNotFoundException. Необязательно, по умолчанию: false.
     *
     * @throws RenderException
     * @throws HttpRouteNotFoundException
     */
	#[NoReturn] protected function render(string $template, string $layout, array $params = [], string $dir = Constants::DEFAULT_RENDER_DIR, bool $notfound = false) {
		global $app;
		$app::$render->renderPage($template, $layout, $params, $dir, $notfound);
	}

	/**
     * @param string $url URL, на который необходимо совершить переадресацию
	*/
	protected function redirect(string $url) {
		global $app;
		$app::$router::redirect($url);
	}
}