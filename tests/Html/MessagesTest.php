<?php
declare(strict_types = 1);
namespace AccountManager\Html;

require_once __DIR__.'/../../src/Constants.php';

use PHPUnit\Framework\TestCase;
use \AccountManager\Html\Messages;
use \AccountManager\Html\Message;
use \AccountManager\Twig\Load;

class MessagesTest extends TestCase
{

    /**
     * testInstance
     *
     * @return Messages
     */
    public function testInstance(): Messages
    {
        Load::load(TWIG_TMP);
        $messages = new Messages();
        $this->assertInstanceOf(Messages::class, $messages);
        return $messages;
    }

    /**
     * testEmpty
     * @depends testInstance
     * @param Messages $messages
     * @return void
     */
    public function testEmpty(Messages $messages): void
    {
        $html = $messages->render();
        $this->assertEquals("", $html);
        $this->assertEmpty($html);
    }

    /**
     * testNotEmpty
     * @depends testInstance
     * @param Messages $messages
     * @return void
     */
    public function testNotEmpty(Messages $messages): void
    {
        $messages->add(new Message("test message @!"));
        $html = $messages->render();
        $this->assertEquals(
            '    <div class="alert alert-primary" role="alert">'.PHP_EOL.
            '    test message @!'.PHP_EOL.
            '    </div>'.PHP_EOL,
            $html
        );
        $this->assertNotEmpty($html);
    }

    /**
     * testMessage
     * @return void
     */
    public function testMessage(): void
    {
        $message = new Message("test message @!");
        $this->assertEquals(Message::primary, $message->severity);
        $this->assertNotEmpty($message->severity);
        $this->assertEquals("test message @!", $message->message);
        $this->assertNotEmpty($message->message);
    }

}
