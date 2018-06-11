<?php
declare(strict_types = 1);
namespace AccountManager;

use PHPUnit\Framework\TestCase;
use \AccountManager\Database;
use \AccountManager\Config;
use \AccountManager\Utils\Tests;
use \PDO;
use \PDOStatement;

/**
  * @author William Desportes
  */
class DatabaseTest extends TestCase
{

    /**
     * test Connect And Instance
     *
     * @return Database
     */
    public function testConnectAndInstance(): Database
    {
        $config = new Config(__DIR__);// Test suite file: tests/.env
        $db     = new Database($config);
        $this->assertInstanceOf(Database::class, $db);
        return $db;
    }

    /**
     * test Process Conditions Single
     * @depends testConnectAndInstance
     * @param Database $db Database instance
     * @return void
     */
    public function testProcessConditionsSingle(Database $db): void
    {
        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1" => "valeur1"), "WHERE "));
        $this->assertEquals('WHERE `col1`=:p1', $whereTest->sql);
        $this->assertEquals(array(':p1' => 'valeur1'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);

        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1" => "valeur1")));
        $this->assertEquals('`col1`=:p1', $whereTest->sql);
        $this->assertEquals(array(':p1' => 'valeur1'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);
    }

    /**
      * test Process Conditions Multiple
      * @depends testConnectAndInstance
      * @param Database $db Database instance
      * @return void
      */
    public function testProcessConditionsMultiple(Database $db): void
    {
        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1" => "valeur1","col2" => "valeur2"), "WHERE "));
        $this->assertEquals('WHERE `col1`=:p1 , `col2`=:p2', $whereTest->sql);
        $this->assertEquals(array(':p1' => 'valeur1',':p2' => 'valeur2'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);

        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1" => "valeur1","col2" => "valeur2")));
        $this->assertEquals('`col1`=:p1 , `col2`=:p2', $whereTest->sql);
        $this->assertEquals(array(':p1' => 'valeur1',':p2' => 'valeur2'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);
    }

    /**
     * test Get Bdd
     * @depends testConnectAndInstance
     * @param Database $db Database instance
     * @return Database
     */
    public function testGetBdd(Database $db): Database
    {
        $pdo = $db->getPDO();
        $this->assertInstanceOf(PDO::class, $pdo);

        return $db;
    }

    /**
     * test Create Table
     * @depends testGetBdd
     * @param Database $db Database instance
     * @return Database
     */
    public function testCreateTable(Database $db): Database
    {
        $this->assertEquals(
            0,
            $db->getPDO()->exec(
                "CREATE TABLE `test1` (id INT(11) PRIMARY KEY, akey VARCHAR(32) CHARACTER SET utf8mb4) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            )
        );
        $this->assertEquals(
            0,
            $db->getPDO()->exec(
                "CREATE TABLE `test2` (id INT(11) PRIMARY KEY, akey VARCHAR(32) CHARACTER SET utf8mb4) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            )
        );
        return $db;
    }

    /**
     * test Select Empty Table No Criteria
     * @depends testCreateTable
     * @param Database $db Database instance
     * @return void
     */
    public function testSelectEmptyTableNoCriteria(Database $db): void
    {
        $obj = $db->Select("*", "test1");
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(0, $obj->rowCount());
    }

    /**
     * test Insert Table
     * @depends testCreateTable
     * @param Database $db Database instance
     * @return Database
     */
    public function testInsertTable(Database $db): Database
    {
        $success = $db->Insert("test1", array("id" => 0, "akey" => "blablaéà"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * test Select Table No Criteria
     * @depends testInsertTable
     * @param Database $db Database instance
     * @return void
     */
    public function testSelectTableNoCriteria(Database $db): void
    {
        $this->verifyCanFindOne($db, "blablaéà");
    }

    /**
     * verify Can Find One
     * @depends testInsertTable
     * @param Database $db        Database instance
     * @param string   $akeyvalue Expected value to column 'akey'
     * @return void
     */
    public function verifyCanFindOne(Database $db, string $akeyvalue): void
    {
        $obj = $db->Select("*", "test1");
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals($akeyvalue, $row->akey);
    }

    /**
     * test Exists
     * @depends testInsertTable
     * @param Database $db Database instance
     * @return void
     */
    public function testExists(Database $db): void
    {
        $exists = $db->Exists("test1", array("id" => 0));
        $this->assertTrue($exists);
    }

    /**
     * test Insert Table Duplicate
     * @depends testInsertTable
     * @param Database $db Database instance
     * @return Database
     */
    public function testInsertTableDuplicate(Database $db): Database
    {
        $success = $db->Insert("test1", array("id" => 0, "akey" => "blablaéà"), array("akey" => "updated"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * test Select Table After Duplicate No Criteria
     * @depends testInsertTableDuplicate
     * @param Database $db Database instance
     * @return void
     */
    public function testSelectTableAfterDuplicateNoCriteria(Database $db): void
    {
        $this->verifyCanFindOne($db, "updated");
    }

    /**
     * test Insert Table 2
     * @depends testCreateTable
     * @param Database $db Database instance
     * @return Database
     */
    public function testInsertTable2(Database $db): Database
    {
        $success = $db->Insert("test2", array("id" => 0, "akey" => "valueoftest2"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * test Select Table Join
     * @depends testInsertTable2
     * @param Database $db Database instance
     * @return void
     */
    public function testSelectTableJoin(Database $db): void
    {
        $obj = $db->Select(
            array("test1.id, test2.akey, test1.akey as `akey of test1`"),
            array("test1","test2"),
            array("test1.id" => "test2.id")
        );
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("valueoftest2", $row->akey);
        $this->assertEquals("updated", $row->{'akey of test1'});
    }

    /**
     * test Select Table Operator Not Like
     * @depends testInsertTable2
     * @param Database $db Database instance
     * @return void
     */
    public function testSelectTableOperatorNotLike(Database $db): void
    {
        $obj = $db->Select("*", "test1", array(array("akey","NOT LIKE","updated")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(0, $obj->rowCount());
    }

    /**
     * Provides test data for testSelectTableOperators
     * @return array[]
     */
    public function dataSelectOperatorsProvider(): array
    {
        return [
            ["id", "<", 1],
            ["id", ">", -1],
            ["akey", "LIKE", "updated"],
            ["akey", "=", "updated"],
        ];
    }

    /**
     * test Select Table Operators
     *
     * @dataProvider dataSelectOperatorsProvider
     * @depends testInsertTable2
     * @param string            $key      The key
     * @param string            $operator The operator
     * @param int|string|double $value    The value
     * @param Database          $db       Database instance
     * @return void
     */
    public function testSelectTableOperators(string $key, string $operator, $value, Database $db): void
    {
        $obj = $db->Select("*", "test1", array(array($key, $operator, $value)));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("updated", $row->akey);
    }

    /**
     * test Delete
     * @depends testInsertTable
     * @param Database $db Database instance
     * @return void
     */
    public function testDelete(Database $db): void
    {
        $deleted = $db->Delete("test1", array("id" => 0));
        $this->assertTrue($deleted);
    }

    /**
     * test Drop Table
     * @depends testCreateTable
     * @param Database $db Database instance
     * @return void
     */
    public function testDropTable(Database $db): void
    {
        $this->assertEquals(0, $db->getPDO()->exec("DROP TABLE `test1`"));
        $this->assertEquals(0, $db->getPDO()->exec("DROP TABLE `test2`"));
    }

}
