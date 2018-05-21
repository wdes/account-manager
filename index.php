<?php
require_once __DIR__.'/src/AutoLoad.php';
if ($auth->isLoggedIn()) {
    echo $twig->render('pages/index.twig', array());
} else {
    header('Location: login.php');
}

?>