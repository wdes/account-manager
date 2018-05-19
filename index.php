<?php
require_once __DIR__.'/src/AutoLoad.php';
if ($auth->isLoggedIn()) {
    echo $twig->render('index.twig', array('locale' => Locale::getDefault()));
} else {
    header('Location: login.php');
}

?>