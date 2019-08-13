<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Base;
use Attachment\Model\Filter\Restriction\SessionRestrictionTrait;

class SessionIndex extends Base
{
  use SessionRestrictionTrait;

  public function process():bool
  {
    if ($this->skip()) {
        return true;
    }
    $this->restrict();
    return true;
  }
}
