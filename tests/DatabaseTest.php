<?php
declare(strict_types=1);

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
    public function testConnectAndInstance(): Database
    {
        $config = new Config(__DIR__);// Test suite file: tests/.env
        $db = new Database($config);
        $this->assertInstanceOf(Database::class, $db);
        return $db;
    }

    /**
     * @depends testConnectAndInstance
     */
    public function testProcessConditionsSingle(Database $db): void
    {
        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1"=>"valeur1"), "WHERE "));
        $this->assertEquals('WHERE `col1`=:p1', $whereTest->sql);
        $this->assertEquals(array(':p1'=>'valeur1'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);

        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1"=>"valeur1")));
        $this->assertEquals('`col1`=:p1', $whereTest->sql);
        $this->assertEquals(array(':p1'=>'valeur1'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);

    }
   /**
     * @depends testConnectAndInstance
     */
    public function testProcessConditionsMultiple(Database $db): void
    {
        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1"=>"valeur1","col2"=>"valeur2"), "WHERE "));
        $this->assertEquals('WHERE `col1`=:p1, `col2`=:p2', $whereTest->sql);
        $this->assertEquals(array(':p1'=>'valeur1',':p2'=>'valeur2'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);

        $whereTest = Tests::invokeMethod($db, 'processConditions', array(array("col1"=>"valeur1","col2"=>"valeur2")));
        $this->assertEquals('`col1`=:p1, `col2`=:p2', $whereTest->sql);
        $this->assertEquals(array(':p1'=>'valeur1',':p2'=>'valeur2'), $whereTest->ks);
        $this->assertNotEmpty($whereTest);

    }

    /**
     * @depends testConnectAndInstance
     */
    public function testGetBDD(Database $db): Database
    {
        $pdo = $db->getPDO();
        $this->assertInstanceOf(PDO::class, $pdo);

        return $db;
    }

    /**
     * @depends testGetBDD
     */
    public function testCreateTable(Database $db): Database
    {
        $this->assertEquals(0, $db->getPDO()->exec(
            "CREATE TABLE `test1` (id INT(11) PRIMARY KEY, akey VARCHAR(32) CHARACTER SET utf8mb4) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"));
        $this->assertEquals(0, $db->getPDO()->exec(
                "CREATE TABLE `test2` (id INT(11) PRIMARY KEY, akey VARCHAR(32) CHARACTER SET utf8mb4) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"));
        return $db;
    }

    /**
     * @depends testCreateTable
     */
    public function testSelectEmptyTableNoCriteria(Database $db): void
    {
        $obj = $db->Select("*", "test1");
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(0, $obj->rowCount());
    }

    /**
     * @depends testCreateTable
     */
    public function testInsertTable(Database $db): Database
    {
        $success = $db->Insert("test1", array("id"=>0, "akey"=>"blablaéà"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * @depends testInsertTable
     */
    public function testSelectTableNoCriteria(Database $db): void
    {
        $this->verifyCanFindOne($db, "blablaéà");
    }

    /**
     * @depends testInsertTable
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
     * @depends testInsertTable
     */
    public function testExists(Database $db): void
    {
        $exists = $db->Exists("test1", array("id"=>0));
        $this->assertTrue($exists);
    }

    /**
     * @depends testInsertTable
     */
    public function testInsertTableDuplicate(Database $db): Database
    {
        $success = $db->Insert("test1", array("id"=>0, "akey"=>"blablaéà"), array("akey"=>"updated"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * @depends testInsertTableDuplicate
     */
    public function testSelectTableAfterDuplicateNoCriteria(Database $db): void
    {
        $this->verifyCanFindOne($db, "updated");
    }

    /**
     * @depends testCreateTable
     */
    public function testInsertTable2(Database $db): Database
    {
        $success = $db->Insert("test2", array("id"=>0, "akey"=>"valueoftest2"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableJoin(Database $db): void
    {
        $obj = $db->Select(
            array("test1.id, test2.akey, test1.akey as `akey of test1`"),
            array("test1","test2"),
            array("test1.id"=>"test2.id")
        );
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("valueoftest2", $row->akey);
        $this->assertEquals("updated", $row->{'akey of test1'});
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableOperatorNOTLIKE(Database $db): void
    {
        $obj = $db->Select("*", "test1", array(array("akey","NOT LIKE","updated")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(0, $obj->rowCount());
    }

    /**
     * Provides test data for testSelectTableOperators
     *
     * @return array
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
     * @dataProvider dataSelectOperatorsProvider
     * @depends testInsertTable2
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
     * @depends testInsertTable
     */
    public function testDelete(Database $db): void
    {
        $deleted = $db->Delete("test1", array("id"=>0));
        $this->assertTrue($deleted);
    }

    /**
     * @depends testCreateTable
     */
    public function testDropTable(Database $db): void
    {
        $this->assertEquals(0, $db->getPDO()->exec("DROP TABLE `test1`"));
        $this->assertEquals(0, $db->getPDO()->exec("DROP TABLE `test2`"));
    }

}
