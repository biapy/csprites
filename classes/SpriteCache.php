<?php
/**
 * The SpriteCache class. Manage the sprite filesystem cache.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteCache
{

  /**
   * The CSprite parent object.
   *
   * @var CSprite
   * @access protected
   */
  protected $cSprite;

  /**
   * List of cached files.
   * @var array
   * @access  protected
   */
  protected $cacheArray;

  /**
   * Instanciate a new SpriteCache.
   *
   * @param  CSprite $cSprite The parent object.
   * @access public
   * @return SpriteCache This object.
   */
  public function __construct(CSprite &$cSprite)
  {
    $this->cSprite = $cSprite;

    $this->cacheArray = array();
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
   * Check if the given cache is outdated.
   *
   * @param   string $absFile A absolute cache file path.
   * @access  public
   * @return  boolean  True if the cache is outdated, false otherwise.
   */
  public function needsCreation($absFile)
  {
    $cacheTime = $this->getSpriteConfig()->get('cacheTime') * 60;

    $this->cacheArray[] = $absFile;

    //If Cache time is 0 always create
    if(!$cacheTime){
      return true;
    }

    if(file_exists($absFile)){
      return (time() - $cacheTime < filemtime($file))?(false):(true);
    }
    return true;
  } // needsCreation()

  /**
   * Delete outdated cache files.
   *
   * @access  public
   * @return  SpriteCache  This object.
   */
  public function updateCache()
  {
    $cacheTime = $this->getSpriteConfig()->get('cacheTime') * 60;

    $tmplFiles  = CSpriteTools::buildFileList($this->getSpriteConfig()->get('rootDir') . $this->getSpriteConfig()->get('relTmplOutputDirectory'));
    $imageFiles = CSpriteTools::buildFileList($this->getSpriteConfig()->get('rootDir') . $this->getSpriteConfig()->get('relImageOutputDirectory'));

    $files = array_merge($tmplFiles, $imageFiles);
    foreach($files as $file)
    {
      if(file_exists($file) && (time() - $cacheTime > filemtime($file)))
      {
        unlink($file);
      }
    }

    return $this;
  } // updateCache()

}
