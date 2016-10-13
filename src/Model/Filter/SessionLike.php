<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Like;
use Attachment\Model\Filter\Restriction\SessionRestrictionTrait;

class SessionLike extends Like
{
  use SessionRestrictionTrait;

  public function process()
  {
    if ($this->skip()) {
        return;
    }
    parent::process();
    $this->restrict();
  }
}
