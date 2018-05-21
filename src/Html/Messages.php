<?php
declare(strict_types=1);

namespace AccountManager\Html;

use \AccountManager\Twig\Load;

class Messages {
    protected $messages;

    public function __construct() {
        $this->messages = array();
    }
    public function add(Message $message): void {
        $this->messages[] = $message;
    }
    public function render(): string {
        return Load::getTwig()->render('html/messages.twig',array('messages'=>$this->messages));
    }
}
