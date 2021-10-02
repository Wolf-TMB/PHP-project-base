<?php

namespace App\Http\Controllers;

use App\Exceptions\RenderException;
use JetBrains\PhpStorm\NoReturn;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;

class WebController extends BaseController {
	/**
	 * @throws RenderException
	 * @throws HttpRouteNotFoundException
	 */
	#[NoReturn] public function getIndex() {
		$this->render(
			'index',
            'main',
            array(
                'title' => 'Главная'
            )
		);
	}

}