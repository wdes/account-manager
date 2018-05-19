<?php

namespace AccountManager\Twig;
/**
 * Load
 */
class Load {
    public static $cacheFS;
    public static function load(string $tmpDir): void {
        global $loader, $twig;
        Load::$cacheFS = new \Twig_Cache_Filesystem($tmpDir);
        $loader = new \Twig_Loader_Filesystem(TEMPLATE_DIR);
        $twig = new \Twig_Environment($loader, array(
            'cache' => Load::$cacheFS,
            'debug' => true
        ));
        $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
            // implement whatever logic you need to determine the asset path

            return sprintf('public/assets/%s', ltrim($asset, '/'));
        }));

        $twig->addExtension(new \AccountManager\Twig\I18nExtension());
        $twig->addGlobal('_session', @$_SESSION);
        $twig->addGlobal('_post', $_POST);
        $twig->addGlobal('_get', $_GET);
    }
}
