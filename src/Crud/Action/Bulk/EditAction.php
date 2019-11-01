<?php
declare(strict_types=1);

namespace Attachment\Crud\Action\Bulk;

use Cake\Controller\Controller;
use Cake\ORM\Query;
use Crud\Action\Bulk\BaseAction;

class EditAction extends BaseAction
{
  public function __construct(Controller $Controller, array $config = [])
  {
    $this->_defaultConfig['messages'] = [
      'success' => [
        'text' => 'Edit completed successfully',
      ],
      'error' => [
        'text' => 'Could not complete edit',
      ],
    ];

    parent::__construct($Controller, $config);
  }

  protected function _bulk(?Query $query = null): bool
  {
    // retrieve
    $associated = $this->getConfig('relatedModels')?? [];
    $query->contain($associated);
    $list = $query->toArray();

    // patch
    $patched = $this->_table()->patchEntities(
      $list,
      $this->_controller()->getRequest()->getData(),
      ['associated' => $associated ]
    );

    // save
    return $this->_table()->saveMany($patched, ['associated' => $associated ]);
  }
}
