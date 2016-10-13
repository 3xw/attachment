<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;
use Cake\Utility\Inflector;

class TypesRestricted extends BaseRestriction
{
  public $query = null;

  public function process(Query $query, $settings )
  {
    if(!empty($settings['types']))
    {
      $condition = '(';
      foreach($settings['types'] as $type )
      {
        $types = explode('/',$type);
        $condition .= '(Attachments.type = "'.$types[0].'" AND Attachments.subtype ="'.$types[1].'" ) OR ';
      }
      $condition = substr($condition,0, -4).')';
      $query->andWhere([$condition]);
    }
  }
}
