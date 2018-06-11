<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

use \AccountManager\Database;
use \PDO;
use \stdClass;

class Users
{
    protected $db;

    /**
     * Create an Users object
     *
     * @param Database $db The Datbase instance
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Register an user
     *
     * @param string $username The username
     * @param string $email    The E-Mail
     * @param string $password The password
     * @return bool Register status
     */
    public function register(string $username, string $email, string $password): bool
    {
        return $this->db->Insert(
            "users",
            array(
            "username" => $username,
            "password" => $password,
            "email" => $email
            )
        );
    }

    /**
     * Login user
     *
     * @param string $username The username
     * @param string $password The password
     * @return stdClass
     */
    public function login(string $username, string $password): stdClass
    {
        $res    = $this->db->Select(
            "*",
            "users",
            array(
            "username" => $username,
            "password" => $password
            )
        );
        $user   = new stdClass();
        $userDB = $res->fetch(PDO::FETCH_OBJ);
        if ($userDB !== false) {
            $user->id       = $userDB->id;
            $user->username = $userDB->username;
            $user->verified = ($userDB->verified === "1");
        }
        $user->success = $res->rowCount() === 1;
        return $user;
    }

    /**
     * Check if username is available
     *
     * @param string $username The username
     * @return bool isAvailable
     */
    public function usernameAvailable(string $username): bool
    {
        return $this->db->Exists(
            "users",
            array(
            "username" => $username
            )
        ) === false;
    }

    /**
     * Delete account
     *
     * @param string $username The username
     * @return bool deleted
     */
    public function deleteAccount(string $username): bool
    {
        return $this->db->Delete(
            "users",
            array(
            "username" => $username
            )
        );
    }

}
