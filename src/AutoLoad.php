<?php
declare(strict_types = 1);
require_once __DIR__.'/bootstrap.php';
// Configure
//_setlocale(LC_MESSAGES, 'fr');
//_textdomain('account-manager');
//_bindtextdomain('account-manager', LOCALE_DIR);
//_bind_textdomain_codeset('account-manager', 'UTF-8');

\AccountManager\Twig\Load::init(TWIG_TMP);

$auth   = new \AccountManager\Authentification\Authentification();
$config = new \AccountManager\Config(PROJECT_ROOT);
$db     = new \AccountManager\Database($config);

\AccountManager\Twig\Load::getTwig()->addGlobal('_auth', $auth);
