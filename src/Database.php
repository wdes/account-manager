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
use \PDOStatement;
use \stdClass;

class Database {

    private $bdd;
    function __construct(Config $config){
        $c = $config->getDatabase();
        $this->bdd = new PDO("mysql:dbname=$c->name;host=$c->host;charset=utf8", $c->user, $c->password);
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Créé une chaine clef=valeur ou clef opérateur valeur
     * @param array $kvalues (cle=>valeur,cle2=>valeur2) ou (cle=>valeur, array('cle','opérateur','valeur'))
     * @param string $prefix (optional) Un préfixe
     * @param int $countstart (optional) Identifiant de paramètre de départ
     * @example processConditions(array("col1"=>"valeur1"),"WHERE")
     * @example processConditions(array("col1"=>"valeur1"),"WHERE",5) // Si déja 4 parametres de process voir Database::Insert
     * @example processConditions(array("table1.id"=>"table2.id"),"WHERE",0, true) // vérifier si clause de jointure est dans $kvalues
     * @example processConditions(array("col1"=>"valeur1"))
     * @return stdClass L'objet
     */
    private function processConditions(array $kvalues, string $prefix = "", int $countstart = 0, bool $joincheck = false): stdClass {
        $out = new stdClass();
        $out->sql = "";
        $out->ks = array();
        $i = $countstart;
        $kcount = count($kvalues);
        foreach($kvalues as $key => $value){
            if ($i == $countstart) // Add prefix at start
                $out->sql .= $prefix;
            $i++;

            if (is_array($value)) {
                $out->sql .=  "`".$value[0]."` ".$value[1]." :p$i";
                $out->ks[":p$i"] = $value[2];
            } else {
                $is_join = false;
                if ($joincheck) {
                    $is_join = (
                        preg_match("!([0-9A-Za-z]+).([0-9A-Za-z]+)!", $key)
                        &&
                        preg_match("!([0-9A-Za-z]+).([0-9A-Za-z]+)!", $value)
                    );
                }
                if ($is_join) {
                    $out->sql .=  "$key=$value";
                } else {
                    $out->sql .=  "`$key`=:p$i";
                    $out->ks[":p$i"] = $value;
                }

            }

            if ($kcount != ($i-$countstart) )// No , at the end
                $out->sql .= ", ";
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
     * @return boolean Exécution de la requête
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
     * @param string $tableName Table name
     * @param array $where WHERE, see processConditions
     * @see Database::processConditions
     * @example Delete("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç","ip"=>"127.0.0.1"));
     * @example Delete("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç",array("valeur",">")=>"3"));
     * @return boolean Request success/failure
     */
    public function Delete(string $tableName, array $where): bool {
        $w = self::processConditions($where, " WHERE ");
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
     * @return PDOStatement L'objet de la requête
     */
    public function Select($cols, $tableName, array $wheres = array()): PDOStatement {
        $sql = "SELECT ";
        $multi_tables = is_array($tableName);
        if($multi_tables)
            $tableName = implode(",", $tableName);
        if(is_array($cols))
            $cols = implode(",", $cols);

        $sql .= $cols;
        $sql .= " FROM ".$tableName;
        $w = self::processConditions($wheres, " WHERE ", 0, $multi_tables);
        $sql .= $w->sql;

        $req = $this->bdd->prepare($sql);
        $req->execute($w->ks);
        return $req;
    }

    /**
     * Check if row exists
     *
     * @param string|array $tableName
     * @param array $wheres
     * @return boolean
     */
    public function Exists($tableName, array $wheres = array()): bool {
        $req = $this->Select("COUNT(*) as nbr", $tableName, $wheres);
        return $req->fetch(PDO::FETCH_OBJ)->nbr > 0;
    }

    public function getPDO(): PDO {
        return $this->bdd;
    }
}
?>