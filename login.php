<?php
require_once __DIR__.'/src/AutoLoad.php';
echo $twig->render('login.twig', array('locale' => Locale::getDefault()));
?>