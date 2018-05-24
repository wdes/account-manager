<?php
declare(strict_types = 1);
namespace AccountManager;

use PHPUnit\Framework\TestCase;

class ConstantsTest extends TestCase
{

    /**
     * testConstants
     *
     * @return void
     */
    public function testConstants(): void
    {
        require_once __DIR__.'/../src/Constants.php';
        $this->assertNotEmpty(LOCALE_DIR);
        $this->assertStringEndsWith('/', LOCALE_DIR);

        $this->assertNotEmpty(TEMPLATE_DIR);
        $this->assertStringEndsWith('/', TEMPLATE_DIR);

        $this->assertNotEmpty(SRC_DIR);
        $this->assertStringEndsWith('/', SRC_DIR);

        $this->assertNotEmpty(TMP_DIR);
        $this->assertStringEndsWith('/', TMP_DIR);

        $this->assertNotEmpty(PROJECT_ROOT);
        $this->assertStringEndsWith('/', PROJECT_ROOT);

        $this->assertNotEmpty(TWIG_TMP);

        $this->assertNotEmpty(PO_DIR);
        $this->assertStringEndsWith('/', PO_DIR);
    }

}
