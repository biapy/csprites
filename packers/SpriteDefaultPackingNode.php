<?php
/**
 * The SpriteDefaultPackingNode class. Compute the images position in the generated sprite.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteDefaultPackingNode extends SpriteAbstractPackingNode
{

  /**
   * The SpriteImageRegistry parent object
   * @var SpriteImageRegistry
   */
  protected $spriteImageRegistry;

  /**
   * Instanciate a new SpriteDefaultPackingNode.
   *
   * @param  SpriteImageRegistry $spriteImageRegistry The parent object.
   * @access public
   * @return SpriteDefaultPackingNode This object.
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

      $this->child[0] = new SpriteDefaultPackingNode($this->spriteImageRegistry);
      $this->child[1] = new SpriteDefaultPackingNode($this->spriteImageRegistry);

      $dw = $this->rect->width - $spriteImage->getWidth();
      $dh = $this->rect->height - $spriteImage->getHeight();

      if ($dw > $dh){
        $this->child[0]->rect = new SpriteRectangle($this->spriteImageRegistry, $this->rect->left, $this->rect->top, $this->rect->left + $spriteImage->getWidth(), $this->rect->bottom);
        $this->child[1]->rect = new SpriteRectangle($this->spriteImageRegistry, $this->rect->left + $spriteImage->getWidth(), $this->rect->top, $this->rect->right, $this->rect->bottom);
      }
      else{
        $this->child[0]->rect = new SpriteRectangle($this->spriteImageRegistry, $this->rect->left, $this->rect->top, $this->rect->right, $this->rect->top + $spriteImage->getHeight());
        $this->child[1]->rect = new SpriteRectangle($this->spriteImageRegistry, $this->rect->left, $this->rect->top+$spriteImage->getHeight(), $this->rect->right, $this->rect->bottom);
      }
      //$this->child[0]->setSpriteImage($spriteImage);
      $this->getSpriteConfig()->debug('Inserting Node into rectangle :'.$this->child[0]->rect);
      $newNode = $this->child[0]->insert($spriteImage);
      return $newNode;
    }

  } // insert()

}
