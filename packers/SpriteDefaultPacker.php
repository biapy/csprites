<?php
/**
 * The SpriteDefaultPacker class. Place the images in the generated sprite.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteDefaultPacker extends SpriteAbstractPacker
{

  /**
   * The SpriteImageRegistry parent object
   * @var SpriteImageRegistry
   */
  protected $spriteImageRegistry;

  /**
   * Instanciate a new SpriteDefaultPacker.
   *
   * @param  SpriteImageRegistry $spriteImageRegistry The parent object.
   * @access public
   * @return SpriteDefaultPacker This object.
   */
  public function __construct(SpriteImageRegistry &$spriteImageRegistry)
  {
    $this->spriteImageRegistry = $spriteImageRegistry;
  } // __construct()

  /**
   * Get this object parent SpriteImageRegistry.
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
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->spriteImageRegistry->getSpriteCache();
  } // getSpriteCache()

  public function pack(SpriteSprite &$sprite)
  {
    $root = new SpriteDefaultPackingNode($this->spriteImageRegistry);
    $root->setRectangle($this->getBoundingBox($sprite));

    foreach($sprite as &$spriteImage)
    {
      $root->insert($spriteImage);
    }

    return $root;
  } // pack()

  protected function getBoundingBox(SpriteSprite $sprite)
  {
    $bbSize = ($box = $this->getSpriteConfig()->get('boundingBoxSize'))?($box):(10000);

    if($sprite->getRepeatable())
    {
      if(strtolower($sprite->getRepeatable()) == 'x')
      {
        return new SpriteRectangle($this->spriteImageRegistry, 0,0,$sprite->getLongestWidth(), $bbSize);
      }
      else
      {
        return new SpriteRectangle($this->spriteImageRegistry, 0,0,$bbSize, $sprite->getLongestHeight());
      }
    }
    return ($sprite->getLongestWidth() > $sprite->getLongestHeight())?
      (new SpriteRectangle($this->spriteImageRegistry, 0,0,$sprite->getLongestWidth(), $bbSize)):
      (new SpriteRectangle($this->spriteImageRegistry, 0,0,$bbSize, $sprite->getLongestHeight()));
  } // getBoundingBox()
}
