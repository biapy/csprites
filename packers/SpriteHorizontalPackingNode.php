<?php
/**
 * The SpriteHorizontalPackingNode class. Compute the images position in the generated sprite with a horizontal pattern.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteHorizontalPackingNode extends SpriteDefaultPackingNode
{

  /**
   * Add an image to the sprite resulting image.
   * 
   * @param   SpriteImage $spriteImage  The added image.
   * @access  public
   * @return  SpriteDefaultPackingNode  The node containing the image.
   */
  public function insert(SpriteImage &$spriteImage)
  {

    if(!$this->isLeaf())
    {
      //$this->getSpriteConfig()->debug('Not a leaf');
      $newNode = $this->child[0]->insert($spriteImage);
      if($newNode != NULL)
      {
        return $newNode;
      }
      //$this->getSpriteConfig()->debug('Not a Leaf: Inserting Node into rectangle :'.$this->child[1]->rect);
      return $this->child[1]->insert($spriteImage);
    }
    else
    {
      if($this->spriteImage != NULL)
      {
       // $this->getSpriteConfig()->debug('Node found');
        return NULL;
      }

      if(!($this->rect->willFit($spriteImage)))
      {
        //$this->getSpriteConfig()->debug('Will not fit');
        return NULL;
      }
      if($this->rect->willFitPerfectly($spriteImage))
      {
        $this->getSpriteConfig()->debug('Fits perfectly'.$spriteImage);
        $spriteImage->setPosition($this->rect);
        $this->setSpriteImage($spriteImage);
        //$spriteImage->display($this->rect);
        return $this;
      }

      $this->getSpriteConfig()->debug('Adding children');

      $this->child[0] = new SpriteHorizontalPackingNode($this->spriteImageRegistry);
      $this->child[1] = new SpriteHorizontalPackingNode($this->spriteImageRegistry);

      $dw = $this->rect->width - $spriteImage->getWidth();
      $dh = $this->rect->height - $spriteImage->getHeight();

      $this->child[0]->rect = new SpriteRectangle($this->spriteImageRegistry, $this->rect->left, $this->rect->top, $this->rect->left + $spriteImage->getWidth(), $this->rect->bottom);
      $this->child[1]->rect = new SpriteRectangle($this->spriteImageRegistry, $this->rect->left + $spriteImage->getWidth(), $this->rect->top, $this->rect->right, $this->rect->bottom);

      //$this->child[0]->setSpriteImage($spriteImage);
      $this->getSpriteConfig()->debug('Inserting Node into rectangle :'.$this->child[0]->rect);
      $newNode = $this->child[0]->insert($spriteImage);
      return $newNode;
    }

  } // insert()

}
