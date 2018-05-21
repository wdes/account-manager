<?php
declare(strict_types=1);

namespace AccountManager\Html;

class Message {
    public const primary = "primary";
    public const secondary = "secondary";
    public const success = "success";
    public const danger = "danger";
    public const warning = "warning";
    public const info = "info";
    public const light = "light";
    public const dark = "dark";
    public $message;
    public $severity;
    /**
     * new Message
     *
     * @param string $message the message
     * @param string $severity the severity
     */
    public function __construct(string $message, $severity=self::primary) {
        $this->message = $message;
        $this->severity = $severity;
    }
}
