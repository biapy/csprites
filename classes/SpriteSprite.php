<?php
/**
 * The SpriteSprite class. Define a sprite image.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteSprite extends ArrayObject implements SpriteHashable, SpriteAbstractConfigSource
{
  /**
   * This object name.
   * @var string
   * @access protected
   */
  protected $spriteName;

  /**
   * The image type.
   * @var string
   * @access protected
   */
  protected $type;

  /**
   * The X background offset on hover.
   * @var integer
   * @access  protected
   */
  protected $hoverXOffset;

  /**
   * The Y background offset on hover.
   * @var integer
   * @access  protected
   */
  protected $hoverYOffset;

  /**
   * The SpriteImages added to the sprite.
   * @var array
   * @access protected
   */
  protected $spriteImages;

  /**
   * The longest SpriteImage width of the sprite.
   * @var integer
   * @access protected
   */
  protected $longestWidth;

  /**
   * The longest SpriteImage height of the sprite.
   * @var integer
   * @access protected
   */
  protected $longestHeight;

  /**
   * The largest image area of the sprite.
   * @var integer
   * @access protected
   */
  protected $largestArea;

  /**
   * The total image area of the sprite.
   * @var integer
   * @access  protected
   */
  protected $totalArea;

  /**
   * The repeat direction of the sprite (x or y or null)
   * @var string
   * @access  protected
   */
  protected $repeatable;

  /**
   * The hash of this object.
   * @var string
   */
  protected $hash;

  /**
   * The SpriteConfig source object.
   * @var SpriteAbstractConfigSource
   * @access  protected
   */
  protected $spriteConfigSource;

  /**
   * The SpriteStyleGroup associated to this object.
   * @var SpriteStyleGroup
   * @access  protected
   */
  protected $spriteStyleGroup;

  /**
   * Instanciate a new SpriteSprite.
   *
   * Valid params are:
   *  * name : the sprite name.
   *  * imageType : the image type.
   *  * hoverXOffset : Offset to the background X position on hover
   *  * hoverYOffset : Offset to the background Y position on hover
   *
   * @param SpriteAbstractConfigSource $spriteConfigSource A SpriteConfig source.
   * @param array                      $params             An array of parameters.
   * @access public
   * @return SpriteSprite This object.
   */
  public function __construct(SpriteAbstractConfigSource &$spriteConfigSource, $params)
  {
    $this->spriteConfigSource = $spriteConfigSource;
  	$this->hash = null;
    $this->spriteImages = array();
    $this->spriteName = isset($params['name']) ? $params['name'] : null;
    $this->type = strtolower(isset($params['imageType']) ? $params['imageType'] : null);

    $this->hoverXOffset = isset($params['hoverXOffset']) ? $params['hoverXOffset'] : 0;
    $this->hoverYOffset = isset($params['hoverYOffset']) ? $params['hoverYOffset'] : 0;

    parent::__construct($this->spriteImages, ArrayObject::ARRAY_AS_PROPS);

    //Update all the sprite styles to the registry
    $this->getCSprite()->getStyleRegistry()->addSprite($this);
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

  public function getSpriteStyleGroup()
  {
    return $this->spriteStyleGroup;
  } // getSpriteStyleGroup()

  public function setSpriteStyleGroup(SpriteStyleGroup $spriteStyleGroup)
  {
    if(! $this == $spriteStyleGroup->getSprite())
    {
      throw new SpriteException('Error: SpriteStyleGroup Sprite must match this object.', 302);
    }

    $this->spriteStyleGroup = $spriteStyleGroup;
  } // setSpriteStyleGroup()

  /**
   * Handle the addition of a SpriteImage to this SpriteSprite.
   * Override the default append ArrayObject method.
   *
   * @param  SpriteImage $spriteImage A SpriteImage
   * @access public
   */
  public function append($spriteImage)
  {
    if (!($spriteImage instanceof SpriteImage))
    {
      throw new SpriteException( 'You can only add SpriteImages to this Sprite', 301);
    }

    $this->offsetSet(null, $spriteImage);
    $this->updateMaximums($spriteImage);
  } // append()

  /**
   * Handle the update of a SpriteImage in this SpriteSprite.
   * Override the default offsetSet ArrayObject method.
   *
   * @param  mixed $index             A optionnal index value.
   * @param  SpriteImage $spriteImage A SpriteImage
   * @access  public
   */
  public function offsetSet($index, $spriteImage)
  {
    if (!($spriteImage instanceof SpriteImage))
    {
      throw new Exception( 'You can only add SpriteImages to this Sprite', 301);
    }

    $index = ($index)?($index):($spriteImage->getKey());
    parent::offsetSet($index, $spriteImage);

    $sorterclass = $this->getSpriteConfig()->get('sorter');
    call_user_func($sorterclass.'::sort', &$this);

    $this->updateMaximums($spriteImage);
    $this->updateRepeatable($spriteImage);

    $this->spriteStyleGroup->addStylesToGroup($spriteImage);
  } // offsetSet()

  public function getType()
  {
    return $this->type;
  }

  public function getName()
  {
    return $this->spriteName;
  }

  public function getRepeatable()
  {
    return $this->repeatable;
  }

  public function add(SpriteImage $spriteImage)
  {
    $this[] = $spriteImage;
    //  $sorterclass::addImage($this, $spriteImage);
    // $this->spriteImages[$spriteImage->getKey()] = $spriteImage;
  }

  public function getLongestWidth()
  {
    return $this->longestWidth;
  }

  public function getLongestHeight()
  {
    return $this->longestHeight;
  }

  public function getLongestDimension()
  {
  	return ($this->longestWidth > $this->longestHeight)?($this->longestWidth):($this->longestHeight);
  }

  public function getTotalArea()
  {
    return $this->totalArea;
  }

  /**
   * Compute this object hash.
   *
   * @access  public
   * @return  string A hash.
   */
  public function getHash()
  {
    if(!$this->hash)
    {
      $this->hash = md5(serialize($this) . $this->getSpriteConfig()->getHash() . $this->getType());
    }

    return $this->hash;
  } // getHash()

  public function getImageExtension()
  {
    return ($this->getType() == SpriteConfig::PNG8)?(SpriteConfig::PNG):($this->getType());
  }

  public function getFilename()
  {
    return $this->getHash().'.'.$this->getImageExtension();
  }

  public function getRelativePath()
  {
    return $this->getSpriteConfig()->get('relImageOutputDirectory').'/'.$this->getFilename();
  }

  /**
   * Compute a SpriteSprite key.
   * 
   * @param  string $name A SpriteSprite name.
   * @param  string $type A SpriteSprite type.
   * @access  public
   * @static
   * @return string       A SpriteSprite key.
   */
  public static function computeKey($name, $type)
  {
    return ($name) ? $name . '-' . $type : $type;
  } // computeKey()

  /**
   * Get this SpriteSprite key in SpriteImageRegistry.
   *
   * @access  public
   * @return string A SpriteSprite key.
   */
  public function getKey()
  {
    return self::computeKey($this->spriteName, $this->type);
  } // getKey()

  /**
   * Update all sprites images SpriteSprite data.
   *
   * @access  public
   * @return SpriteSprite This object.
   */
  public function prepareSprite()
  {
    foreach($this as $spriteImage)
    {
      $spriteImage->updateAlignment(array('longestWidth'=>$this->longestWidth,
            'longestHeight'=>$this->longestHeight,
            'totalArea'=>$this->totalArea));
    }

    return $this;
  } // prepareSprite()

  protected function updateMaximums($spriteImage)
  {
    $this->longestWidth   = ($spriteImage->getWidth() > $this->longestWidth)?($spriteImage->getWidth()):($this->longestWidth);
    $this->longestHeight  = ($spriteImage->getHeight() > $this->longestHeight)?($spriteImage->getHeight()):($this->longestHeight);
    $this->largestArea    = ($spriteImage->getArea() > $this->largestArea)?($spriteImage->getArea()):($this->largestArea);
    $this->totalArea     += $spriteImage->getArea();
  }

  protected function updateRepeatable($spriteImage)
  {
    $params = $spriteImage->getParams();
    if(isset($params['repeat']))
    {
      $this->repeatable = $params['repeat'];
    }
  }
}

