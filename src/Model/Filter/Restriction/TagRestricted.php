<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Cake\Utility\Text;

class TagRestricted extends BaseRestriction
{
  public static function process(Query $query, $atags )
  {
    if(empty($atags)) return;
    if(!is_array($atags)) $atags = [$atags];

    $where = [];
    foreach($atags as $tag )
    {
      $alias = Inflector::classify(str_replace(['-'],[''],Text::slug($tag,'-')));
      $query->innerJoin([$alias.'AAtags' => 'attachments_atags'],[$alias.'AAtags.attachment_id = Attachments.id']);
      $query->innerJoin([$alias.'Atags' => 'atags'],[$alias.'Atags.id = '.$alias.'AAtags.atag_id']);
      array_push($where,[
        'OR' => [
          $alias.'Atags.name' => $tag,
          $alias.'Atags.slug' => Text::slug($tag,'-')
        ]
      ]);
    }

    $query->where($where);
  }
}
