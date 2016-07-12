<?php
namespace Attachment\Test\TestCase\Model\Table;

use Attachment\Model\Table\AttachmentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Attachment\Model\Table\AttachmentsTable Test Case
 */
class AttachmentsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Attachment\Model\Table\AttachmentsTable
     */
    public $Attachments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.attachment.attachments',
        'plugin.attachment.atags',
        'plugin.attachment.attachments_atags',
        'plugin.attachment.highlights',
        'plugin.attachment.attachments_highlights',
        'plugin.attachment.paths',
        'plugin.attachment.attachments_paths',
        'plugin.attachment.visits',
        'plugin.attachment.attachments_visits'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Attachments') ? [] : ['className' => 'Attachment\Model\Table\AttachmentsTable'];
        $this->Attachments = TableRegistry::get('Attachments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Attachments);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
