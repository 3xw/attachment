<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Base;
use Attachment\Model\Filter\Restriction\SessionRestrictionTrait;

class SessionIndex extends Base
{
  use SessionRestrictionTrait;

  public function process()
  {
    if ($this->skip()) {
        return;
    }
    $this->restrict();
  }
}
