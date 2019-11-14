<?php
declare(strict_types=1);

namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Http\Exception\BadRequestException;
use Cake\Event\Event;

class AarchivesController extends AppController
{
  use \Crud\Controller\ControllerTrait;

  public $paginate = [
    'page' => 1,
    'limit' => 20,
    'order' => [
      'Aarchives.created' => 'DESC'
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
        'add' =>[
          'className' => 'Crud.Add'
        ],
        'delete' => [
          'className' => 'Attachment\Crud\Action\DeleteAction',
        ]
      ],
      'listeners' => [
        //'CrudCache',
        'Crud.Api',
        'Crud.RelatedModels',
        'Crud.ApiPagination',
        'Crud.ApiQueryLog',
      ]
    ]);

    $this->loadComponent('Attachment.EventDispatcher');
  }

  public function index()
  {
    // get user_id
    $identity = $this->getRequest()->getAttribute('identity');
    $identity = $identity ?? [];
    $loggedUserId = $identity['id'] ?? null;

    // find by user_id
    $this->Crud->on('beforePaginate', function($event) use($loggedUserId)
    {
      $event->getSubject()->query->where(['Aarchives.user_id' => $loggedUserId]);
    });

    return $this->Crud->execute();
  }

  public function add()
  {
    $ids = $this->getRequest()->getData('ids');
    if(!$this->getRequest()->is('POST') || empty($ids) || !is_array($ids)) throw new BadRequestException('Need POST request with an ids array!');


    // SNS
    $this->Crud->on('afterSave', function($event)
    {
      if ($event->getSubject()->success)
      {
        $this->log("The entity was saved successfully");
      }
    });

    return $this->Crud->execute();
  }
}
