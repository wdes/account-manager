<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
$websites = new \AccountManager\Websites($db, $auth);
if ($auth->isLoggedIn()) {
    echo $twig->render('pages/websites.twig', array('websites'=>$websites));
} else {
    header('Location: login.php');
}
