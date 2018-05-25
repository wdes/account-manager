<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

use \AccountManager\Database;
use \PDO;
use \stdClass;

/**
 * Users
 */
class Users
{
    /**
     * Database instance
     *
     * @var Database
     */
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
     * @return bool|stdClass
     */
    public function login(string $username, string $password): stdClass
    {
        $res           = $this->db->Select(
            "*",
            "users",
            array(
            "username" => $username,
            "password" => $password
            )
        );
        if ( ($user          = $res->fetch(PDO::FETCH_OBJ)) === false ) {
            $user = new stdClass();
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

}
