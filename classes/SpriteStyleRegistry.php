<?php
/**
 * The SpriteStyleRegistry class. Manage the sprite CSS styles.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteStyleRegistry implements SpriteAbstractConfigSource
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
   * This object hash
   * @var string
   */
  protected $hash;


  /**
   * Instanciate a new SpriteStyleRegistry.
   *
   * @param  CSprite $cSprite The parent object.
   * @access public
   * @return SpriteStyleRegistry This object.
   */
  public function __construct(CSprite &$cSprite)
  {
    $this->cSprite = $cSprite;

    $this->registry = array();
    $this->hash = null;
  } // __construct()

  /**
   * Get this object CSprite instance.
   * @return CSprite A CSprite instance.
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

  public function addSprite(SpriteSprite &$sprite)
  {
    $this->registry[$sprite->getKey()] = new SpriteStyleGroup($this, $sprite);

    return $this;
  } // addSprite()

  public function processCss()
  {
    $allCss = '';
    foreach($this->registry as &$styleGroup){
      $filepath = $this->getSpriteConfig()->get('rootDir') . $styleGroup->getRelativePath();
      $tempCss = $styleGroup->getCss();

      file_put_contents($filepath, $tempCss);
      $allCss .= $tempCss;
    }
    file_put_contents($this->getSpriteConfig()->get('rootDir') . $this->getRelativePath(), $allCss);
  } // processCss()

  public function getStyleNodes()
  {
    return $this->registry;
  }

  public function getStyleNode($path){
    $node = null;

    foreach($this->registry as $spriteGroup){
     if(isset($spriteGroup[$path])){
        $node = $spriteGroup[$path];
        break;
      }
    }
    return $node;
  }

  public function getCssInclude($spriteName, $imageType = null){
    $tempSprite = new SpriteSprite($this, $spriteName, $imageType);
    if(isset($this->registry[$tempSprite->getKey()])){
      $sprite = $this->registry[$tempSprite->getKey()];
      return '<link rel="stylesheet" type="text/css" title="'.$sprite->getKey().'" media="all" href="'.$sprite->getRelativePath().'" />'."\n";
    }
    return '';
  }

  public function getAllCssInclude(){
    return '<link rel="stylesheet" type="text/css" title="cSprite CSS" media="all" href="'.$this->getRelativePath().'" />'."\n";
  }

  public function getRelativePath(){
    return $this->getSpriteConfig()->get('relTmplOutputDirectory').'/'.$this->getFileName();
  }

  public function getFileName(){
    return $this->getHash().'.css';
  }

  public function getHash(){
    if(!$this->hash)
    {
      $this->hash = md5(serialize($this->registry));
    }

    return $this->hash;
  }

}

