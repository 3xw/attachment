<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Event\Event;
use Crud\Event\Subject;
use App\Crud\ADelete;

/**
* Attachments Controller
*
* @property \Attachment\Model\Table\AttachmentsTable $Attachments
*/
class AttachmentsController extends AppController
{


  public $paginate = [
    'page' => 1,
    'limit' => 18,
    'maxLimit' => 200,
    'sortWhitelist' => [
      'name', 'created', 'type', 'subtype', 'date'
    ]
  ];

  use \Crud\Controller\ControllerTrait;

  public function initialize(){
    parent::initialize();

    $this->loadComponent('Crud.Crud', [
      'actions' => [
        //'Crud.Index',
        'index' => [
          'className' => 'Crud.Index',
        ],
        'Crud.View',
        'add' =>[
          'className' => 'Crud.Add',
          'api.success.data.entity' => ['id','profile','path','type','subtype','name','size']
        ],
        'Crud.Edit',
        'delete' => [
          'className' => 'Attachment\Crud\Action\DeleteAction',
        ],
        //'Crud.Delete',
        /*'find' => [
        'className' => 'Crud.Index',
        'api.success.data.entity' => ['type','subtype']
        ]*/
      ],
      'listeners' => [
        //'CrudCache',
        'Crud.Api',
        'Crud.ApiPagination',
        'Crud.ApiQueryLog',
        'Crud.Search'
      ]
    ]);

  }

  public function index()
  {
    $this->Crud->on('beforePaginate', function(Event $event) {
      $this->paginate['contain'] = ['Atags'];
    });

    return $this->Crud->execute();
  }

  public function delete($id)
  {
    return $this->Crud->execute();
  }

}
