<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
$websites   = new \AccountManager\Websites($db, $auth);
$identities = new \AccountManager\Identities($db, $auth);
if ($auth->isLoggedIn()) {
    echo $twig->render('pages/index.twig', array('websites' => $websites, 'identities' => $identities));
} else {
    header('Location: login.php');
}
