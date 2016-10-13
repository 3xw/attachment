<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;

class BaseRestriction
{
  public $query = null;
  
  public function process(Query $query, $settings )
  {

  }
}
