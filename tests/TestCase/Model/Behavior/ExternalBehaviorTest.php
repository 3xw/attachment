<?php
namespace Attachment\Test\TestCase\Model\Behavior;

use Attachment\Model\Behavior\ExternalBehavior;
use Cake\TestSuite\TestCase;

/**
 * Attachment\Model\Behavior\ExternalBehavior Test Case
 */
class ExternalBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Attachment\Model\Behavior\ExternalBehavior
     */
    public $External;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->External = new ExternalBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->External);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
