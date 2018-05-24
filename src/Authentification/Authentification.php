<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

/**
 * Authentification
 */
class Authentification
{
    protected $loggedIn = false;

    public function __construct()
    {
        session_start();
        $this->check();
    }

    private function check(): void
    {
        $this->loggedIn = (isset($_SESSION["loggedIn"]));
    }

    public function isLoggedIn()
    {
        $this->check();
        return $this->loggedIn;
    }

}
