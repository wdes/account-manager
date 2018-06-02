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
            "COUNT(*) as nbr", "websites__users",
            array("idUser" => $this->auth->getUser()->id)
        );
        return $obj->fetch(PDO::FETCH_OBJ)->nbr;
    }

    /**
     * Get the websites of the user
     *
     * @return \stdClass[]
     */
    public function websites(): array
    {
        $obj = $this->db->Select(
            array("id", "label"), array("websites__users", "websites"),
            array(
                 "idUser" => $this->auth->getUser()->id,
                 "websites.id" => "websites__users.idWebsite"
            )
        );
        return $obj->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Add a website
     *
     * @param integer $idIdentity Id of identity
     * @param string $domainName The domain name
     * @param boolean $canBeDeleted The account can be deleted (on website)
     * @return void
     */
    public function add(int $idIdentity, string $domainName, bool $canBeDeleted): void
    {
        $canBeDeleted = ($canBeDeleted) ? "1" : "0";
        if ($this->db->Insert(
            "websites",
            array("label" => $domainName, "cantDelete" => $canBeDeleted)
        )
        ) {
            $idWebsite = $this->db->getPDO()->lastInsertId();
            $this->db->Insert(
                "websites__users",
                array("idWebsite" => $idWebsite, "idUser" => $this->auth->getUser()->id)
            );
            $this->db->Insert(
                "domains",
                array("domainName" => $domainName)
            );
            $idDomain = $this->db->getPDO()->lastInsertId();
            $this->db->Insert(
                "websites__domains",
                array("idWebsite" => $idWebsite, "idDomain" => $idDomain)
            );
            $this->db->Insert(
                "identities__websites",
                array("idIdentity" => $idIdentity, "idWebsite" => $idWebsite)
            );
        }
    }

}
