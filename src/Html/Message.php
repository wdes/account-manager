<?php
declare(strict_types = 1);
namespace AccountManager\Html;

class Message
{
    public const PRIMARY   = "primary";
    public const SECONDARY = "secondary";
    public const SUCCESS   = "success";
    public const DANGER    = "danger";
    public const WARNING   = "warning";
    public const INFO      = "info";
    public const LIGHT     = "light";
    public const DARK      = "dark";
    public $message;
    public $severity;

    /**
     * Create a message
     *
     * @param string $message  the message
     * @param string $severity the severity
     */
    public function __construct(string $message, string $severity=self::PRIMARY)
    {
        $this->message  = $message;
        $this->severity = $severity;
    }

}
