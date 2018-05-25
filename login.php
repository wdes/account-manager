<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
use \AccountManager\Html\Message;
use \AccountManager\Html\Messages;
use \AccountManager\Authentification\Users;

$users      = new Users($db);
$messages   = new Messages();
$loggedIn = false;

if (isset($_POST["login"])) {
    if (empty($_POST['password'])) {
        $messages->add(new Message(_gettext("Empty password"), Message::DANGER));
    } elseif (empty($_POST['username'])) {
        $messages->add(new Message(_gettext("Empty username"), Message::DANGER));
    } elseif (empty($_POST['password']) === false && empty($_POST['username']) === false
    ) {
        $user = $users->login($_POST["username"], hash('sha256', $_POST["password"], false));
        if ($user->success) {
            $messages->add(new Message(_gettext("Welcome !"), Message::INFO));
            $auth->setLoggedIn(true);
            $auth->setUser($user);
            $loggedIn = true;
        } else {
            $messages->add(new Message(_gettext("Password or username invalid !"), Message::DANGER));
        }
    }
}

echo $twig->render(
    'pages/login.twig',
    array('messages' => $messages->render(), 'loggedIn' => $loggedIn)
);
