<?php

namespace AccountManager\Authentification;
/**
 * Authentification
 */
class Authentification {
    protected $loggedIn = false;
    public function __construct() {
        session_start();
        $this->check();
    }
    private function check() {
        if (isset($_SESSION["loggedIn"])) {
            $loggedIn = true;
        }
    }
    public function isLoggedIn() {
        return $this->loggedIn;
    }
}
