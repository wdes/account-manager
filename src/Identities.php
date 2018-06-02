<?php
declare(strict_types = 1);
namespace AccountManager;

use \AccountManager\Database;
use \AccountManager\Authentification\Authentification;
use \PDO;

class Identities
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
     * Create a new identities
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
     * Get the number of identities of user
     *
     * @return string number of identities of user
     */
    public function count(): string
    {
        $obj = $this->db->Select(
            "COUNT(*) as nbr", "identities__users",
            array("idUser" => $this->auth->getUser()->id)
        );
        return $obj->fetch(PDO::FETCH_OBJ)->nbr;
    }

    /**
     * Get the identities of the user
     *
     * @return \stdClass[]
     */
    public function identities(): array
    {
        $obj = $this->db->Select(
            array(
                "identities.id as idId",
                "value as valueId",
                "identities__types.label as typeLabel"
            ),
            array(
                "identities__users",
                "identities",
                "identities__types"
            ),
            array(
                "idUser" => $this->auth->getUser()->id,
                "identities.idTypeIdentity" => "identities__types.id",
                "identities__users.idIdentity" => "identities.id",
            )
        );
        return $obj->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get the identity types
     *
     * @return \stdClass[]
     */
    public function types(): array
    {
        $obj = $this->db->Select(
            array(
                "id",
                "label"
            ),
            "identities__types"
        );
        return $obj->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Add an identity
     *
     * @param integer $idType Id of identity type
     * @param string  $value  Value
     * @return void
     */
    public function add(int $idType, string $value): void
    {
        if ($this->db->Insert(
            "identities",
            array("value" => $value, "idTypeIdentity" => $idType)
        )
        ) {
            $this->db->Insert(
                "identities__users",
                array("idIdentity" => $this->db->getPDO()->lastInsertId(), "idUser" => $this->auth->getUser()->id)
            );
        }
    }

}
