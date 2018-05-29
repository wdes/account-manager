<?php
declare(strict_types = 1);
namespace AccountManager\Twig;

use PHPUnit\Framework\TestCase;
use \Twig_Environment;
use \AccountManager\Twig\Load;

class TwigTest extends TestCase
{

    /**
     * testInstance
     *
     * @return Twig
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
     * testEscapeHtml
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

}
