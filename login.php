<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
echo $twig->render('pages/login.twig', array());
