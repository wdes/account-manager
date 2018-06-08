<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';

$network     = new \AccountManager\Network($db, $auth);
$networkData = $network->buildNetwork();
$nodes       = $networkData->nodes;
$edges       = $networkData->edges;
$renderArray = array('nodes' => $nodes, 'edges' => $edges);

if ($auth->isLoggedIn()) {
    echo $twig->render('pages/network/index.twig', $renderArray);
} else {
    header('Location: login.php');
}
