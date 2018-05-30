<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
$identities = new \AccountManager\Identities($db, $auth);
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $identities->add((int) $_POST['type'], $_POST['value']);
            break;
    }
}
if ($auth->isLoggedIn()) {
    echo $twig->render('pages/identities/index.twig', array('identities' => $identities));
} else {
    header('Location: login.php');
}
