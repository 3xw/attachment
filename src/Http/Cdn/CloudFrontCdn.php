<?php
namespace Attachment\Http\Cdn;

use Aws\Exception\AwsException;
use Aws\CloudFront\CloudFrontClient;

class CloudFrontCdn extends BaseCdn
{
  public $client;

  public function __construct(array $config = [])
  {
    parent::__construct($config);

    $this->client = new CloudFrontClient([
      'credentials' => $this->getConfig('credentials'),
      'region' => $this->getConfig('region'),
      'version' => $this->getConfig('version')
    ]);
  }

  public function clear(array $paths = [])
  {
    $paths = empty($paths)? $this->getConfig('paths'):$paths;

    try {

      $result = $this->client->createInvalidation([
        'DistributionId' => $this->getConfig('id'),
        'InvalidationBatch' => [
          'CallerReference' => (string) times(),
          'Paths' => ['Items' => $paths,'Quantity' => 1],
        ]
      ]);

    }
    catch (AwsException $e)
    {
      $error = $e->getMessage();
      return false;
    }

    return $result;
  }
}
