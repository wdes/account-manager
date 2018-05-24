<?php
declare(strict_types = 1);
namespace AccountManager\Html;

use PHPUnit\Framework\TestCase;
use \AccountManager\Authentification\Authentification;

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

}
