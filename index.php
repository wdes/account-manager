<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
if ($auth->isLoggedIn()) {
    $websites   = new \AccountManager\Websites($db, $auth);
    $identities = new \AccountManager\Identities($db, $auth);
    echo $twig->render('pages/index.twig', array('websites' => $websites, 'identities' => $identities));
} else {
    header('Location: login.php');
}
