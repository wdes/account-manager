<?php
declare(strict_types=1);

namespace AccountManager\Html;

class Messages {
    protected $messages;

    public function __construct() {
        $this->messages = array();
    }
    public function add(Message $message): void {
        $this->messages[] = $message;
    }
    public function render(): string {
        return \AccountManager\Twig\Load::getTwig()->render('html/messages.twig',array('messages'=>$this->messages));
    }
}
