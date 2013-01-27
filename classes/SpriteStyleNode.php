<?php
/**
 * The SpriteStyleNode class. A Style Node for a image file integrated in the Sprite.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteStyleNode implements SpriteAbstractConfigSource
{
  protected $style;
  protected $class;
  protected $backgroundImage;
  protected $background_position;
  protected $width;
  protected $height;
  protected $backgroundNode;

  /**
   * The X background offset on hover.
   * @var integer
   * @access  protected
   */
  protected $hoverXOffset;

  /**
   * The Y background offset on hover.
   * @var integer
   * @access protected
   */
  protected $hoverYOffset;

  /**
   * The SpriteConfig source object.
   * @var SpriteAbstractConfigSource
   * @access protected
   */
  protected $spriteConfigSource;

  /**
   * The source SpriteImage of this object.
   * @var SpriteImage
   * @access protected
   */
  protected $spriteImage;

  /**
   * Instanciate a new SpriteStyleNode.
   *
   * @param SpriteAbstractConfigSource $spriteConfigSource  A SpriteConfig source.
   * @param SpriteImage     $spriteImage      The SpriteImage associated to the node.
   * @param string          $class            [description]
   * @param SpriteStyleNode $backgroundNode   An optionnal custom background style node.
   * @param [type]          $backgroundImage [description]
   */
  public function __construct(SpriteAbstractConfigSource $spriteConfigSource, SpriteImage $spriteImage = null, $class, SpriteStyleNode $backgroundNode = null, $backgroundImage = null)
  {
    $this->spriteConfigSource = $spriteConfigSource;

    if($spriteImage)
    {
      $this->spriteImage = $spriteImage;
      $this->background_position = array();
      $this->width = $spriteImage->getWidth();
      $this->height = $spriteImage->getHeight();

      $this->hoverXOffset = $spriteImage->getHoverXOffset();
      $this->hoverYOffset = $spriteImage->getHoverYOffset();
    }

    $this->class = $class;
    $this->backgroundImage = $backgroundImage;
    $this->backgroundNode = $backgroundNode;
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

  /**
   * Generate the image tag for the style node.
   *
   * Available parameters are:
   *  - inline: true to use style attribute instead of class.
   *  - background: allow to choose a different background-repeat value (default to no-repeat).
   *  - augmentX: add a offset to background X position.
   *  - augmentY: add a offset to background Y position.
   *
   * The additionnal attributes are defined like this:
   *   array('class' => 'additionnal-class', 'id' => 'item-id');
   * 
   * @param  array  $params       A array of options.
   * @param  array  $attributes   A array of HTML attributes to add to the tag.
   * @return string               A HTML img tag.
   */
  public function image_tag($params = array(), $attributes = array())
  {
    if(!is_array($params))
    {
      $params = array();
    }

    if(!is_array($attributes))
    {
      $attributes = array();
    }

    $transImage = $this->getSpriteConfig()->get('transparentImagePath');

    if(!isset($attributes['src']))
    {
      $attributes['src'] = $transImage;
    }

    if(isset($params['inline']) && $params['inline'])
    {
      if(isset($attributes['style']))
      {
        $attributes['style'] .= '; ' . $this->renderStyleWithBackground($params);
      }
      else
      {
        $attributes['style'] = $this->renderStyleWithBackground($params);
      }
    }
    else
    {
      if(isset($attributes['class']))
      {
        $attributes['class'] .= ' ' . $this->renderClass();
      }
      else
      {
        $attributes['class'] = $this->renderClass();
      }
    }

    $tag = '<img';
    foreach($attributes as $key => $value)
    {
      $tag .= sprintf(' %s="%s"', $key, $value);
    }
    $tag .= ' />';

    return $tag;
  }

  public function getBackgroundImage()
  {
    return $this->backgroundImage;
  }

  /**
   * Get the hover X background offset of this image.
   *
   * @access public
   * @return integer a background offset in pixels on the X axis.
   */
  public function getHoverXOffset()
  {
    return $this->hoverXOffset;
  } // getHoverXOffset()

  /**
   * Get the hover Y background offset of this image.
   *
   * @access public
   * @return integer a background offset in pixels on the Y axis.
   */
  public function getHoverYOffset()
  {
    return $this->hoverYOffset;
  } // getHoverYOffset()

  /**
   * Check if image has an hover offset on X or Y axis.
   *
   * @access public
   * @return boolean True if image has a hover offset.
   */
  public function hasHoverOffset()
  {
    return ($this->hoverXOffset != 0) || ($this->hoverYOffset != 0);
  } // hasHoverOffset()

  /**
   * Get the background image position from the source sprite Image.
   *
   * @access  protected
   * @return array An array of left and top background position in pixels.
   */
  protected function getBackgroundPosition()
  {
    if($this->spriteImage)
    {
      $position = $this->spriteImage->getPosition();
      $this->background_position['left'] = -1 * $position->left;
      $this->background_position['top']  = -1 * $position->top;
    }

    return $this->background_position;
  } // getBackgroundPosition()

  /**
   * Compute the inline CSS background-position rule of this node.
   *
   * Available parameters are:
   *  - align: image alignment (top, right, bottom, left).
   *  - augmentX: add a offset to background X position.
   *  - augmentY: add a offset to background Y position.
   *
   * @param  array  $params A array of options.
   * @return string         A CSS value.
   */
  public function renderBackgroundPosition(array $params = array())
  {
    $augmentX = (isset($params['augmentX']))?($params['augmentX']):(0);
    $augmentY = (isset($params['augmentY']))?($params['augmentY']):(0);

    $background_position = $this->getBackgroundPosition();

    if(isset($params['align']))
    {
      switch($params['align'])
      {
        case 'left':
          $left = 'left';
          $top = $background_position['top'].'px';
          break;

        case 'right':
          $left = 'right';
          $top = $background_position['top'].'px';
          break;

        case 'bottom':
          $left = $background_position['left'].'px';
          $top = 'bottom';
          break;

        case 'top':
          $left = $background_position['left'].'px';
          $top = 'top';
          break;
      }
    }
    else
    {
      $left = ($background_position['left'] + $augmentX).'px';
      $top = ($background_position['top'] + $augmentY).'px';
    }
    if($background_position)
    {
      return 'background-position: '.$left.' '.$top.' ; ';
    }
    return '';
  } // renderBackgroundPosition()

  /**
   * Compute the inline CSS background-image rule of this node.
   *
   * Accepted parameters are:
   *  - background : forced background-repeat value.
   *
   * @param  array  $params An array of parameters.
   * @access public
   * @return string A CSS inline style.
   */
  public function renderBackground(array $params = array())
  {
    if($this->backgroundNode){
      return $this->backgroundNode->renderBackground($params);
    }
    else{
      $background = (isset($params['background']))?($params['background']):('no-repeat');
      return 'background: url(\'' . $this->backgroundImage . '\') ' . $background . ' ; ';
    }
  } // renderBackground()

  public function renderWidth()
  {
    if($this->width)
    {
      return 'width: '. $this->width . 'px; ';
    }
    return '';
  }

  public function renderHeight()
  {
    if($this->height){
      return 'height: ' . $this->height . 'px; ';
    }
    return '';
  }

  public function renderSize()
  {
    return $this->renderWidth() . ' ' . $this->renderHeight();
  }

  public function renderImageClass()
  {
    return $this->class . ' ';
  }

  public function renderBgClass()
  {
    if($this->backgroundNode)
    {
      return $this->backgroundNode->renderImageClass();
    }
    return '';
  }

  public function renderClass()
  {
    return $this->renderBgClass() . $this->renderImageClass();
  }

  /**
   * Compute the inline CSS rules of this node.
   *
   * Accepted parameters are:
   *  - inline : true to enable inline display (add background image).
   *  - background : forced background-repeat value.
   *  - align: image alignment (top, right, bottom, left).
   *  - augmentX: add a offset to background X position.
   *  - augmentY: add a offset to background Y position.
   *
   * @param  array  $params An array of parameters.
   * @access public
   * @return string A CSS inline style.
   */
  public function renderStyle(array $params=array())
  {

    if(!$this->backgroundNode)
    {
      return $this->renderBackground($params);
    }
    else
    {
      if(isset($params['inline']) && $params['inline'])
      {
        return $this->renderStyleWithBackground($params);
      }
      else
      {

        return $this->renderBackgroundPosition($params) . $this->renderSize();
      }
    }
  } // renderStyle()

  /**
   * Compute the CSS inline CSS rules with background image.
   *
   * Accepted parameters are:
   *  - inline : true to enable inline display.
   *  - background : forced background-repeat value.
   *  - align: image alignment (top, right, bottom, left).
   *  - augmentX: add a offset to background X position.
   *  - augmentY: add a offset to background Y position.
   *
   * @param  array  $params An array of parameters.
   * @access public
   * @return string A CSS inline style.
   */
  public function renderStyleWithBackground(array $params=array())
  {
    if(isset($params['inline']) && $params['inline'])
    {
      return $this->renderBackground($params) . $this->renderBackgroundPosition($params) . $this->renderSize();
    }
    else
    {
      //return $this->renderCssWithBackground($params);
    }
  } // renderStyleWithBackground()

  /**
   * Compute the CSS rules for this node.
   *
   * Accepted parameters are:
   *  - inline : true to enable inline display.
   *  - background : forced background-repeat value.
   *  - align: image alignment (top, right, bottom, left).
   *  - augmentX: add a offset to background X position.
   *  - augmentY: add a offset to background Y position.
   *
   * @param  array  $params An array of parameters.
   * @access public
   * @return string A CSS class ruleset.
   */
  public function renderCss(array $params=array())
  {
    $css = sprintf('.%s {%s} ', $this->class, $this->renderStyle($params));

    if($this->hasHoverOffset())
    {
      $hover_params = $params;
      if(isset($params['augmentX']))
      {
        $hover_params['augmentX'] += $this->getHoverXOffset();
      }
      else
      {
        $hover_params['augmentX'] = $this->getHoverXOffset();
      }

      if(isset($params['augmentY']))
      {
        $hover_params['augmentY'] += $this->getHoverYOffset();
      }
      else
      {
        $hover_params['augmentY'] = $this->getHoverYOffset();
      }

      $css .= sprintf('.%s:hover {%s}', $this->class, $this->renderBackgroundPosition($hover_params));
    }

    return $css;
  } // renderCss()

  /*public function renderCssWithBackground(array $params=array()){
    return '.'.$this->class.' {'.$this->renderStyleWithBackground($params).'} ';
  }
  public function renderCssOnlyBackground(array $params=array()){
    return '.'.$this->class.' {'.$this->renderBackground($params).'} ';
  }*/
}
