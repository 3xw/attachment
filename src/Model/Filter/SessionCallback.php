<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Like;
use Attachment\Model\Filter\Restriction\SessionRestrictionTrait;

class SessionCallback extends Like
{
  use SessionRestrictionTrait;

  public function process():bool
  {
    if ($this->skip()) {
        return true;
    }
    call_user_func($this->config('callback'), $this->getQuery(), $this->getArgs(), $this);
    $this->restrict();
    return true;
  }
}
