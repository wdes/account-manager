<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';

$network = new \AccountManager\Network($db, $auth);
if ($auth->isLoggedIn()) {
    $networkData = $network->buildNetwork();
    $nodes       = $networkData->nodes;
    $edges       = $networkData->edges;
    $groups      = $networkData->groups;
    $renderArray = array('nodes' => $nodes, 'edges' => $edges, 'groups' => $groups);
    echo $twig->render('pages/network/index.twig', $renderArray);
} else {
    header('Location: login.php');
}
