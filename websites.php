<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';

if ($auth->isLoggedIn()) {

    $websites    = new \AccountManager\Websites($db, $auth);
    $renderArray = array('websites' => $websites);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $_POST['cbd'] = isset($_POST['cbd']) ? (bool) $_POST['cbd'] : false;
                $websites->add((int) $_POST['identityId'], $_POST['domainName'], $_POST['cbd']);
                break;
        }
    }

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'add':
                $identities                = new \AccountManager\Identities($db, $auth);
                $renderArray['identities'] = $identities;
                break;
        }
    }
    echo $twig->render('pages/websites/index.twig', $renderArray);
} else {
    header('Location: login.php');
}
