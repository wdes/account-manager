<?php
declare(strict_types = 1);
namespace AccountManager;

use PHPUnit\Framework\TestCase;
use \Twig_Environment;
use \AccountManager\Config;
use \AccountManager\Authentification\Authentification;

class AutoLoadTest extends TestCase
{

    /**
     * testInstance
     *
     * @return Twig_Environment
     */
    public function testInstance(): Twig_Environment
    {
        global $twig, $auth, $config;
        require_once __DIR__.'/../src/AutoLoad.php';
        $this->assertInstanceOf(Twig_Environment::class, $twig);
        $this->assertInstanceOf(Authentification::class, $auth);
        $this->assertInstanceOf(Config::class, $config);

        return $twig;
    }

    /**
     * testAuthGlobalTwig
     * @depends testInstance
     * @param Twig_Environment $Twig Twig_Environment instance
     * @return void
     */
    public function testAuthGlobalTwig(Twig_Environment $Twig): void
    {
        $this->assertInstanceOf(Authentification::class, $Twig->getGlobals()["_auth"]);
    }

    /**
     * testPostGetGlobalTwig
     * @depends testInstance
     * @param Twig_Environment $Twig Twig_Environment instance
     * @return void
     */
    public function testPostGetGlobalTwig(Twig_Environment $Twig): void
    {
        $this->assertEquals([], $Twig->getGlobals()["_post"]);
        $this->assertEquals([], $Twig->getGlobals()["_get"]);
    }

}
