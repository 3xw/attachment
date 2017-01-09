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
    /*
    $this->Crud->on('afterDelete', function(Event $event) {
      if (!$event->subject()->success) {
        $event->stopPropagation();
        debug($event->subject()->youpi);
        return false;
        //$this->log("Delete failed for entity " . $event->subject()->id);
        //return 'unable to delete this Attachment. This attachment looks beeing in use by an other record. Please detatch the attachment to related record an then try to delete it again.';
      }
    });


    try
    {
      return $this->Crud->execute();
    }
    catch (\PDOException $e)
    {
      $subject = new Subject([
        'success' => false,
        'id' => $id,
        'message' => 'unable to delete this Attachment. This attachment looks beeing in use by an other record. Please detatch the attachment to related record an then try to delete it again.'
      ]);
      $this->Crud->trigger('afterDelete',$subject);
      $this->redirect(['action' => 'index']);
    }*/

    return $this->Crud->execute();
  }

}
