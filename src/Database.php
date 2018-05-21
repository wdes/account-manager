<?php
declare(strict_types=1);

/**
  * @uses \PDO
  * @author William Desportes
  * Gère les interactions avec la BDD
  * Tout est fait en requêtes préparés
  */

namespace AccountManager;
use \AccountManager\Config;
use \PDO;
use \stdClass;

class Database {

    private $bdd;
    function __construct(Config $config){
        $c = $config->getDatabase();
        $this->bdd = new PDO("mysql:dbname=;host=$c->host;charset=utf8", $c->user, $c->password);
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    /**
     * Créé une chaine clef=valeur ou clef opérateur valeur
     * @param array $kvalues (cle=>valeur,cle2=>valeur2) ou (cle=>valeur, array('cle','opérateur','valeur'))
     * @param string $prefix (optional) Un préfixe
     * @param int $countstart (optional) Identifiant de paramètre de départ
     * @example processConditions(array("col1"=>"valeur1"),"WHERE")
     * @example processConditions(array("col1"=>"valeur1"),"WHERE",5) // Si déja 4 parametres de process voir Database::Insert
     * @example processConditions(array("col1"=>"valeur1"))
     * @return string la chaine
     */
    private function processConditions(array $kvalues, string $prefix = "", int $countstart = 0): stdClass {
        $out = new stdClass();
        $out->sql = "";
        $out->ks = array();
        $i = $countstart;
        foreach($kvalues as $key => $value){
            if($i == $countstart)
                $out->sql .= $prefix;
            $i++;
            if(is_array($value))
                $out->sql .=  "`".$value[0]."`".$value[1].":p$i";
            else
                $out->sql .=  "`$key`=:p$i";
            if(count($kvalues) != ($i-$countstart) )
                $out->sql .= ", ";
            if(is_array($value))
                $out->ks[":p$i"] = $value[2];
            else
                $out->ks[":p$i"] = $value;
        }
        return $out;
    }
    /**
     * Insertion
     * @param string $tableName Nom de la table
     * @param array $kvalues voir processConditions
     * @param array $duplicateReplace (optional) voir processConditions
     * @see Database::processConditions
     * @example Insert("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç","ip"=>"127.0.0.1"));
     * @return bool Exécution de la requête
     */
    public function Insert(string $tableName, array $kvalues, array $duplicateReplace = null): bool {
        $executeValues = array();
        $sql = "INSERT INTO $tableName SET ";
        $w = self::processConditions($kvalues);
        $sql .= $w->sql;
        if(is_array($duplicateReplace)){
            $ww = self::processConditions($duplicateReplace, " ON DUPLICATE KEY UPDATE ", count($kvalues));
            $sql .= $ww->sql;
            $w->ks = array_merge($w->ks,$ww->ks);
        }
        $req = $this->bdd->prepare($sql);
        return $req->execute($w->ks);
    }
   /**
     * Suppression
     * @param string $tableName Nom de la table
     * @param array $kvalues voir processConditions
     * @see Database::processConditions
     * @example Delete("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç","ip"=>"127.0.0.1"));
     * @example Delete("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç",array("valeur",">")=>"3"));
     * @return bool Exécution de la requête
     */
    public function Delete(string $tableName, array $kvalues): bool {
        $w = self::processConditions($kvalues, " WHERE ");
        $sql = "DELETE FROM $tableName".$w->sql;
        $req = $this->bdd->prepare($sql);
        return $req->execute($w->ks);
    }
    /**
     * Sélection
     * @param array|string $cols (nom des colonnes) ou nom de la colonne
     * @param array|string $tableName Nom de la table ou noms des tables array('table1','table2')
     * @param array $wheres (optional) (nom de champ => valeur) wheres
     * @example Select("test_william_dev",array("id","valeur","ip"));
     * @example Select("test_william_dev",array("id","valeur","ip"),array("ip"=>"127.0.0.1"));
     * @return bool L'objet de la requête
     */
    public function Select($cols, $tableName, array $wheres = array()): bool {
        $sql = "SELECT ";
        if(is_array($tableName))
            $tableName = implode(",", $tableName);
        if(is_array($cols))
            $cols = implode(",", $cols);

        $sql .= $cols;
        $sql .= " FROM ".$tableName;
        $w = self::processConditions($wheres, " WHERE ");
        $sql .= $w->sql;

        $req = $this->bdd->prepare($sql);
        return $req->execute($w->ks);
    }
}
?>