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
  protected $spriteName;
  protected $type;
  protected $spriteImages;
  protected $longestWidth;
  protected $longestHeight;
  protected $largestArea;
  protected $totalArea;
  protected $repeatable;
  protected $hash;

  /**
   * The SpriteConfig source object.
   * @var SpriteAbstractConfigSource
   */
  protected $spriteConfigSource;

  /**
   * Instanciate a new SpriteImageWriter.
   * 
   * @param SpriteAbstractConfigSource $spriteConfigSource A SpriteConfig source.
   * @param string                     $spriteName         A name.
   * @param string                     $type               A image type.
   * @access public
   * @return SpriteSprite This object.
   */
  public function __construct(SpriteAbstractConfigSource &$spriteConfigSource, $spriteName, $type)
  {
    $this->spriteConfigSource = $spriteConfigSource;
  	$this->hash = null;
    $this->spriteImages = array();
    $this->spriteName = $spriteName;
    $this->type = strtolower($type);
    parent::__construct($this->spriteImages, ArrayObject::ARRAY_AS_PROPS);
  }

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

  public function append($spriteImage)
  {
    if (!($spriteImage instanceof SpriteImage))
    {
      throw new SpriteException( 'You can only add SpriteImages to this Sprite' );
    }

    $this->offsetSet(null, $spriteImage);
    $this->updateMaximums($spriteImage);
  }

  public function offsetSet($index, $spriteImage)
  {
    if (!($spriteImage instanceof SpriteImage))
    {
      throw new Exception( 'You can only add SpriteImages to this Sprite' );
    }

    $index = ($index)?($index):($spriteImage->getKey());
    parent::offsetSet($index, $spriteImage);

    $sorterclass = $this->getSpriteConfig()->get('sorter');
    call_user_func($sorterclass.'::sort', &$this);

    $this->updateMaximums($spriteImage);
    $this->updateRepeatable($spriteImage);
  }

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

  public function getHash(){
    if(!$this->hash)
    {
      $this->hash = md5(serialize($this) . $this->getSpriteConfig()->getHash() . $this->getType());
    }

    return $this->hash;
  }

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

  public function getKey()
  {
    $spriteName = ($this->spriteName)?($this->spriteName.'-'):('');
    return $spriteName.$this->type;
  }

  public function prepareSprite()
  {
    foreach($this as $spriteImage)
    {
      $spriteImage->updateAlignment(array('longestWidth'=>$this->longestWidth,
            'longestHeight'=>$this->longestHeight,
            'totalArea'=>$this->totalArea));
    }
  }

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

