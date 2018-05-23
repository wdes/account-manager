<?php

namespace AccountManager\Authentification;
use \AccountManager\Database;

/**
 * Users
 */
class Users {
    protected $db;
    public function __construct(Database $db){
        $this->db = $db;
    }
    public function register(string $username, string $email, string $password): bool {
        return $this->db->Insert("users",
        array(
            "username"=>$username,
            "password"=>$password,
            "email"=>$email
        ));
    }
    public function usernameAvailable(string $username): bool {
        return $this->db->Select("users",
        array(
            "username"=>$username
        ));
    }
}
