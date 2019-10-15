<?php
namespace Attachment\Protect;

class ProtectionRegistry
{
  /* TODO ThumbProtectionRegistry /w => CONFIGURATION_KEY_SUFFIX = '.thumbProtection';
  */
  const CONFIGURE_KEY_PREFIX = 'Attachment.profiles.';

  const CONFIGURATION_KEY_SUFFIX = '.protection';

  protected static $instances = [];

  public static function retrieve($alias)
  {
    if (!isset(static::$instances[$alias])) static::$instances[$alias] = static::create($alias);
    return static::$instances[$alias];
  }

  public function static exists($alias)
  {
    return static::existsAndInstanceOf(static::getAliasConfigKey($alias));
  }

  public static function reset()
  {
    static::$instances = [];
    return static;
  }

  protected static function create($alias)
  {
    $aliasConfigKey = static::getAliasConfigKey($alias);
    if (!Configure::check($aliasConfigKey)) throw new \InvalidArgumentException('Protection for profile "' . $alias . '" not configured');

    if (static::existsAndInstanceOf($aliasConfigKey)) return Configure::read($aliasConfigKey);

    throw new \InvalidArgumentException('Protection "' . $alias . '" is not an instance of ');
  }

  protected static function existsAndInstanceOf($aliasConfigKey)
  {
    return Configure::check($aliasConfigKey) &&
      Configure::read($aliasConfigKey) instanceof ProtectionInterface;
  }

  public function static getAliasConfigKey($alias)
  {
    return static::CONFIGURE_KEY_PREFIX . $alias . static::CONFIGURATION_KEY_SUFFIX;
  }
}
