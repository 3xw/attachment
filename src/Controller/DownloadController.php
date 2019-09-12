<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Attachment\Utility\Token;
use Attachment\Filesystem\Downloader;

class DownloadController extends AppController
{

  public function test()
  {
    debug((new Token)->encode('837959d9da68ae252c33d01431559531'));
  }

  public function file($token)
  {
    // get Attachment
    $attachment = $this->loadModel('Attachment.Attachments')->find()
    ->where(['md5' => (new Token)->decode($token)])
    ->firstOrFail();

    // serve
    $response = $this->response->withFile((new Downloader)->download($attachment));
    $response = $response->withHeader('Content-Type', $attachment->type.'/'.$attachment->subtype);
    $response = $response->withDownload($attachment->name);
    return $response;
  }
}
