<?php
declare(strict_types = 1);
namespace AccountManager;

use \AccountManager\Database;
use \AccountManager\Authentification\Authentification;
use \PDO;

class Websites
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
     * Create a new websites
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
     * Get the number of websites of user
     *
     * @return string number of websites of user
     */
    public function count(): string
    {
        $obj = $this->db->Select(
            "COUNT(*) as nbr", "users__websites",
            array("idUser" => $this->auth->getUser()->id)
        );
        return $obj->fetch(PDO::FETCH_OBJ)->nbr;
    }

    /**
     * Get the websites of the user
     *
     * @return stdClass[]
     */
    public function websites(): array
    {
        $obj = $this->db->Select(
            array("id", "label"), array("users__websites", "websites"),
            array(
                 "idUser" => $this->auth->getUser()->id,
                 "websites.id" => "users__websites.idWebsite"
            )
        );
        return $obj->fetchAll(PDO::FETCH_OBJ);
    }

}
