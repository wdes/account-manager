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
    public function testConnectAndInstance()
    {
        $config = new Config(__DIR__);// Test suite file: tests/.env
        $db = new Database($config);
        $this->assertInstanceOf(Database::class, $db);
        return $db;
    }

    /**
     * @depends testConnectAndInstance
     */
    public function testProcessConditionsSingle(Database $db)
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
    public function testProcessConditionsMultiple(Database $db)
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
    public function testGetBDD(Database $db)
    {
        $pdo = $db->getPDO();
        $this->assertInstanceOf(PDO::class, $pdo);

        return $db;
    }

    /**
     * @depends testGetBDD
     */
    public function testCreateTable(Database $db)
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
    public function testSelectEmptyTableNoCriteria(Database $db)
    {
        $obj = $db->Select("*", "test1");
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(0, $obj->rowCount());
    }

    /**
     * @depends testCreateTable
     */
    public function testInsertTable(Database $db)
    {
        $success = $db->Insert("test1", array("id"=>0, "akey"=>"blablaéà"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * @depends testInsertTable
     */
    public function testSelectTableNoCriteria(Database $db)
    {
        $obj = $db->Select("*", "test1");
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("blablaéà", $row->akey);
    }

    /**
     * @depends testInsertTable
     */
    public function testExists(Database $db)
    {
        $exists = $db->Exists("test1", array("id"=>0));
        $this->assertTrue($exists);
    }

    /**
     * @depends testInsertTable
     */
    public function testInsertTableDuplicate(Database $db)
    {
        $success = $db->Insert("test1", array("id"=>0, "akey"=>"blablaéà"), array("akey"=>"updated"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * @depends testInsertTableDuplicate
     */
    public function testSelectTableAfterDuplicateNoCriteria(Database $db)
    {
        $obj = $db->Select("*", "test1");
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("updated", $row->akey);
    }

    /**
     * @depends testCreateTable
     */
    public function testInsertTable2(Database $db)
    {
        $success = $db->Insert("test2", array("id"=>0, "akey"=>"valueoftest2"));
        $this->assertTrue($success);
        return $db;
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableJoin(Database $db)
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
    public function testSelectTableOperatorNOTLIKE(Database $db)
    {
        $obj = $db->Select("*", "test1", array(array("akey","NOT LIKE","updated")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(0, $obj->rowCount());
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableOperatorEquals(Database $db)
    {
        $obj = $db->Select("*", "test1", array(array("akey","=","updated")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("updated", $row->akey);
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableOperatorLIKE(Database $db)
    {
        $obj = $db->Select("*", "test1", array(array("akey","LIKE","updated")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("updated", $row->akey);
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableOperatorGT(Database $db)
    {
        $obj = $db->Select("*", "test1", array(array("id",">","-1")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("updated", $row->akey);
    }

    /**
     * @depends testInsertTable2
     */
    public function testSelectTableOperatorLT(Database $db)
    {
        $obj = $db->Select("*", "test1", array(array("id","<","1")));
        $this->assertInstanceOf(PDOStatement::class, $obj);
        $this->assertEquals(1, $obj->rowCount());
        $row = $obj->fetch(PDO::FETCH_OBJ);
        $this->assertEquals(0, $row->id);
        $this->assertEquals("updated", $row->akey);
    }

    /**
     * @depends testInsertTable
     */
    public function testDelete(Database $db)
    {
        $deleted = $db->Delete("test1", array("id"=>0));
        $this->assertTrue($deleted);
    }

    /**
     * @depends testCreateTable
     */
    public function testDropTable(Database $db)
    {
        $this->assertEquals(0, $db->getPDO()->exec("DROP TABLE `test1`"));
        $this->assertEquals(0, $db->getPDO()->exec("DROP TABLE `test2`"));
    }

}
