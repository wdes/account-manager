<?php
declare(strict_types=1);

namespace AccountManager\Html;

require_once __DIR__.'/../../src/Constants.php';

use PHPUnit\Framework\TestCase;
use \AccountManager\Authentification\Authentification;

class AuthentificationTest extends TestCase
{
    public function testInstance()
    {
        $auth = new Authentification();
        $this->assertInstanceOf(Authentification::class, $auth);
        return $auth;
    }

    /**
     * @depends testInstance
     */
    public function testIsNotLoggedIn(Authentification $auth)
    {
        $this->assertFalse($auth->isLoggedIn());
    }

    /**
     * @depends testInstance
     */
    public function testIsLoggedIn(Authentification $auth)
    {
        $_SESSION["loggedIn"] = true;
        $this->assertTrue($auth->isLoggedIn());
    }
}
