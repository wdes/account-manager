<?php
declare(strict_types = 1);
namespace AccountManager;

use \AccountManager\Config;
use \PDO;
use \PDOStatement;
use \stdClass;

/**
  * Gère les interactions avec la BDD
  * Tout est fait en requêtes préparés
  * @uses \PDO
  * @author William Desportes <williamdes@wdes.fr>
  */
class Database
{
    private $bdd;

    /**
     * Create a Database instance
     *
     * @param Config $config Configuration
     */
    public function __construct(Config $config)
    {
        $c         = $config->getDatabase();
        $this->bdd = new PDO("mysql:dbname=$c->name;host=$c->host;charset=utf8", $c->user, $c->password);
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Créé une chaine clef=valeur ou clef opérateur valeur
     * @param array<string|int, float|int|string|array<string, string, float|int|string>> $kvalues      wheres
     * @see (cle=>valeur,cle2=>valeur2) ou (cle=>valeur, array('cle','opérateur','valeur'))
     * @param string                                                                      $prefix       (optional) Un
     *                                                                                                  préfixe
     * @param int                                                                         $countstart   (optional) Identifiant de
     *                                                                                                  paramètre de départ
     * @param bool                                                                        $joincheck    (optional) Check if join can be made
     * @param string                                                                      $glueOperator (optional) Operator to glue parts (OR, AND, , )
     * @example processConditions(array("col1"=>"valeur1"),"WHERE")
     * @example processConditions(array("col1"=>"valeur1"),"WHERE",5) // Si déja 4 parametres de process voir Database::Insert
     * @example processConditions(array("table1.id"=>"table2.id"),"WHERE",0, true) // vérifier si clause de jointure est dans $kvalues
     * @example processConditions(array("col1"=>"valeur1"))
     * @return stdClass L'objet
     */
    private function processConditions(
        array $kvalues,
        string $prefix = "",
        int $countstart = 0,
        bool $joincheck = false,
        string $glueOperator = ","
    ): stdClass {
        $out          = new stdClass();
        $out->sql     = "";
        $out->ks      = array();
        $i            = $countstart;
        $kcount       = count($kvalues);
        $glueOperator = " ".$glueOperator." ";
        foreach ($kvalues as $key => $value) {
            if ($i == $countstart) { // Add prefix at start
                $out->sql .= $prefix;
            }
            $i++;

            if (is_array($value)) {
                $isSubRequest = false;
                if (\is_string($value[2])) {
                    if (\substr($value[2], 0, 1) === "("
                        && \substr($value[2], \strlen($value[2]) - 1, 1) === ")"
                    ) {
                        $isSubRequest = true;
                    }
                }

                if ($isSubRequest) {
                    $out->sql .= "`".$value[0]."` ".$value[1]." ".$value[2];
                    if (count($value) === 4) {
                        foreach ($value[3] as $key => $value) {
                            $out->ks[$key] = $value;
                        }
                    }
                } else {
                    $out->sql       .= "`".$value[0]."` ".$value[1]." :p$i";
                    $out->ks[":p$i"] = $value[2];
                }

            } else {
                $is_join = false;
                if ($joincheck) {
                    $is_join = (
                        preg_match("!([0-9A-Za-z]+).([0-9A-Za-z]+)!", (string) $key)
                        &&
                        preg_match("!([0-9A-Za-z]+).([0-9A-Za-z]+)!", (string) $value)
                    );
                }
                if ($is_join) {
                    $out->sql .= "$key=$value";
                } else {
                    $out->sql       .= "`$key`=:p$i";
                    $out->ks[":p$i"] = $value;
                }
            }

            if ($kcount != ($i - $countstart)) {// No , at the end
                $out->sql .= $glueOperator;
            }
        }
        return $out;
    }

    /**
     * Insertion
     * @param string                          $tableName        Nom de la table
     * @param array<string, int|float|string> $kvalues          see processConditions
     * @param array<string, int|float|string> $duplicateReplace (optional) see processConditions
     * @see Database::processConditions
     * @example Insert("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç","ip"=>"127.0.0.1"));
     * @return bool Exécution de la requête
     */
    public function Insert(string $tableName, array $kvalues, array $duplicateReplace = null): bool
    {
        $sql  = "INSERT INTO $tableName SET ";
        $w    = self::processConditions($kvalues);
        $sql .= $w->sql;
        if (is_array($duplicateReplace)) {
            $ww    = self::processConditions($duplicateReplace, " ON DUPLICATE KEY UPDATE ", count($kvalues));
            $sql  .= $ww->sql;
            $w->ks = array_merge($w->ks, $ww->ks);
        }
        $req = $this->bdd->prepare($sql);
        return $req->execute($w->ks);
    }

    /**
      * Suppression
      * @param string                                                                      $tableName Table name
      * @param array<string|int, float|int|string|array<string, string, float|int|string>> $where     WHERE, see processConditions
      * @see Database::processConditions
      * @example Delete("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç","ip"=>"127.0.0.1"));
      * @example Delete("test_william_dev",array("id"=>1,"valeur"=>"ééééé'''àààççç",array("valeur",">")=>"3"));
      * @return bool Request success/failure
      */
    public function Delete(string $tableName, array $where): bool
    {
        $w   = self::processConditions($where, " WHERE ");
        $sql = "DELETE FROM $tableName".$w->sql;
        $req = $this->bdd->prepare($sql);
        return $req->execute($w->ks);
    }

    /**
     * Sélection
     * @param string[]|string                                                             $cols      (nom des colonnes) ou nom de la colonne
     * @param string[]|string                                                             $tableName Nom de la table ou noms des tables array('table1','table2')
     * @param array<string|int, float|int|string|array<string, string, float|int|string>> $wheres    (optional) (nom de champ => valeur) wheres
     * @example Select("test_william_dev",array("id","valeur","ip"));
     * @example Select("test_william_dev",array("id","valeur","ip"),array("ip"=>"127.0.0.1"));
     * @return PDOStatement L'objet de la requête
     */
    public function Select($cols, $tableName, array $wheres = array()): PDOStatement
    {
        $sql          = "SELECT ";
        $multi_tables = is_array($tableName);
        if ($multi_tables) {
            $tableName = implode(",", $tableName);
        }
        if (is_array($cols)) {
            $cols = implode(",", $cols);
        }

        $sql .= $cols;
        $sql .= " FROM ".$tableName;
        $w    = self::processConditions($wheres, " WHERE ", 0, $multi_tables, "AND");
        $sql .= $w->sql;
        $req  = $this->bdd->prepare($sql);
        $req->execute($w->ks);
        return $req;
    }

    /**
     * Check if row exists
     *
     * @param string[]|string                                                             $tableName table name or tables names
     * @param array<string|int, float|int|string|array<string, string, float|int|string>> $wheres    Wheres
     * @return bool row exists
     */
    public function Exists($tableName, array $wheres = array()): bool
    {
        $req = $this->Select("COUNT(*) as nbr", $tableName, $wheres);
        return $req->fetch(PDO::FETCH_OBJ)->nbr > 0;
    }

    /**
     * Returns the PDO object
     *
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->bdd;
    }

}
