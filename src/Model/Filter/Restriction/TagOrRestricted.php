<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;
use Cake\Utility\Inflector;

class TagOrRestricted extends BaseRestriction
{
  public $query = null;

  public function process(Query $query, $settings )
  {
    if(!empty($settings['atags']))
    {

      $query->innerJoin(['AAtags' => 'attachments_atags'],['AAtags.attachment_id = Attachments.id']);
      $query->innerJoin(['Atags' => 'atags'],['Atags.id = AAtags.atag_id']);

      $where = ['OR' => []];

      foreach($settings['atags'] as $tag )
      {
        array_push($where['OR'],[
          'OR' => [
            'Atags.name' => $tag,
            'Atags.slug' => $tag
          ]
        ]);
      }

      $query->where($where);
    }
  }
}