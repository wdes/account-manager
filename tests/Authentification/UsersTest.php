<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

use PHPUnit\Framework\TestCase;
use \AccountManager\Authentification\Users;
use \AccountManager\Database;
use \AccountManager\Config;
use \stdClass;

class UsersTest extends TestCase
{
    /**
     * Test user
     *
     * @var stdClass
     */
    private static $testUser;

    /**
     * Generate random user
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        UsersTest::$testUser           = new stdClass();
        UsersTest::$testUser->username = str_shuffle("abcdefghijklmnopqrstuvw");
        UsersTest::$testUser->email    = str_shuffle("abcdefghijklmnopqrstuvw").
        "@".str_shuffle("abcdefghijklmnopqrstuvw");
        UsersTest::$testUser->password = str_shuffle("ABCDEFGHIJLGHIZNCHEFEJEF156878916312798abcdefghijklmnopqrstuvw'\"@");
    }

    /**
     * test Instance
     *
     * @return Users
     */
    public function testInstance(): Users
    {
        $config = new Config(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR);// Test suite file: tests/.env
        $db     = new Database($config);
        $users  = new Users($db);
        $this->assertInstanceOf(Users::class, $users);
        return $users;
    }

    /**
     * Test random username available
     * @depends testInstance
     * @param Users $users Users auth object
     * @return void
     */
    public function testRandomUsernameAvailable(Users $users): void
    {
        $this->assertTrue($users->usernameAvailable(str_shuffle("abcdefghijklmnopqrstuvw'\"@")));
    }

    /**
     * Test random login
     * @depends testInstance
     * @param Users $users Users auth object
     * @return void
     */
    public function testRandomLogin(Users $users): void
    {
        $login = $users->login(
            str_shuffle("abcdefghijklmnopqrstuvw'\"@"),
            str_shuffle("abcdefghijklmnopqrstuvw'\"@")
        );
        $this->assertInstanceOf(stdClass::class, $login);
        $this->assertFalse(
            $login->success
        );
    }

    /**
     * Test register test user
     * @depends testInstance
     * @param Users $users Users auth object
     * @return Users
     */
    public function testRegister(Users $users): Users
    {
        $success = $users->register(
            UsersTest::$testUser->username,
            UsersTest::$testUser->email,
            UsersTest::$testUser->password
        );
        $this->assertTrue($success);

        return $users;
    }

    /**
     * Test login with test user
     * @depends testRegister
     * @param Users $users Users auth object
     * @return void
     */
    public function testLogin(Users $users): void
    {
        $user = $users->login(
            UsersTest::$testUser->username,
            UsersTest::$testUser->password
        );
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertFalse(
            $user->verified
        );
        $this->assertTrue(
            $user->success
        );
        $this->assertEquals(UsersTest::$testUser->username, $user->username);
        $this->assertGreaterThan(0, $user->id);
    }

    /**
     * Test delete account of test user
     * @depends testRegister
     * @param Users $users Users auth object
     * @return void
     */
    public function testDeleteAccount(Users $users): void
    {
        $success = $users->deleteAccount(
            UsersTest::$testUser->username
        );
        $this->assertTrue($success);
    }

    /**
     * Test login with test user when deleted
     * @depends testRegister
     * @param Users $users Users auth object
     * @return void
     */
    public function testLoginDeletedUser(Users $users): void
    {
        $user = $users->login(
            UsersTest::$testUser->username,
            UsersTest::$testUser->password
        );
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertFalse(
            $user->success
        );
    }

}
