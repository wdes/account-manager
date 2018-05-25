<?php
declare(strict_types = 1);
require_once __DIR__.'/src/AutoLoad.php';
use \AccountManager\Html\Message;
use \AccountManager\Html\Messages;
use \AccountManager\Authentification\Users;

$users      = new Users($db);
$messages   = new Messages();
$registered = false;

if (isset($_POST["register"])) {
    if (empty($_POST['password1'])) {
        $messages->add(new Message(_gettext("Empty password"), Message::DANGER));
    } elseif (empty($_POST['password2'])) {
        $messages->add(new Message(_gettext("Empty confirmation password"), Message::DANGER));
    } elseif (empty($_POST['username'])) {
        $messages->add(new Message(_gettext("Empty username"), Message::DANGER));
    } elseif (empty($_POST['password1']) === false
        && empty($_POST['password2']) === false
        && empty($_POST['username']) === false
    ) {
        if ($users->usernameAvailable($_POST['username'])) {
            if ($_POST['password1'] === $_POST['password2']) {
                $registered = $users->register($_POST["username"], $_POST["email"], hash('sha256', $_POST["password1"], false));
            } else {
                $messages->add(new Message(_gettext("Passwords are different"), Message::DANGER));
            }
        } else {
            $messages->add(new Message(_gettext("Username already taken :("), Message::INFO));
        }
    }
}

echo $twig->render(
    'pages/register.twig',
    array('messages' => $messages->render(), 'registered' => $registered)
);
