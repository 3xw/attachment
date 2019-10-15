<?php
namespace Attachment\Protect;

use Aws\CloudFront\UrlSigner;

class AwsSignedUrlsProtection extends BaseProtection
{
  protected $_urlSigner;

  public function getUrlSigner()
  {
    if(!isset($this->_urlSigner)) $this->_urlSigner = new UrlSigner(
      $this->getConfig('keyPairId'),
      $this->getConfig('privateKey')
    );

    return $this->_urlSigner;
  }

  public function verify(): boolean
  {
    return false;
  }

  public function createUrl(string $url): string
  {
    return $this->getUrlSigner()->getSignedUrl($url, $this->getConfig('expires'), $this->getPolicy($url));
  }

  public function getPolicy($url)
  {
    // min requirement is DateLessThan!
    if (!$this->getConfig('DateLessThan')) return null;

    // create statment
    $statement = (object) ['Resource' => null,'Condition' => []];

    // set conditions
    // resource
    $statement->Resource = $this->getConfig('Resource')? $this->getConfig('Resource'): $url;
    // DateLessThan
    $statement->Condition['DateLessThan'] = ['AWS:EpochTime' => time() + $this->getConfig('DateLessThan') ];
    // DateGreaterThan
    if ($this->getConfig('DateGreaterThan')) $statement->Condition['DateGreaterThan'] = ['AWS:EpochTime' => time() + $this->getConfig('DateGreaterThan') ];
    // IpAddress
    if ($this->getConfig('IpAddress') && !Configure::read('debug')) $statement->Condition['IpAddress'] = ['AWS:SourceIp' => Router::getRequest()->clientIp().'\32' ];

    // JSON policy
    $policy = json_encode((object) [
      'Statement' => [$statement]
    ]);

    // return
    return $policy;
  }
}
