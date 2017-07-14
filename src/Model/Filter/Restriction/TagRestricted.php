<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\ORM\Query;
use Cake\Utility\Inflector;

class TagRestricted extends BaseRestriction
{
  public $query = null;

  public function process(Query $query, $settings )
  {

    if(!empty($settings['atags']))
    {

      foreach($settings['atags'] as $tag )
      {
        $alias = Inflector::classify(str_replace(['-'],[''],$tag));
        $query->innerJoin([$alias.'AAtags' => 'attachments_atags'],[$alias.'AAtags.attachment_id = Attachments.id']);
        $query->innerJoin([$alias.'Atags' => 'atags'],[$alias.'Atags.id = '.$alias.'AAtags.atag_id']);
        array_push($where,[
          'OR' => [
            $alias.'Atags.name' => $tag,
            $alias.'Atags.slug' => $tag
          ]
        ]);
      }

      $query->where($where);
    }
  }
}
