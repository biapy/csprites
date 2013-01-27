<?php
/**
 * The SpriteHashable interface. Define method for an object that can provide a hash.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
interface SpriteHashable
{
  /**
   * Compute this object hash.
   *
   * @access  public
   * @return  string A hash.
   */
  public function getHash();
}