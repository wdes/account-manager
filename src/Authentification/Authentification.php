<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

/**
 * Authentification
 */
class Authentification
{
    protected $loggedIn = false;

    /**
     * Create an Authentification
     */
    public function __construct()
    {
        session_start();
        $this->check();
    }

    /**
     * Check and set loggedIn state
     *
     * @return void
     */
    private function check(): void
    {
        $this->loggedIn = (isset($_SESSION["loggedIn"]));
    }

    /**
     * Is logged in ?
     *
     * @return bool loggedIn/Out
     */
    public function isLoggedIn()
    {
        $this->check();
        return $this->loggedIn;
    }

}
