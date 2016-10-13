<?php

namespace Attachment\Fly;

use WyriHaximus\FlyPie\FilesystemRegistry as WyriHaximusFilesystemRegistry;

class FilesystemRegistry extends WyriHaximusFilesystemRegistry
{
  const CONFIGURE_KEY_PREFIX = 'Attachment.profiles.';
}
