<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;

/**
 * Atags Controller
 *
 * @property \Attachment\Model\Table\AtagsTable $Atags
 */
class AtagsController extends AppController
{
  use \Crud\Controller\ControllerTrait;

  public $paginate = [
        'limit' => 100000,
        'order' => [
            'Atags.name' => 'ASC'
        ]
    ];

  public function initialize(){
    parent::initialize();

    $this->loadComponent('Crud.Crud', [
      'actions' => [
        'Crud.Index',
        /*
        'Crud.View',
        'add' =>[
          'className' => 'Crud.Add',
          'api.success.data.entity' => ['id','path','type','subtype','name','size']
        ],
        'Crud.Edit',
        'Crud.Delete'*/
      ],
      'listeners' => [
        //'CrudCache',
        'Crud.Api',
        //'Crud.ApiPagination',
        'Crud.ApiQueryLog',
        //'Crud.Search'
      ]
    ]);
  }
}
