<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;
use Cake\Utility\Text;

class TagOrRestricted extends BaseRestriction
{
  public static function process(Query $query, $atags )
  {
    if(empty($atags)) return;
    if(!is_array($atags)) $atags = [$atags];

    $query->innerJoin(['AAtags' => 'attachments_atags'],['AAtags.attachment_id = Attachments.id']);
    $query->innerJoin(['Atags' => 'atags'],['Atags.id = AAtags.atag_id']);
    $query->group(['Attachments.id']);

    $where = ['OR' => []];

    foreach($atags as $tag )
    {
      array_push($where['OR'],[
        'OR' => [
          'Atags.name' => $tag,
          'Atags.slug' => Text::slug($tag,'-')
        ]
      ]);
    }

    $query->where($where);
  }
}
