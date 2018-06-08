<?php
declare(strict_types = 1);
namespace AccountManager;

use \AccountManager\Database;
use \AccountManager\Authentification\Authentification;
use \AccountManager\Identities;
use \AccountManager\Websites;
use \stdClass;

class Network
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
     * @var Identities
     */
    protected $identities;
    /**
     * @var Websites
     */
    protected $websites;

    /**
     * Create a new network
     *
     * @param Database         $db   Database instance
     * @param Authentification $auth Authentification instance
     */
    public function __construct(Database $db, Authentification $auth)
    {
        $this->db         = $db;
        $this->auth       = $auth;
        $this->identities = new Identities($db, $auth);
        $this->websites   = new Websites($db, $auth);
    }

    /**
     * Get the network of the user
     *
     * @return stdClass the visJS network with nodes, edges, groups
     */
    public function buildNetwork(): stdClass
    {
        $out        = new stdClass();
        $out->nodes = array();
        $out->edges = array();

        $groupIDS             = new stdClass();
        $groupIDS->id         = "ids";
        $groupIDS->label      = "Identities";
        $groupWebsites        = new stdClass();
        $groupWebsites->id    = "webs";
        $groupWebsites->label = "Websites";
        $out->groups          = array($groupIDS, $groupWebsites);

        foreach ($this->identities->identities() as $identity) {
            $newNode        = new stdClass();
            $newNode->id    = "id:".$identity->idId;
            $newNode->label = $identity->valueId;
            $newNode->group = $groupIDS->id;
            $out->nodes[]   = $newNode;
        }
        foreach ($this->websites->websites() as $website) {
            $newNode        = new stdClass();
            $newNode->id    = "web:".$website->id;
            $newNode->label = $website->label;
            $newNode->group = $groupWebsites->id;
            $out->nodes[]   = $newNode;
        }
        foreach ($this->websites->websitesIdentities() as $websiteIdentity) {
            $newEdge       = new stdClass();
            $newEdge->id   = "web:".$websiteIdentity->idWebsite."id:".$websiteIdentity->idIdentity;
            $newEdge->from = "web:".$websiteIdentity->idWebsite;
            $newEdge->to   = "id:".$websiteIdentity->idIdentity;
            $out->edges[]  = $newEdge;
        }
        return $out;
    }

}
