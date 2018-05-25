<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

use \stdClass;

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

    /**
     * Set loggedIn
     *
     * @param boolean $loggedIn Is loggedIn
     * @return void
     */
    public function setLoggedIn(bool $loggedIn): void
    {
        $_SESSION["loggedIn"] = $loggedIn;
    }

    /**
     * Set the user
     *
     * @param stdClass $user User object
     * @return void
     */
    public function setUser(stdClass $user): void
    {
        $_SESSION["user"] = $user;
    }

}
