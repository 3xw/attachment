<?php
declare(strict_types=1);

namespace Attachment\Crud\Action\Bulk;

use Cake\Controller\Controller;
use Cake\ORM\Query;

class DeleteAction extends BaseAction
{
  public function __construct(Controller $Controller, array $config = [])
  {
    $this->_defaultConfig['messages'] = [
      'success' => [
        'text' => 'Delete completed successfully',
      ],
      'error' => [
        'text' => 'Could not complete deletion',
      ],
    ];

    parent::__construct($Controller, $config);
  }

  protected function _bulk(?Query $query = null): bool
  {
    $query = $query->delete();
    try {
      $statement = $query->execute();
    } catch (\PDOException $e)
    {
      return false;
    }

    $statement = $query->execute();
    $statement->closeCursor();
    return (bool)$statement->rowCount();
  }
}
