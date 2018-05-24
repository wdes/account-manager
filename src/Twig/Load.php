<?php
declare(strict_types = 1);
namespace AccountManager\Twig;

use \Twig_Environment;

class Load
{
    public static $cacheFS;
    public static $twig;

    /**
     * Initialize static twig
     *
     * @param string $tmpDir The directory for temporary files
     * @return void
     */
    public static function init(string $tmpDir): void
    {
        global $loader, $twig;
        Load::$cacheFS = new \Twig_Cache_Filesystem($tmpDir);
        $loader        = new \Twig_Loader_Filesystem(TEMPLATE_DIR);
        $twig          = new \Twig_Environment(
            $loader, array(
            'cache' => Load::$cacheFS,
            'debug' => true
            )
        );
        $twig->addFunction(
            new \Twig_SimpleFunction(
                'asset', function ($asset) {
                    // implement whatever logic you need to determine the asset path

                    return sprintf('public/assets/%s', ltrim($asset, '/'));
                }
            )
        );
        $twig->addFunction(
            new \Twig_SimpleFunction(
                'html', function ($code) {
                    return new \Twig_Markup($code, "utf-8");
                }
            )
        );

        $twig->addExtension(new \AccountManager\Twig\I18nExtension());
        $twig->addGlobal('_session', @$_SESSION);
        $twig->addGlobal('_post', $_POST);
        $twig->addGlobal('_get', $_GET);
        $twig->addGlobal('locale', \Locale::getDefault());
        Load::$twig = $twig;
    }

    /**
     * Get static twig object
     *
     * @return Twig_Environment
     */
    public static function getTwig(): Twig_Environment
    {
        return Load::$twig;
    }

}
