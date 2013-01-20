<?php
class SpriteImageRegistry{

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

  public function __construct(cSprite &$cSprite)
  {
    $this->cSprite = $cSprite;

    $this->registry = array();
  } // __construct()

  /**
   * Get this object CSprite instance.
   * @return CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this->cSprite;
  } // getCSprite()

  public function register($imgPath, array $params = array())
  {
    $relPath = $imgPath;
    $absPath = SpriteConfig::get('rootDir') . $relPath;

    if((! file_exists($absPath)) && file_exists($imgPath))
    {
      $absPath = $imgPath;
    }

    if(is_dir($absPath))
    {
      $files = $this->buildFileList($absPath);
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
  }

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
        call_user_func(SpriteConfig::get('sorter').'::sort',&$sprite);
        //And pack the sprite
        call_user_func(SpriteConfig::get('packer').'::pack', &$sprite);
        //Write the sprite image to a file
        SpriteImageWriter::writeImages($sprite);
        //Update all the sprite styles to the registry
        $this->getCSprite()->getStyleRegistry()->addSprite($sprite);
      }
      //SpriteStyleRegistry::processCssMetaFiles();
    }
    SpriteCache::updateCache();
  }

  protected function loadSorter()
  {
    if (include_once 'sorters/' .SpriteConfig::getSorter().'.php')
    {
      $classname = SpriteConfig::getSorter();
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
      $spriteImage = new SpriteImage($path, $params);
    }
    catch(SpriteException $e)
    {
      return NULL;
    }

    $type = ($imageType)?($imageType):($spriteImage->getType());
    $tempSprite = new SpriteSprite($spriteName, $type);

    if(!isset($this->registry[$tempSprite->getKey()]))
    {
      $this->registry[$tempSprite->getKey()] = $tempSprite;
    }

    $this->registry[$tempSprite->getKey()][] = $spriteImage;
  }

  public function buildFileList($path)
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
  }

  public function debug(){
    $output = '';
    foreach(self::$registry as $type=>$imageAr)
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
?>
