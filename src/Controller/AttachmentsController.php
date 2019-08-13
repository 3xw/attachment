<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Event\Event;
use Crud\Event\Subject;
use Cake\Core\Configure;

/**
* Attachments Controller
*
* @property \Attachment\Model\Table\AttachmentsTable $Attachments
*/
class AttachmentsController extends AppController
{

  use \Crud\Controller\ControllerTrait;

  public $paginate = [
    'page' => 1,
    'limit' => 18,
    'maxLimit' => 200,
    'sortWhitelist' => [
      'name', 'created', 'type', 'subtype', 'date'
    ]
  ];

  public function initialize():void
  {
    parent::initialize();

    $this->loadComponent('Crud.Crud', [
      'actions' => [
        'index' => [
          'className' => 'Crud.Index'
        ],
        'view' => [
          'className' => 'Crud.View',
        ],
        'add' =>[
          'className' => 'Crud.Add',
          'api.success.data.entity' => ['id','profile','path','type','subtype','name','size','fullpath'],
          'api.error.exception' => [
            'type' => 'validate',
            'class' => 'Attachment\Crud\Error\Exception\ValidationException'
          ],
        ],
        'edit' => [
          'className' => 'Crud.Edit',
        ],
        'delete' => [
          'className' => 'Attachment\Crud\Action\DeleteAction',
        ]
      ],
      'listeners' => [
        //'CrudCache',
        'Crud.Api',
        'Crud.ApiPagination',
        'Crud.ApiQueryLog',
        'Crud.Search'
      ]
    ]);

    $this->loadComponent('Attachment.EventDispatcher');
  }

  public function add()
  {
    return $this->Crud->execute();
  }

  public function index()
  {
    $this->Crud->on('beforePaginate', function(Event $event)
    {
      $event->getSubject()->query->contain(['Atags']);
      if(Configure::read('Attachment.translate'))
      {
        $event->getSubject()->query->find('translations');
      }
    });

    $this->Crud->on('afterPaginate', function(Event $event)
    {
      foreach ($event->getSubject()->entities as $entity)
      {
        $entity->set('fullpath', $entity->get('fullpath'));
      }
    });

    return $this->Crud->execute();
  }

  public function view($id = null)
  {
    $this->Crud->on('beforeFind', function(Event $event) {
      $event->getSubject()->query->contain(['Atags']);
      if(Configure::read('Attachment.translate'))
      {
        $event->getSubject()->query->find('translations');
      }
    });
    return $this->Crud->execute();
  }

  public function edit($id = null)
  {
    $this->Crud->on('beforeFind', function(Event $event) {
      $event->getSubject()->query->contain(['Atags']);
      if(Configure::read('Attachment.translate'))
      {
        $event->getSubject()->query->find('translations');
      }
    });
    return $this->Crud->execute();
  }

  public function delete($id)
  {
    return $this->Crud->execute();
  }

}
