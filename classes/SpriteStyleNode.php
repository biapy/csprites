<?php
/**
 * A Style Node for a image file integrated in the Sprite.
 */
class SpriteStyleNode
{
  protected $style;
  protected $class;
  protected $background_image;
  protected $background_position;
  protected $width;
  protected $height;
  protected $backgroundNode;


  /**
   * Instanciate a new SpriteStyleNode.
   * 
   * @param SpriteImage     $spriteImage      The SpriteImage associated to the node.
   * @param string          $class            [description]
   * @param SpriteStyleNode $backgroundNode   An optionnal custom background style node.
   * @param [type]          $background_image [description]
   */
  public function __construct(SpriteImage $spriteImage = null, $class, SpriteStyleNode $backgroundNode = null, $background_image = null)
  {
    if($spriteImage)
    {
      $this->background_position = array();
      $position = $spriteImage->getPosition();
      $this->background_position['left'] = -1 * $position->left;
      $this->background_position['top']  = -1 * $position->top;
      $this->width = $spriteImage->getWidth();
      $this->height = $spriteImage->getHeight();
    }
    $this->class = $class;
    $this->background_image = $background_image;
    $this->backgroundNode = $backgroundNode;
  }

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

    $transImage = SpriteConfig::get('transparentImagePath');

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
    return $this->background_image;
  }

  /**
   * Generate the background position CSS code..
   *
   * Available parameters are:
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

    if(isset($params['align'])){
      switch($params['align']){
        case 'left':{
          $left = 'left';
          $top = $this->background_position['top'].'px';
          break;
        }
        case 'right':{
          $left = 'right';
          $top = $this->background_position['top'].'px';
          break;
        }
        case 'bottom':{
          $left = $this->background_position['left'].'px';
          $top = 'bottom';
          break;
        }
        case 'top':{
          $left = $this->background_position['left'].'px';
          $top = 'top';
          break;
        }
      }
    }
    else{
      $left = ($this->background_position['left'] + $augmentX).'px';
      $top = ($this->background_position['top'] + $augmentY).'px';
    }
    if($this->background_position){
      return 'background-position: '.$left.' '.$top.' ; ';
    }
    return '';
  }

  public function renderBackground(array $params = array())
  {
    if($this->backgroundNode){
      return $this->backgroundNode->renderBackground($params);
    }
    else{
      $background = (isset($params['background']))?($params['background']):('no-repeat');
      return 'background: url(\'' . $this->background_image . '\') ' . $background . ' ; ';
    }
  }

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

  public function renderStyle(array $params=array())
  {

    if(!$this->backgroundNode)
    {
      return $this->renderBackground($params);
    }
    else
    {
      if(@$params['inline'])
      {
        return $this->renderBackground($params).$this->renderBackgroundPosition($params).$this->renderSize();
      }
      else
      {

        return $this->renderBackgroundPosition($params).$this->renderSize();
      }
    }
  }

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
  }

  public function renderCss(array $params=array())
  {
    return '.'.$this->class.' {'.$this->renderStyle($params).'} ';
  }

  /*public function renderCssWithBackground(array $params=array()){
    return '.'.$this->class.' {'.$this->renderStyleWithBackground($params).'} ';
  }
  public function renderCssOnlyBackground(array $params=array()){
    return '.'.$this->class.' {'.$this->renderBackground($params).'} ';
  }*/
}
