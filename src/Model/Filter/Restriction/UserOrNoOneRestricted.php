<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Routing\Router;

class UserOrNoOneRestricted extends BaseRestriction
{
  public static function process(Query $query, $noArgs )
  {
    $identity = Router::getRequest()->getAttribute('identity');
    $userId = $identity? $identity->getIdentifier(): 'no-user-id';

    $query->andWhere([
      'OR' => [
        'Attachments.user_id' => $userId,
        'Attachments.user_id' => ''
      ]
    ]);
  }
}
