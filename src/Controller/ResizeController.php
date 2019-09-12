<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Attachment\Filesystem\FilesystemRegistry;
use Cake\Filesystem\Folder;
use Cake\Core\App;
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
    // test profile
    if(!Configure::check('Attachment.profiles.'.$profile) || $profile == 'thumbnails' ){ throw new NotFoundException(); }

    // test $dim
    preg_match_all('/([a-z])([0-9]*-[0-9]*|[0-9]*)/', $dim, $dims, PREG_SET_ORDER);
    if(empty($dims)){ throw new NotFoundException(); }

    // if empty $image
    if(empty($image)){ throw new NotFoundException(); }
    $image = implode("/", $image);

    //test if webp
    preg_match_all('/([a-zA-Z0-9_:\/.èüéöàä\$£ç\&%#*+?=,;~-]*)\.webp$/', $image, $webp, PREG_SET_ORDER);
    if(!empty($webp))
    {
      if(!Configure::read('Attachment.thumbnails.compression.cwebp') || !Configure::read('Attachment.thumbnails.compression.convert'))
      {
        throw new NotFoundException('wepb and/or convert (ImageMagick) not installed!!');
      }
      $extsToTest = ['.jpg','.jpeg','.png','.JPG','.JPEG','.PNG'];
      $fileFound = false;
      foreach($extsToTest as $ext)
      {
        $image = $webp[0][1].$ext;
        if($this->_filesystem($profile)->has($image))
        {
          $fileFound = true;
          break;
        }
      }
      if(!$fileFound)
      {
        throw new NotFoundException();
      }
    }else
    {
      // look for image
      if(!$this->_filesystem($profile)->has($image))
      {
        throw new NotFoundException();
      }
    }

    // test if image
    $mimetypes = ['image/jpeg','image/png','image/gif'];
    if($profile == 'external')
    {
      $url = str_replace(':/','://',$image);
      $mimetype = get_headers($url, 1)["Content-Type"];
    }else{
      $mimetype = $this->_filesystem($profile)->getMimetype($image);
    }
    if(!in_array($mimetype, $mimetypes))
    {
      throw new NotFoundException('Invalide file type! Image only!');
    }

    /* all checks god let's resize...
    ********************************************/
    // set dimentions
    $align = $crop = $height = $width = $quality = 0;
    for( $i = 0; $i < count($dims); $i++ ){
      switch($dims[$i][1]){
        case 'a': $align = (int) $dims[$i][2]; break; // 0 to 8
        case 'c': $crop = explode('-',$dims[$i][2]); break; // 16-9 for 16/9
        case 'h': $height = (int) $dims[$i][2]; break; // height in px
        case 'w': $width = (int) $dims[$i][2]; break; // width in px
        case 'q': $quality = (int) $dims[$i][2]; break; // width in px
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

    // create folders
    $folder = $profile.DS.$dim.DS.substr($image, 0, strrpos($image, '/') );
    $folder = $this->_filesystem('thumbnails')->getAdapter()->applyPathPrefix($folder);
    $folder = new Folder($folder, true, 0777);

    // quality
    $quality = $quality? $quality: Configure::read('Attachment.thumbnails.compression.quality');
    $encodingQuality = ((Configure::read('Attachment.thumbnails.compression.jpegoptim') && ($mimetype == 'image/jpeg' || $mimetype == 'image/jpeg') )
      || (Configure::read('Attachment.thumbnails.compression.pngquant') && $mimetype == 'image/png' ))? 100: $quality;

    // write image
    $img->encode($mimetype, $encodingQuality);
    if(!empty($webp))
    {
      $pos = strrpos($image, '/');
      if($pos === false)
      {
        $image = uniqid('tmp_').'_'.$image;
      }else
      {
        $image = substr($image, 0, $pos + 1).uniqid('tmp_').'_'.substr($image, $pos + 1);
      }
    }
    $path = $profile.DS.$dim.DS.$image;
    $this->_filesystem('thumbnails')->put($profile.DS.$dim.DS.$image, $img);
    $path = $this->_filesystem('thumbnails')->getAdapter()->applyPathPrefix($path);


    // jpegoptim
    if(empty($webp) && Configure::read('Attachment.thumbnails.compression.jpegoptim') && ($mimetype == 'image/jpeg' || $mimetype == 'image/jpeg') )
    {
      $jpegoptim = Configure::read('Attachment.thumbnails.compression.jpegoptim');
      exec("$jpegoptim -m $quality --all-progressive --strip-all --strip-iptc --strip-icc $path");
    }
    // pngquant
    if(empty($webp) && Configure::read('Attachment.thumbnails.compression.pngquant') && $mimetype == 'image/png' )
    {
      $pngquant = Configure::read('Attachment.thumbnails.compression.pngquant');
      exec("$pngquant $path --ext .png --quality $quality --force");
    }

    // cwebp jpeg && png
    if(!empty($webp) && Configure::read('Attachment.thumbnails.compression.cwebp') && Configure::read('Attachment.thumbnails.compression.convert') && ($mimetype == 'image/jpeg' || $mimetype == 'image/jpeg' || $mimetype == 'image/png') )
    {

      $output = $profile.DS.$dim.DS.$webp[0][0];
      $output = $this->_filesystem('thumbnails')->getAdapter()->applyPathPrefix($output);

      // CMYK to RGB
      if(($mimetype == 'image/jpeg' || $mimetype == 'image/jpeg'))
      {
        $convert = Configure::read('Attachment.thumbnails.compression.convert');
        $imageSize = getimagesize($path);
        if($imageSize['channels'] == 4)
        {
          exec("$convert -colorspace RGB $path $path 2>&1", $out);
        }
      }

      // FORMAT TO webp
      $cwebp = Configure::read('Attachment.thumbnails.compression.cwebp');
      exec("$cwebp -q $quality $path -o $output 2>&1", $out);

      // clean
      exec("rm $path");

      // set new path
      $path = $output;
    }

    // delete file when over
    if(Configure::read('Attachment.profiles.thumbnails.cdn')) register_shutdown_function(function() use($path) { unlink($path); });

    // send file
    $response = $this->response;
    $response = $response->withStringBody(file_get_contents($path));
    $response = $response->withHeader('Content-Type', $mimetype);
    //$response = $response->withDownload('filename_for_download.ics');
    return $response;

  }
}
