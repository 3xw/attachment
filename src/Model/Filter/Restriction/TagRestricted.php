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
        $alias1 = 'At'.Inflector::camelize($tag);
        $query->innerJoin([$alias1 => 'attachments_atags'],[$alias1.'.attachment_id = Attachments.id']);
        $alias2 = 'Atags'.Inflector::camelize($tag);
        $query->innerJoin([$alias2 => 'atags'],[
          $alias2.'.id = '.$alias1.'.atag_id AND ('.$alias2.'.name = "'.$tag.'" OR '.$alias2.'.slug = "'.$tag.'" )'
        ]);
      }
    }
  }
}
