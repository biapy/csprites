<?php
/**
 * The SpriteDefaultCssParser class. Parse a CSS file.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteDefaultCssParser implements SpriteAbstractParser, SpriteAbstractConfigSource
{

  /**
   * The SpriteConfig source object.
   * @var SpriteAbstractConfigSource
   */
  protected $spriteConfigSource;

  /**
   * Instanciate a new SpriteDefaultCssParser.
   *
   * @param  SpriteImageRegistry $spriteImageRegistry The parent object.
   * @access public
   * @return SpriteDefaultCssParser This object.
   */
  public function __construct(SpriteAbstractConfigSource &$spriteConfigSource)
  {
    $this->spriteConfigSource = $spriteConfigSource;
  } // __construct()

  /**
   * Get this object parent SpriteAbstractConfigSource.
   *
   * @access  public
   * @return SpriteAbstractConfigSource A SpriteConfig source.
   */
  public function getSpriteConfigSource()
  {
    return $this->spriteConfigSource;
  } // getSpriteConfigSource()

  /**
   * Get this object CSprite instance.
   *
   * @access  public
   * @return  CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this->spriteConfigSource->getCSprite();
  } // getCSprite()

  /**
   * Get this object CSprite config instance.
   *
   * @access  public
   * @return  CSpriteConfig A CSpriteConfig instance.
   */
  public function getSpriteConfig()
  {
    return $this->spriteConfigSource->getSpriteConfig();
  } // getSpriteConfig()

  /**
   * Get this object CSprite cache manager.
   *
   * @access  public
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->spriteConfigSource->getSpriteCache();
  } // getSpriteCache()

  public function parse($relFilePath)
  {
    $absPath = $this->getSpriteConfig()->get('rootDir');
    if(!file_exists($absPath))
    {
      throw new SpriteException($absPath.' : file does not exist');
    }

    $contents = file_get_contents($absPath);
    return $this->parseFile($contents);
  } // parse()

  protected function parseFile($contents)
  {
    return null;
  } // parseFile()
}
