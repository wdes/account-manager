<?php
declare(strict_types = 1);
namespace AccountManager;

use PHPUnit\Framework\TestCase;
use \AccountManager\Config;
use \stdClass;

class ConfigTest extends TestCase
{
    public static $dir = __DIR__.DIRECTORY_SEPARATOR."testenvdir".DIRECTORY_SEPARATOR;

    /**
     * Setup config
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        global $_SERVER;
        mkdir(self::$dir);
        $contents  = 'DB_USER="my user"'.PHP_EOL.'DB_PASS=p@ssword'.PHP_EOL;
        $contents .= 'DB_HOST=localhost'.PHP_EOL.'DB_NAME=testdb';
        file_put_contents(self::$dir.".env", $contents);
    }

    /**
     * Tear down config
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        unlink(self::$dir.".env");
        rmdir(self::$dir);
    }

    /**
     * test Instance
     *
     * @return Config
     */
    public function testInstance(): Config
    {
        $cfg = new Config(self::$dir);
        $this->assertInstanceOf(Config::class, $cfg);

        return $cfg;
    }

    /**
     * test Dir does not exist
     *
     * @expectedException     Exception
     * @expectedExceptionCode 0
     * @expectedExceptionMessageRegExp /The directory does not exist : (.+)/
     *
     * @return void
     */
    public function testException(): void
    {
        $cfg = new Config(self::$dir.str_shuffle("abcdefghijklmnopqrstuv"));
    }

    /**
     * test get Database
     * @depends testInstance
     * @param Config $cfg Config instance
     * @return void
     */
    public function testGetDatabase(Config $cfg): void
    {
        $conf = $cfg->getDatabase();
        $this->assertInstanceOf(stdClass::class, $conf);
        $this->assertEquals("my user", $conf->user);
        $this->assertEquals("p@ssword", $conf->password);
        $this->assertEquals("testdb", $conf->name);
        $this->assertEquals("localhost", $conf->host);
    }

}
