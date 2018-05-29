<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
$identities = new \AccountManager\Identities($db, $auth);
if ($auth->isLoggedIn()) {
    echo $twig->render('pages/identities.twig', array('identities' => $identities));
} else {
    header('Location: login.php');
}
