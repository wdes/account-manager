<?php
declare(strict_types = 1);
namespace AccountManager;

use \AccountManager\Database;
use \AccountManager\Authentification\Authentification;
use \PDO;

class Auths
{
    /**
     * @var Database
     */
    protected $db;
    /**
     * @var Authentification
     */
    protected $auth;

    /**
     * Create a new auths
     *
     * @param Database         $db   Database instance
     * @param Authentification $auth Authentification instance
     */
    public function __construct(Database $db, Authentification $auth)
    {
        $this->db   = $db;
        $this->auth = $auth;
    }

    /**
     * Get the number of auths
     *
     * @return string number of auths
     */
    public function count(): string
    {
        $obj = $this->db->Select(
            "COUNT(*) as nbr", "auths"
        );
        return $obj->fetch(PDO::FETCH_OBJ)->nbr;
    }

    /**
     * Get the auths
     *
     * @return stdClass[]
     */
    public function auths(): array
    {
        $obj = $this->db->Select(
            array(
                "auths.id as idAuth",
                "label as labelAuth"
            ),
            "auths"
        );
        return $obj->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Add an auth
     *
     * @param string  $label  The label
     * @return void
     */
    public function add(string $label): void
    {
        $this->db->Insert(
            "auths",
            array("label" => $label)
        );
    }

}
