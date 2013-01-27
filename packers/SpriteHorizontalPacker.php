<?php
/**
 * The SpriteHorizontalPacker class. Place the images in the generated sprite on a horizontal pattern.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteHorizontalPacker extends SpriteDefaultPacker
{

  /**
   * Generate the image of the given sprite.
   * 
   * @param   SpriteSprite $sprite [description]
   * @access  public
   * @return  SpriteHorizontalPackingNode The packing node.
   */
  public function pack(SpriteSprite &$sprite)
  {
    $root = new SpriteHorizontalPackingNode($this->spriteImageRegistry);
    $root->setRectangle($this->getBoundingBox($sprite));

    foreach($sprite as &$spriteImage)
    {
      $root->insert($spriteImage);
    }

    return $root;
  } // pack()

  /**
   * Compute the sprite bounding box (for horizontal placement).
   * 
   * @param   SpriteSprite $sprite [description]
   * @access  public
   * @return  SpriteRectangle A bounding box.
   */
  protected function getBoundingBox(SpriteSprite $sprite)
  {
    $box = $this->getSpriteConfig()->get('boundingBoxSize');
    $bbSize = ($box)?$box:10000;

    return new SpriteRectangle($this->spriteImageRegistry, 0,0,$bbSize, $sprite->getLongestHeight());
  } // getBoundingBox()
}
