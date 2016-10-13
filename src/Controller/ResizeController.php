<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Attachment\Fly\FilesystemRegistry;
use Intervention\Image\ImageManagerStatic as Image;

/**
* Attachments Controller
*
* @property \Attachment\Model\Table\AttachmentsTable $Attachments
*/
class ResizeController extends AppController
{
  private function _filesystem($profile)
  {
    return FilesystemRegistry::retrieve($profile);
  }

  public function proceed($profile, $dim, ...$image )
  {
    //debug(Configure::read('Attachment'));
    //die();

    // test profile
    if(!Configure::check('Attachment.profiles.'.$profile) || $profile == 'cache' ){ throw new NotFoundException(); }

    // test $dim
    preg_match_all('/([a-z])([0-9]*-[0-9]*|[0-9]*)/', $dim, $dims, PREG_SET_ORDER);
    if(empty($dims)){ throw new NotFoundException(); }

    // if empty $images
    if(empty($image)){ throw new NotFoundException(); }
    $image = implode("/", $image);

    // look for image
    if(!$this->_filesystem($profile)->has($image))
    {
      throw new NotFoundException();
    }

    // test if image
    $mimetypes = ['image/jpeg','image/png','image/gif'];
    $mimetype = $this->_filesystem($profile)->getMimetype($image);
    if(!in_array($mimetype, $mimetypes))
    {
      throw new NotFoundException('Invalide file type! Image only!');
    }

    /* all checks god let's resize...
    ********************************************/
    // set dimentions
    $align = $crop = $height = $width = 0;
    for( $i = 0; $i < count($dims); $i++ ){
      switch($dims[$i][1]){
        case 'a': $align = (int) $dims[$i][2]; break; // 0 to 8
        case 'c': $crop = explode('-',$dims[$i][2]); break; // 16-9 for 16/9
        case 'h': $height = (int) $dims[$i][2]; break; // height in px
        case 'w': $width = (int) $dims[$i][2]; break; // width in px
      }
    }

    // check crop
    if(!empty($crop) && count($crop) < 2)
    {
      throw new NotFoundException('Invalide args!');
    }

    // retrieve image
    $contents = $this->_filesystem($profile)->read($image);
    Image::configure(['driver' => Configure::read('Attachment.thumbnails.driver')]);
    $img = Image::make($contents);

    // get original sizes
    $w = $img->width();
    $h = $img->height();
    $ocr = $w/$h;

    // crop implicite
    if( $width && $height )
    {
      $cr = $width/$height;
      $s = $width? ($ocr>$cr? (($width/$cr)*$ocr)/$w : $width/$w) : ($ocr>$cr? $height/$h : (($height*$cr)/$ocr)/$h);
    }

    // crop explicite
    if( ( $width || $height ) && $crop )
    {
      $cr = (int) $crop[0] / (int) $crop[1];
      $s = $width? ($ocr>$cr? (($width/$cr)*$ocr)/$w : $width/$w) : ($ocr>$cr? $height/$h : (($height*$cr)/$ocr)/$h);
    }

    // simple resize
    if( ( $width || $height ) && !$crop )
    {
      $cr = $ocr;
      $s = $width? $width/$w : $height/$h;
    }

    // last sizes we need
    $nw = $s*$w; $nh = $s*$h;
    $fw = $ocr>$cr? $nh*$cr : $nw;
    $fh = $ocr>$cr? $nh : $nw/$cr;
    $nw = round($nw); $nh = round($nh); $fw = round($fw); $fh = round($fh);

    // resize...
    $img->resize($nw,$nh);

    // crop and align :)
    switch($align)
    {
      case 0: $img->crop($fw,$fh); break;
      case 1: $img->crop($fw,$fh, ($nw-$fw)/2, 0 ); break;
      case 2: $img->crop($fw,$fh, $nw-$fw, ($nh-$fh)/2 ); break;
      case 3: $img->crop($fw,$fh, ($nw-$fw)/2, $nh-$fh ); break;
      case 4: $img->crop($fw,$fh, 0, ($nh-$fh)/2 ); break;
      case 5: $img->crop($fw,$fh, $nw-$fw, 0 ); break;
      case 6: $img->crop($fw,$fh, $nw-$fw, $nh-$fh ); break;
      case 7: $img->crop($fw,$fh, 0, $nh-$fh ); break;
      case 8: $img->crop($fw,$fh, 0, 0 ); break;
    }

    // write image
    $img->encode($mimetype);
    $path = $profile.DS.$dim.DS.$image;
    $this->_filesystem('cache')->put($profile.DS.$dim.DS.$image, $img);
    $path = $this->_filesystem('cache')->getAdapter()->applyPathPrefix($path);

    // send file
    $this->response->file($path);
    return $this->response;

  }
}
