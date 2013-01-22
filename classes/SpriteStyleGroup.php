<?php
/**
 * The SpriteStyleGroup class. Define a CSS styles group.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteStyleGroup extends ArrayObject implements SpriteHashable, SpriteAbstractConfigSource
{
  protected $spriteStyleNodes;
  protected $sprite;
  protected $backgroundStyleNode;

  protected $hash;

  /**
   * The SpriteConfig source object.
   * @var SpriteAbstractConfigSource
   */
  protected $spriteConfigSource;

  /**
   * Instanciate a new SpriteImageWriter.
   * 
   * @param SpriteAbstractConfigSource $spriteConfigSource  A SpriteConfig source.
   * @param SpriteSprite               $sprite              A SpriteSprite.
   * @access public
   * @return SpriteStyleGroup This object.
   */
  public function __construct(SpriteAbstractConfigSource &$spriteConfigSource, SpriteSprite &$sprite)
  {
    $this->spriteConfigSource = $spriteConfigSource;

    $this->spriteStyleNodes = array();
    $this->sprite = $sprite;
    parent::__construct($this->spriteStyleNodes, ArrayObject::ARRAY_AS_PROPS);

    $this->backgroundStyleNode = new SpriteStyleNode($this, null, 'sprite'.md5($this->sprite->getRelativePath()), null, $this->sprite->getRelativePath());
    foreach($this->sprite as $spriteImage)
    {
      $this->addStylesToGroup($spriteImage);
    }

    $this->hash = null;
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

  public function getBackgroundStyleNode()
  {
    return $this->backgroundStyleNode;
  }

  public function getStyleNode($path)
  {
    return parent::offsetGet($path);
  }

  public function getRelativePath()
  {
    return $this->getSpriteConfig()->get('relTmplOutputDirectory').'/'.$this->getFilename();
  }

  public function getFilename()
  {
    return $this->getHash().'.css';
  }

  public function getHash(){
    if(!$this->hash)
    {
      $this->hash = md5(serialize($this));
    }

    return $this->hash;
  }

  public function getCss()
  {
    $css = $this->getBackgroundStyleNode()->renderCss()."\n\n";
    foreach($this as $styleNode)
    {
      $css .= $styleNode->renderCss();
    }

    return $css;
  }

  protected function addStylesToGroup(SpriteImage $spriteImage)
  {
     parent::offsetSet($spriteImage->getKey(),
          new SpriteStyleNode($this, $spriteImage, $spriteImage->getCssClass(),
          $this->getBackgroundStyleNode(), $this->sprite->getRelativePath()));
  }

}
