<?php
require_once __DIR__.'/../vendor/autoload.php';
\PhpMyAdmin\MoTranslator\Loader::loadFunctions();
// Configure
_setlocale(LC_MESSAGES, 'fr');
_textdomain('account-manager');
_bindtextdomain('account-manager', __DIR__ . '/../locale/');
_bind_textdomain_codeset('account-manager', 'UTF-8');

\AccountManager\Twig\Load::load(__DIR__.'/../tmp/twig');

$auth = new \AccountManager\Authentification\Authentification();