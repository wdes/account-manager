<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
if ($auth->isLoggedIn()) {
    echo $twig->render('pages/index.twig', array());
} else {
    header('Location: login.php');
}
