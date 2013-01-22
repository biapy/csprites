<?php
/**
 * The SpriteTemplate class. Describe a PHP template file used to generate output.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteTemplate implements SpriteAbstractConfigSource
{
  protected $relPath;
  protected $outputName;
  protected $preprocessName;
  protected $outputPath;

  /**
   * The SpriteConfig source object.
   * @var SpriteAbstractConfigSource
   */
  protected $spriteConfigSource;


  /**
   * Instanciate a new SpriteTemplate.
   *
   * @param SpriteAbstractConfigSource $spriteConfigSource  A SpriteConfig source.
   * @param string          $relPath          Relative path to the template.
   * @param string          $outputName       Template output name.
   * @param string          $outputPath       Template output path.
   * @return SpriteTemplate This object.
   */
  public function __construct(SpriteAbstractConfigSource &$spriteConfigSource, $relPath, $outputName, $outputPath)
  {
    $this->spriteConfigSource = $spriteConfigSource;

    $this->relPath = $relPath;
    $this->outputName = $outputName;
    $this->outputPath = ($outputPath)?($outputPath):($this->getSpriteConfig()->get('relTmplOutputDirectory'));
    $this->preprocessName = md5($relPath).'.php';
    if($outputPath)
    {
      if(!is_dir($this->getSpriteConfig()->get('rootDir').$outputPath))
      {
        throw new SpriteException($outputPath.' - this template output path is not a valid directory.');
      }

      if(!is_writable($this->getSpriteConfig()->get('rootDir').$outputPath))
      {
        throw new SpriteException($outputPath.' - this template output path is not writeable.');
      }
    }
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

  public function getRelativePath()
  {
    return $this->relPath;
  }

  public function getOutputName()
  {
    return $this->outputName;
  }

  public function getPreprocessName()
  {
    return $this->preprocessName;
  }

  public function getRelOutputPath()
  {
    return $this->outputPath.'/'.$this->outputName;
  }

  public function getOutputPath()
  {
    return $this->outputPath;
  }

  public function processTemplate()
  {
    //$absPath = $this->getSpriteConfig()->get('rootDir')
  }

}
