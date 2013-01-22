<?php
/**
 * The SpriteImageRegistry class. Manage the sprite source images.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteImageRegistry implements SpriteAbstractConfigSource
{

  /**
   * The CSprite parent object.
   *
   * @var CSprite
   * @access protected
   */
  protected $cSprite;

  /**
   * The style registry.
   * @var array
   */
  protected $registry;

  /**
   * The SpriteImageWriter object.
   * @var   SpriteImageWriter
   */
  protected $spriteImageWriter;

  /**
   * Instanciate a new SpriteImageRegistry.
   *
   * @param  CSprite $cSprite The parent object.
   * @access public
   * @return SpriteImageRegistry This object.
   */
  public function __construct(CSprite &$cSprite)
  {
    $this->cSprite = $cSprite;

    $this->registry = array();

    $this->spriteImageWriter = new SpriteImageWriter($this);
  } // __construct()

  /**
   * Get this object CSprite instance.
   *
   * @access  public
   * @return  CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this->cSprite;
  } // getCSprite()

  /**
   * Get this object CSprite config instance.
   *
   * @access  public
   * @return  CSpriteConfig A CSpriteConfig instance.
   */
  public function getSpriteConfig()
  {
    return $this->cSprite->getSpriteConfig();
  } // getSpriteConfig()

  /**
   * Get this object CSprite cache manager.
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->cSprite->getSpriteCache();
  } // getSpriteCache()

  /**
   * Get this registry image write.
   * @return SpriteImageWriter a SpriteImageWriter.
   */
  public function getSpriteImageWriter()
  {
    return $this->spriteImageWriter;
  } // getSpriteImageWriter()

  /**
   * Add a image or directory path to the registry.
   * @param  string $imgPath      A relative path.
   * @param  array  $params       An associative array of parameters.
   * @return SpriteImageRegistry  This object.
   */
  public function register($imgPath, array $params = array())
  {
    $relPath = $imgPath;
    $absPath = $this->getSpriteConfig()->get('rootDir') . $relPath;

    if((! file_exists($absPath)) && file_exists($imgPath))
    {
      $absPath = $imgPath;
    }

    if(is_dir($absPath))
    {
      $files = self::buildFileList($absPath);
    }
    elseif(filesize($absPath))
    {
      $files = array($absPath);
    }
    else
    {
      throw new Exception(sprintf('cSprites error: image path "%s" does not exists.', $imgPath));
    }

    foreach($files as $imgFile)
    {
      if(! is_dir($imgFile)) // Ignore bogus directories.
      {
        $this->addImage($imgFile, $params);
      } // Ignore bogus directories.
    }

    // Process sprites after each directory addition.
    $this->processSprites();

    return $this;
  } // register()

  public function getRegistry()
  {
    return $this->registry;
  }

  public function processSprites()
  {
    if(count($this->registry))
    {
      //call_user_func(self::$packerClass.'::pack', self::$registry, self::$longestWidth, self::$longestHeight, self::$totalArea);
      foreach($this->registry as $sprite)
      {
        //First lets prepare all the sprite properties
        $sprite->prepareSprite();
        //Now lets sort it
        call_user_func($this->getSpriteConfig()->get('sorter').'::sort',&$sprite);

        //And pack the sprite
        $packer_class = $this->getSpriteConfig()->get('packer');
        $packer = new $packer_class($this);
        $packer->pack(&$sprite);

        //Write the sprite image to a file
        $this->getSpriteImageWriter()->writeImages($sprite);
        //Update all the sprite styles to the registry
        $this->getCSprite()->getStyleRegistry()->addSprite($sprite);
      }
      //SpriteStyleRegistry::processCssMetaFiles();
    }
    $this->getSpriteCache()->updateCache();

    return $this;
  } // processSprites()

  protected function loadSorter()
  {
    if (include_once 'sorters/' .$this->getSpriteConfig()->getSorter().'.php')
    {
      $classname = $this->getSpriteConfig()->getSorter();
      return new $classname;
    }
    else
    {
      throw new SpriteException ('Sorter class not found.');
    }
  }

  protected function addImage($path, $params)
  {
    $spriteName = @$params['name'];
    $imageType  = @$params['imageType'];

    try
    {
      $spriteImage = new SpriteImage($this, $path, $params);
    }
    catch(SpriteException $e)
    {
      return NULL;
    }

    $type = ($imageType)?($imageType):($spriteImage->getType());
    $tempSprite = new SpriteSprite($this, $spriteName, $type);

    if(!isset($this->registry[$tempSprite->getKey()]))
    {
      $this->registry[$tempSprite->getKey()] = $tempSprite;
    }

    $this->registry[$tempSprite->getKey()][] = $spriteImage;
  } // addImage()

  public static function buildFileList($path)
  {
    $files = array();
    $fileObjs = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach($fileObjs as $name=>$fileObj)
    {
      if($fileObj->isFile())
      {
        $files[] = $name;
      }
    }

    return $files;
  } // buildFileList()

  public function debug(){
    $output = '';
    foreach($this->registry as $type=>$imageAr)
    {
      $output .=  $type."<br>";
      foreach($imageAr as $key=>$image)
      {
        $output .= $image;
      }
    }
    return $output;
  }

}
