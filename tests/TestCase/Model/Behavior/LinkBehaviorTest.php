<?php
namespace Attachment\Test\TestCase\Model\Behavior;

use ArrayObject;
use Attachment\Model\Behavior\LinkBehavior;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LinkBehaviorTest extends TestCase
{
    public $behavior;
    public $table;

    public function setUp()
    {
        parent::setUp();
        $this->table = TableRegistry::get('LinkBehaviorAttachments', ['table' => 'attachments']);
        $this->behavior = new LinkBehavior($this->table, ['link_field' => 'link', 'file_field' => 'path']);
    }

    public function tearDown()
    {
        TableRegistry::clear();
        unset($this->behavior, $this->table);
        parent::tearDown();
    }

    public function testMarshalLinkSetsTypeAndPath()
    {
        $data = new ArrayObject(['name' => 'Mon lien', 'link' => 'https://example.com/doc.pdf']);
        $this->behavior->beforeMarshal(new Event('Model.beforeMarshal'), $data, new ArrayObject());

        $this->assertSame('link', $data['type']);
        $this->assertSame('https://example.com/doc.pdf', $data['path']);
        $this->assertSame(0, $data['size']);
        $this->assertSame('external', $data['profile']);
        $this->assertSame(md5('https://example.com/doc.pdf'), $data['md5']);
        $this->assertArrayNotHasKey('link', (array)$data);
    }

    public function testMarshalNoLinkDoesNothing()
    {
        $data = new ArrayObject(['name' => 'Un fichier']);
        $this->behavior->beforeMarshal(new Event('Model.beforeMarshal'), $data, new ArrayObject());

        $this->assertArrayNotHasKey('type', (array)$data);
        $this->assertArrayNotHasKey('path', (array)$data);
    }
}
