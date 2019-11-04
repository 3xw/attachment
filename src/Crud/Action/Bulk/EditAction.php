<?php
declare(strict_types=1);

namespace Attachment\Crud\Action\Bulk;

use Cake\Controller\Controller;
use Cake\ORM\Query;

class EditAction extends BaseJsonRestAction
{
  protected function _bulk(?Query $query = null): bool
  {
    // retrieve
    $associated = $this->getConfig('relatedModels')?? [];
    $query->contain($associated);
    $indexedList = $query->toArray();

    // patch
    $patched = [];
    foreach($indexedList as $pk => $entity)
    {
      $patched[] = $this->_table()->patchEntities(
        $entity,
        $this->subject->data[$pk],
        ['associated' => $associated ]
      );
    }
    
    // save
    return $this->_table()->saveMany($patched, ['associated' => $associated ]);
  }
}
