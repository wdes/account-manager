<?php
declare(strict_types = 1);
namespace AccountManager\Authentification;

use PHPUnit\Framework\TestCase;
use \AccountManager\Authentification\Authentification;
use \stdClass;

class AuthentificationTest extends TestCase
{

    /**
     * testInstance
     *
     * @return Authentification
     */
    public function testInstance(): Authentification
    {
        $auth = new Authentification();
        $this->assertInstanceOf(Authentification::class, $auth);
        return $auth;
    }

    /**
     * @depends testInstance
     * @param Authentification $auth Authentification auth object
     * @return void
     */
    public function testIsNotLoggedIn(Authentification $auth): void
    {
        $this->assertFalse($auth->isLoggedIn());
    }

    /**
     * @depends testInstance
     * @param Authentification $auth Authentification auth object
     * @return void
     */
    public function testIsLoggedIn(Authentification $auth): void
    {
        $_SESSION["loggedIn"] = true;
        $this->assertTrue($auth->isLoggedIn());
    }

    /**
     * @depends testInstance
     * @param Authentification $auth Authentification auth object
     * @return void
     */
    public function testSetLoggedIn(Authentification $auth): void
    {
        $auth->setLoggedIn(true);
        $this->assertTrue($_SESSION["loggedIn"]);
        $auth->setLoggedIn(false);
        $this->assertFalse($_SESSION["loggedIn"]);
    }

    /**
     * @depends testInstance
     * @param Authentification $auth Authentification auth object
     * @return void
     */
    public function testGetSetUser(Authentification $auth): void
    {
        $user     = new stdClass();
        $user->id = "1";

        $auth->setUser($user);
        $this->assertEquals($user, $auth->getUser());
    }

    /**
     * Deletes the session
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        \session_destroy();
    }

}
