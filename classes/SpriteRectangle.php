<?php
/**
 * The SpriteRectangle class. Describe a rectangle area.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteRectangle
{

  /**
   * The rectangle width.
   * @var integer
   */
  public $width;

  /**
   * The rectangle height.
   * @var integer
   */
  public $height;

  /**
   * The rectangle left position.
   * @var integer
   */
  public $left;

  /**
   * The rectangle top position.
   * @var integer
   */
  public $top;

  /**
   * The rectangle right position.
   * @var integer
   */
  public $right;

  /**
   * The rectangle bottom position.
   * @var integer
   */
  public $bottom;

  /**
   * The SpriteImageRegistry parent object.
   * @var SpriteImageRegistry
   */
  protected $spriteImageRegistry;

  /**
   * Instanciate a new SpriteRectangle.
   *
   * @param  SpriteImageRegistry $spriteImageRegistry  The parent object.
   * @param  integer $left The rectangle left position.
   * @param  integer $top The rectangle top position.
   * @param  integer $right The rectangle right position.
   * @param  integer $bottom The rectangle bottom position.
   * @access public
   * @return SpriteRectangle the new rectangle.
   */
  public function __construct(SpriteImageRegistry $spriteImageRegistry, $left, $top, $right, $bottom)
  {
    $this->spriteImageRegistry = $spriteImageRegistry;

    $this->left = $left;
    $this->top = $top;
    $this->right = $right;
    $this->bottom = $bottom;
    $this->width = $this->right - $this->left;
    $this->height = $this->bottom - $this->top;
  } // __construct()

  /**
   * Get this object parent SpriteImageRegistry.
   * @access  public
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
   * @access  public
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->spriteImageRegistry->getSpriteCache();
  } // getSpriteCache()

  /**
   * Test if given image fit in this rectangle.
   * @param  SpriteImage $spriteImage A image.
   * @access  public
   * @return boolean     True if the image fit in this rectangle.
   */
  public function willFit(SpriteImage $spriteImage)
  {
    $this->getSpriteConfig()->debug("willFit :".$spriteImage->getWidth()." <= ".$this->width.") && (".$spriteImage->getHeight()." <= ".$this->height."))");
    return (($spriteImage->getWidth() <= $this->width) && ($spriteImage->getHeight() <= $this->height));
  } // willFit()

  /**
   * Test if given image fit perfectly in this rectangle.
   * @param  SpriteImage $spriteImage A image.
   * @access  public
   * @return boolean     True if the image fit perfectly in this rectangle.
   */
  public function willFitPerfectly(SpriteImage $spriteImage)
  {
    $this->getSpriteConfig()->debug('Perfect Fit: '.$spriteImage->getWidth().' '.$spriteImage->getHeight());
    return (($spriteImage->getWidth() == $this->width) && ($spriteImage->getHeight() == $this->height));
  } // willFitPerfectly()

  /**
   * Augment the rectangle size by given values.
   * 
   * @param  integer $x The width addition (default 100).
   * @param  integer $y The height addition (default 100).
   * @access public 
   * @return SpriteRectangle This object.
   */
  public function grow($x=100, $y=100)
  {
    $this->right += $x;
    $this->bottom += $y;
    $this->getSpriteConfig()->debug('Growing : '.$this->right.' '.$this->bottom);
    $this->width = $this->right - $this->left;
    $this->height = $this->bottom - $this->top;

    return $this;
  } // grow()

  /**
   * Compute this object string representation.
   * 
   * @access public 
   * @return string This object string representation.
   */
  public function __toString()
  {
    return 'l:'.$this->left.' t:'.$this->top.' r:'.$this->right.' b:'.$this->bottom;
  } // __toString()

  /**
   * Update this rectangle dimensions.
   * @param  integer $left The rectangle left position.
   * @param  integer $top The rectangle top position.
   * @param  integer $right The rectangle right position.
   * @param  integer $bottom The rectangle bottom position.
   * @access public
   * @return SpriteRectangle This object.
   */
  public function update($left, $top, $right, $bottom)
  {
    $this->left = $left;
    $this->top = $top;
    $this->right = $right;
    $this->bottom = $bottom;
    $this->width = $this->right - $this->left;
    $this->height = $this->bottom - $this->top;

    return $this;
  } // update()
}
