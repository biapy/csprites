<?php
/**
 * The SpriteImageWriter class. Create the sprite image.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteImageWriter implements SpriteAbstractConfigSource
{

  /**
   * The SpriteImageRegistry parent object
   * @var SpriteImageRegistry
   */
  protected $spriteImageRegistry;

  /**
   * Instanciate a new SpriteImageWriter.
   *
   * @param  SpriteImageRegistry $spriteImageRegistry The parent object.
   * @access public
   * @return SpriteImageWriter This object.
   */
  public function __construct(SpriteImageRegistry $spriteImageRegistry)
  {
    $this->spriteImageRegistry = $spriteImageRegistry;
  } // __construct()

  /**
   * Get this object parent SpriteImageRegistry.
   *
   * @access  public
   * @return SpriteImageRegistry A SpriteImageRegistry.
   */
  public function getSpriteImageRegistry()
  {
    return $this->spriteImageRegistry;
  } // getSpriteImageRegistry()

  /**
   * Get this object CSprite instance.
   *
   * @access  public
   * @return  CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this->spriteImageRegistry->getCSprite();
  } // getCSprite()

  /**
   * Get this object CSprite config instance.
   *
   * @access  public
   * @return  CSpriteConfig A CSpriteConfig instance.
   */
  public function getSpriteConfig()
  {
    return $this->spriteImageRegistry->getSpriteConfig();
  } // getSpriteConfig()

  /**
   * Get this object CSprite cache manager.
   *
   * @access  public
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->spriteImageRegistry->getSpriteCache();
  } // getSpriteCache()

  /**
   * Add the given SpriteSprite images to the resulting sprite image.
   *
   * @param  SpriteSprite $sprite A SpriteSprite object.
   * @access  public
   * @return SpriteImageWrite This object.
   */
  public function writeImages(SpriteSprite $sprite)
  {
    $filePath = $sprite->getRelativePath();

    $imgSize = $this->getImageSize($sprite);

    if($this->getSpriteCache()->needsCreation($filePath))
    {
      switch($sprite->getType())
      {
        case SpriteConfig::JPG :
          $this->handleJpg($sprite, $imgSize, $filePath);
          break;

        case SpriteConfig::GIF :
          $this->handleGif($sprite, $imgSize, $filePath);
          break;

        case SpriteConfig::PNG :
          $this->handlePng24($sprite, $imgSize, $filePath);
          break;

        case SpriteConfig::PNG8 :
          $this->handlePng24($sprite, $imgSize, $filePath);
          break;
      }
    }

    return $this;
  } // writeImages()

  protected function getImageSize(SpriteSprite $sprite)
  {
    $imgSize = new SpriteRectangle($this->spriteImageRegistry, 0, 0, 0, 0);
    foreach($sprite as $spriteImage)
    {
      $position = $spriteImage->getPosition();
      $fullLength = $position->left + $spriteImage->getWidth();
      if($imgSize->width < $fullLength)
      {
        $imgSize->update(0, 0, $fullLength, $imgSize->bottom);
      }
      $fullHeight = $position->top + $spriteImage->getHeight();
      if($imgSize->height < $fullHeight)
      {
        $imgSize->update(0, 0, $imgSize->right, $fullHeight);
      }
    }
    return $imgSize;
  } // getImageSize()

  protected function writeImageFile($image, $path)
  {
    $path = $this->getSpriteConfig()->get('rootDir').$path;
    $fh = fopen($path, "w+" );
    fwrite( $fh, $image );
    fclose( $fh );
  } // writeImageFile()

  protected function handleJpg(SpriteSprite $sprite, $imgSize, $filePath)
  {
    $properties = $this->getSpriteConfig()->get('imageProperties');
    $quality = $properties['jpgQuality'];

    $spriteHolder = imagecreatetruecolor($imgSize->right, $imgSize->bottom);
    foreach($sprite as $spriteImage)
    {
      $tempImage = imagecreatefromjpeg($spriteImage->getPath());
      $position = $spriteImage->getPosition();
      $margin = $spriteImage->getMargin();
      imagecopy($spriteHolder, $tempImage, $position->left + $margin->left, $position->top + $margin->top,
                0, 0, $spriteImage->getOriginalWidth(), $spriteImage->getOriginalHeight());
      imagedestroy($tempImage);
    }
    ob_start();
    imagejpeg($spriteHolder, null, $quality);
    $spriteOutput = ob_get_clean();
    imagedestroy($spriteHolder);
    $this->writeImageFile($spriteOutput, $filePath);
  } // handleJpg()

  protected function handleGif(SpriteSprite $sprite, $imgSize, $filePath)
  {
    $spriteHolder = imagecreatetruecolor($imgSize->right, $imgSize->bottom);
    imagealphablending($spriteHolder, false);
    imagesavealpha($spriteHolder,true);
    $transparent = imagecolorallocatealpha($spriteHolder, 255, 255, 255, 127);
    imagefilledrectangle($spriteHolder, 0, 0, $imgSize->right, $imgSize->bottom, $transparent);
    imagecolortransparent  ( $spriteHolder, $transparent);
    foreach($sprite as $spriteImage)
    {
      $tempImage = imagecreatefromgif($spriteImage->getPath());
      $position = $spriteImage->getPosition();
      $margin = $spriteImage->getMargin();
      imagecopy($spriteHolder, $tempImage, $position->left + $margin->left, $position->top + $margin->top,
                0, 0, $spriteImage->getOriginalWidth(), $spriteImage->getOriginalHeight());
      imagedestroy($tempImage);
    }

    ob_start();
    imagegif($spriteHolder);
    $spriteOutput = ob_get_clean();
    imagedestroy($spriteHolder);
    $this->writeImageFile($spriteOutput, $filePath);
  } // handleGif()

  protected function handlePng24(SpriteSprite $sprite, $imgSize, $filePath)
  {
    $spriteHolder = imagecreatetruecolor($imgSize->right, $imgSize->bottom);
    /*$trans_color = imagecolorallocatealpha($sprite, 0, 0, 0, 127);
    imagesavealpha($sprite, true);
    imagealphablending($sprite, true);
    imagefill($sprite, 0, 0, $trans_color);*/
    imagealphablending($spriteHolder, false);
    imagesavealpha($spriteHolder,true);
    $transparent = imagecolorallocatealpha($spriteHolder, 255, 255, 255, 127);
    imagefilledrectangle($spriteHolder, 0, 0, $imgSize->right, $imgSize->bottom, $transparent);

    foreach($sprite as $spriteImage)
    {
      $tempImage = imagecreatefrompng($spriteImage->getPath());
      $position = $spriteImage->getPosition();
      $margin = $spriteImage->getMargin();
      imagecopy($spriteHolder, $tempImage, $position->left + $margin->left, $position->top + $margin->top,
                0, 0, $spriteImage->getOriginalWidth(), $spriteImage->getOriginalHeight());
      imagedestroy($tempImage);
    }

    ob_start();
    imagepng($spriteHolder);
    $spriteOutput = ob_get_clean();
    imagedestroy($spriteHolder);
    $this->writeImageFile($spriteOutput, $filePath);
  } // handlePng24()

  protected function handlePng8(SpriteSprite $sprite, $imgSize, $filePath)
  {

    $spriteHolder = imagecreatetruecolor($imgSize->right, $imgSize->bottom);
    //$trans_color = imagecolorallocatealpha($sprite, 0, 0, 0, 127);
    //imagesavealpha($sprite, true);
    //imagealphablending($sprite, true);
    //imagefill($sprite, 0, 0, $trans_color);

    foreach($sprite as $spriteImage)
    {
      $tempImage = imagecreatefrompng($spriteImage->getPath());
      $position = $spriteImage->getPosition();
      $margin = $spriteImage->getMargin();
      imagecopy($spriteHolder, $tempImage, $position->left + $margin->left, $position->top + $margin->top,
                0, 0, $spriteImage->getOriginalWidth(), $spriteImage->getOriginalHeight());
      imagedestroy($tempImage);
    }

    //And now convert it back to PNG-8
    $png8 = imagecreate($imgSize->right, $imgSize->bottom);
    //imagesavealpha($png8, true);
    //imagealphablending($png8, true);
    //imagefill($png8, 0, 0, $trans_color);

    imagecopy($png8,$spriteHolder,0,0,0,0,$imgSize->right,$imgSize->bottom);
    imagedestroy($spriteHolder);

    ob_start();
    imagepng($png8);
    $spriteOutput = ob_get_clean();
    imagedestroy($png8);
    $this->writeImageFile($spriteOutput, $filePath);
  } // handlePng8()

}
