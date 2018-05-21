<?php
declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Constants.php';
set_include_path(SRC_DIR);
\PhpMyAdmin\MoTranslator\Loader::loadFunctions();
// Configure
_setlocale(LC_MESSAGES, 'fr');
_textdomain('account-manager');
_bindtextdomain('account-manager', LOCALE_DIR);
_bind_textdomain_codeset('account-manager', 'UTF-8');

\AccountManager\Twig\Load::load(TWIG_TMP);

$auth = new \AccountManager\Authentification\Authentification();
$config = new \AccountManager\Config(PROJECT_ROOT.".env");
