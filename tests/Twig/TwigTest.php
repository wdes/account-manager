<?php
declare(strict_types = 1);
namespace AccountManager\Twig;

use PHPUnit\Framework\TestCase;
use \Twig_Environment;
use \Twig_Cache_Filesystem;
use \AccountManager\Twig\Load;
use \stdClass;

class TwigTest extends TestCase
{

    /**
     * testInstance
     *
     * @return Twig_Environment
     */
    public function testInstance(): Twig_Environment
    {
        require_once __DIR__.'/../../src/Constants.php';
        Load::init(TWIG_TMP);
        $Twig = Load::getTwig();
        $this->assertInstanceOf(Twig_Environment::class, $Twig);
        return $Twig;
    }

    /**
     * test get Twig
     * @return void
     */
    public function testGetTwig(): void
    {
        $this->assertInstanceOf(Twig_Environment::class, Load::getTwig());
    }

    /**
     * test get Twig cache FS
     * @return void
     */
    public function testGetTwigCacheFS(): void
    {
        $this->assertInstanceOf(Twig_Cache_Filesystem::class, Load::getTwigCacheFS());
    }

    /**
     * testHtmlFunction
     * @depends testInstance
     * @param Twig_Environment $Twig Twig_Environment instance
     * @return void
     */
    public function testHtmlFunction(Twig_Environment $Twig): void
    {
        $template = $Twig->createTemplate('Hello {{ html(htmltext) }}');
        $html     = $template->render(array('htmltext' => "<b>You</b>"));
        $this->assertEquals("Hello <b>You</b>", $html);
        $this->assertNotEmpty($html);
    }

    /**
     * testAssetFunction
     * @depends testInstance
     * @param Twig_Environment $Twig Twig_Environment instance
     * @return void
     */
    public function testAssetFunction(Twig_Environment $Twig): void
    {
        $template = $Twig->createTemplate('{{ asset("css/style.css") }}');
        $html     = $template->render(array());
        $this->assertEquals("public/assets/css/style.css", $html);
        $this->assertNotEmpty($html);
    }

    /**
     * test Escape Html
     * @depends testInstance
     * @param Twig_Environment $Twig Twig_Environment instance
     * @return void
     */
    public function testEscapeHtml(Twig_Environment $Twig): void
    {
        $template = $Twig->createTemplate('Hello {{ htmltext }}');
        $html     = $template->render(array('htmltext' => "<b>You</b>"));
        $this->assertEquals("Hello &lt;b&gt;You&lt;/b&gt;", $html);
        $this->assertNotEmpty($html);
    }

    /**
     * test to Json
     * @depends testInstance
     * @param Twig_Environment $Twig Twig_Environment instance
     * @return void
     */
    public function testToJson(Twig_Environment $Twig): void
    {
        $template = $Twig->createTemplate('toJson > {{ toJson(obj) }}');
        $obj      = new stdClass();
        $obj->a   = array();
        $html     = $template->render(array('obj' => $obj));
        $this->assertEquals("toJson > {\n    \"a\": []\n}", $html);
        $this->assertNotEmpty($html);
    }

}
