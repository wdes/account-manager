<?php
require_once __DIR__.'/vendor/autoload.php';
PhpMyAdmin\MoTranslator\Loader::loadFunctions();
// Configure
_setlocale(LC_MESSAGES, 'fr');
_textdomain('account-manager');
_bindtextdomain('account-manager', __DIR__ . '/locale/');
_bind_textdomain_codeset('account-manager', 'UTF-8');

$loader = new Twig_Loader_Filesystem(__DIR__.'/src/templates');
$twig = new Twig_Environment($loader, array(
    'cache' => __DIR__.'/tmp/twig',
    'debug' => true
));
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    // implement whatever logic you need to determine the asset path

    return sprintf('public/assets/%s', ltrim($asset, '/'));
}));

$twig->addExtension(new AccountManager\Twig\I18nExtension());

echo $twig->render('index.twig', array('locale' => Locale::getDefault()));

?>