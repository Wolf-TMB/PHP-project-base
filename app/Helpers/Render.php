<?php

namespace App\Helpers;

use App\Constants;
use JetBrains\PhpStorm\NoReturn;
use App\Exceptions\RenderException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;

class Render {
    #[NoReturn] public static function fatalError(\Exception $exception) {
        $code = $exception->getCode();
        $msg = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        ob_end_clean();
        ob_start();
        header('Content-type: text/html; charset=utf-8');
        $trace = preg_replace("/PDO->__construct\(.*\)/", 'PDO->__construct(...data)', $trace);
        echo <<<ERR
            <!doctype html> <html lang="ru"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"> <meta http-equiv="X-UA-Compatible" content="ie=edge"> <title>Fatal error</title> <link rel="stylesheet" href="/resources/css/style.css"> </head> <body> <div class="container-fluid"> <div class="p-4" style="z-index: 1000000; background-color: white"> <h3>Fatal error! Script is dead</h3> Code: $code <br> Message: $msg <br> File: $file <br> Line: $line <br> <pre>$trace</pre> </div> </div> </body> </html>
        ERR;
        ob_end_flush();
        die();
    }

    /**
     * @throws RenderException
     * @throws HttpRouteNotFoundException
     */
    #[NoReturn] public function renderPage($template_name, $layout_name, $args = [], $other_path = Constants::DEFAULT_RENDER_DIR, $notfound = false) {
        global $app;
        extract($args);
        $path = $app::$site_path . $app::$config::PATH_RESOURCES . $app::$config::PATH_VIEWS . $other_path . '/' . $app::$config::PATH_LAYOUTS . $layout_name . '.layout.php';
        if (!file_exists($path)) throw new RenderException('File ' . $layout_name . '.layout.php not found in ' . $path, true);
        require $path;
        $layout = ob_get_clean();
        $layout = $this->loadTemplates($layout, $template_name, 0, $args, $notfound, $other_path);
        print($layout);
    }

    /**
     * @throws RenderException
     * @throws HttpRouteNotFoundException
     */
    public function loadTemplates($layout, $template_name, $iteration, $args, $notfound, $other_path) {
        global $app;
        $iteration++;
        if ($iteration > Constants::RENDER_MAX_ITERATION) {
            throw new RenderException(Config::ERROR_RENDER_MAX_ITERATION_COUNT, true);
        }
        preg_match_all("/\{\{[a-zA-Z_0-9:.]+\}\}/", $layout, $layout_tags);
        $layout_tags = $layout_tags[0];
        foreach ($layout_tags as $tag) {
            preg_match('/[a-zA-Z_0-9:.]+/', $tag, $tag_content);
            $tag_content = $tag_content[0];
            if ($tag_content === 'page_content') {
                ob_start();
                $path = $app::$site_path . $app::$config::PATH_RESOURCES . $app::$config::PATH_VIEWS . $other_path . '/' . $app::$config::PATH_TEMPLATES . $template_name . '.php';

                if (!file_exists($path)) {
                    if ($notfound) {
                        throw new HttpRouteNotFoundException();
                    } else {
                        throw new RenderException('File ' . $template_name . '.php not found in ' . $path, true);
                    }
                }
                extract($args);
                require $path;
                $template = ob_get_clean();
                $layout = str_replace($tag, $template, $layout);

            } else {
                $exp_tag_content = explode(':', $tag_content);
                $type = $exp_tag_content[0];
                $block_name = $exp_tag_content[1];
                if ($type === 'block') {
                    ob_start();
                    $path = $app::$site_path . $app::$config::PATH_RESOURCES . $app::$config::PATH_VIEWS . $other_path . '/' . $app::$config::PATH_BLOCKS . $block_name . '.php';
                    if (!file_exists($path)) throw new RenderException('File ' . $block_name . '.php not found in ' . $path, true);
                    require $path;
                    $block = ob_get_clean();
                    $layout = str_replace($tag, $block, $layout);
                }
                if ($type === 'cache') {
                    ob_start();
                    $block_name = str_replace('.', '/', $block_name);
                    $path = $app::$site_path . $app::$config::PATH_STORAGE . $app::$config::PATH_CACHE . $block_name . '.html';

                    if (!file_exists($path)) throw new RenderException('File ' . $block_name . '.html not found in ' . $path, true);
                    require $path;
                    $block = ob_get_clean();
                    $layout = str_replace($tag, $block, $layout);
                }
            }
        }
        preg_match_all("/\{\{[a-z_0-9:]+\}\}/", $layout, $layout_tags);
        $layout_tags = $layout_tags[0];
        if (count($layout_tags) > 0) {
            return $this->loadTemplates($layout, $template_name, $iteration, $args, $notfound, $other_path);
        }
        return $layout;
    }

}