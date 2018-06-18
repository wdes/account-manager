<?php
declare(strict_types = 1);
namespace AccountManager\Twig;

use \Twig_Environment;
use \Twig_Cache_Filesystem;
use \Twig_Loader_Filesystem;
use \Twig_SimpleFunction;
use \Twig_Markup;
use \Locale;
use \Wdes\PIL\Twig\Extension\I18n as I18nExtension;

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
        global $twig;
        Load::$cacheFS              = new Twig_Cache_Filesystem($tmpDir);
        $loader                     = new Twig_Loader_Filesystem(TEMPLATE_DIR);
        $moReader                   = new \Wdes\PIL\plugins\MoReader(
            array("localeDir" => LOCALE_DIR)
        );
        $data                       = $moReader->readFile(LOCALE_DIR."fr/LC_MESSAGES/account-manager.mo");
        \Wdes\PIL\Launcher::$plugin = $moReader;
        $twig                       = new Twig_Environment(
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
            new Twig_SimpleFunction(
                'html', function ($code) {
                    return new Twig_Markup($code, "utf-8");
                }
            )
        );
        $twig->addFunction(
            new Twig_SimpleFunction(
                'toJson', function ($code) {
                    return new Twig_Markup(json_encode($code, JSON_PRETTY_PRINT), "utf-8");
                }
            )
        );

        $twig->addExtension(new I18nExtension());
        $twig->addGlobal('_post', $_POST);
        $twig->addGlobal('_get', $_GET);
        $twig->addGlobal('locale', Locale::getDefault());
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

    /**
     * Get static twig cache FS object
     *
     * @return Twig_Cache_Filesystem
     */
    public static function getTwigCacheFS(): Twig_Cache_Filesystem
    {
        return Load::$cacheFS;
    }

}
