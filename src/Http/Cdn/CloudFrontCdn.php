<?php
namespace Attachment\Http\Cdn;

use Aws\Exception\AwsException;
use Aws\CloudFront\CloudFrontClient;
use Aws\CloudFront\UrlSigner;
use Cake\Routing\Router;

class CloudFrontCdn extends BaseCdn
{
  public $client;
  public $signer;

  public function __construct(array $config = [])
  {
    parent::__construct($config);

    $this->client = new CloudFrontClient([
      'credentials' => $this->getConfig('credentials'),
      'region' => $this->getConfig('region'),
      'version' => $this->getConfig('version')
    ]);

    if(
      $this->getConfig('keyPairId') &&
      $this->getConfig('privateKey')
    )
    $this->signer = new UrlSigner(
      $this->getConfig('keyPairId'),
      $this->getConfig('privateKey')
    );
  }

  // https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.CloudFront.UrlSigner.html#_getSignedUrl
  public function getSignedUrl(string $url)
  {
    return $this->signer->getSignedUrl($url, $this->getConfig('expires'), $this->getPolicy($url));
  }

  public function getPolicy($url)
  {
    if(!$policy = $this->getConfig('policy')) return null;
    $p = clone $policy;

    $p->Statement->Resource = $url;
    foreach($p->Statement->Condition as $name => $c)
    {
      $value;
      switch($name)
      {
        case 'IpAddress':
        $value = ['AWS:SourceIp' => Router::getRequest()->clientIp()];
        break;

        default:
        $value = ['AWS:EpochTime' => time() + $policy->Statement->Condition[$name] ];
        break;
      }
      $p->Statement->Condition[$name] = $value;
    }

    return json_encode($p);
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
