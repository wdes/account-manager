<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \AccountManager\Database;
use \AccountManager\Config;
use \AccountManager\Utils\Tests;
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
}
?>