<?php

namespace App;

class Constants {
    /**
     *  Системные константы, крайне не рекомендую здесь что-то изменять.
    */
    public const REQUEST_TYPE_POST = 'post';
    public const REQUEST_TYPE_GET = 'get';
    public const REQUEST_TYPE_ANY = 'any';

    public const RENDER_MAX_ITERATION = 10;
    public const DEFAULT_RENDER_DIR = 'web';
	public const URI_404 = '/404';
	public const URI_405 = '/405';
	public const URI_SECURITY = '/security';
}