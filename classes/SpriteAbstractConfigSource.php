<?php
/**
 * The SpriteAbstractConfigSource interface. Define method for an object that can provide a SpriteConfig.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
interface SpriteAbstractConfigSource
{
  /**
   * Get this object CSprite instance.
   *
   * @access  public
   * @return  CSprite A CSprite instance.
   */
  public function getCSprite();

  /**
   * Get this object CSprite config instance.
   *
   * @access  public
   * @return  CSpriteConfig A CSpriteConfig instance.
   */
  public function getSpriteConfig();

  /**
   * Get this object CSprite cache manager.
   * 
   * @access  public
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache();
}
