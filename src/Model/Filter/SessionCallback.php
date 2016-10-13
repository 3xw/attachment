<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Like;
use Attachment\Model\Filter\Restriction\SessionRestrictionTrait;

class SessionCallback extends Like
{
  use SessionRestrictionTrait;

  public function process()
  {
    if ($this->skip()) {
        return;
    }
    call_user_func($this->config('callback'), $this->query(), $this->args(), $this);
    $this->restrict();
  }
}
