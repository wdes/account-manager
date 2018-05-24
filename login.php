<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
print_r($_POST);
echo $twig->render('pages/login.twig', array());
