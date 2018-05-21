<?php
require_once __DIR__.'/src/AutoLoad.php';
print_r($_POST);
echo $twig->render('pages/login.twig', array());
?>