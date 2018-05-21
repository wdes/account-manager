<?php
require_once __DIR__.'/src/AutoLoad.php';
use \AccountManager\Html\Message as Message;
$messages = new \AccountManager\Html\Messages();
if (empty($_POST['password1'])) {
    $messages->add(new Message(_gettext("Empty password"), Message::danger));
}
if (empty($_POST['password2'])) {
    $messages->add(new Message(_gettext("Empty confirmation password"), Message::danger));
}
if (empty($_POST['password1']) === false && empty($_POST['password2']) === false) {
    if ($_POST['password1'] !== $_POST['password2']) {
        $messages->add(new Message(_gettext("Passwords are different"), Message::danger));
    }
}

echo $twig->render('pages/register.twig',
    array('messages' => $messages->render())
);
?>