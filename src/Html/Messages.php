<?php
declare(strict_types = 1);
namespace AccountManager\Html;

use \AccountManager\Twig\Load;

class Messages
{
    protected $messages;

    /**
     * Create a messager container
     */
    public function __construct()
    {
        $this->messages = array();
    }

    /**
     * Add a Message
     *
     * @param Message $message A Message
     * @return void
     */
    public function add(Message $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * Render the messages
     *
     * @return string html render
     */
    public function render(): string
    {
        return Load::getTwig()->render('html/messages.twig', array('messages' => $this->messages));
    }

}
