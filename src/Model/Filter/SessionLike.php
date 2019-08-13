<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Like;
use Attachment\Model\Filter\Restriction\SessionRestrictionTrait;

class SessionLike extends Like
{
  use SessionRestrictionTrait;

  public function process():bool
  {
    if ($this->skip()) {
        return true;
    }
    parent::process();
    $this->restrict();
    return true;
  }
}
