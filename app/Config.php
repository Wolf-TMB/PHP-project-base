<?php

namespace App;

class Config {
    /**
     *  Основные настройки приложения.
    */
    public const APP_NAME = 'PHWolf';
    public const APP_VERSION = '2.1.0';
    public const PATH_BASE = '/';
    public const LANGUAGE = 'ru';


    /**
     *  Настройка путей папки ресурсов. Используется для рендера страниц.
     *  В будущем возможна будет автозагрузка css и js файлов.
     */
    public const PATH_RESOURCES = 'resources/';
    public const PATH_CSS = 'css/';
    public const PATH_JS = 'js/';
    public const PATH_VIEWS = 'views/';
    public const PATH_BLOCKS = 'blocks/';
    public const PATH_LAYOUTS = 'layouts/';
    public const PATH_TEMPLATES = 'templates/';

    public const PATH_STORAGE = 'storage/';
    public const PATH_CACHE = 'cache/';

    /**
     *  Настройки маршрутизатора.
    */
    public const PATH_ROUTES = 'routes/';
    public const ROUTES_LIST = ['web', 'api'];
    public const PATH_MIDDLEWARES = 'routes/middlewares/';
    public const MIDDLEWARES_LIST = ['web'];
	public const RESPONSE_CODE = ['api'];



    /**
     *  Описание ошибок
     */
    public const ERROR_RENDER_MAX_ITERATION_COUNT = 'Превышено максимальные число итераций для отрисовки вложенных шаблонов.';
}