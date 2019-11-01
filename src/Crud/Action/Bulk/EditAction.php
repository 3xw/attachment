<?php
declare(strict_types=1);

namespace Attachment\Crud\Action\Bulk;

use Cake\Controller\Controller;
use Cake\ORM\Query;
use Crud\Action\Bulk\BaseAction;

class DeleteAction extends BaseAction
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

    debug($query);

    return true;

    /*
    $field = $this->getConfig('field');
    $value = $this->getConfig('value');
    $query->update()->set([$field => $value]);
    $statement = $query->execute();
    $statement->closeCursor();

    return (bool)$statement->rowCount();
    */
  }
}
