<?php
/**
 * The SpriteImage class. Describe a image to be integrated in sprite.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteImage implements SpriteIterable, SpriteHashable, SpriteAbstractConfigSource
{

  /**
   * Image absolute path.
   * @var string
   */
  protected $imgPath;

  /**
   * Image relative path.
   * @var string
   */
  protected $relativePath;

  /**
   * Image type.
   * @var string
   */
  protected $imgType;

  /**
   * Image file extension.
   * @var string
   */
  protected $imgExtension;

  /**
   * A associative array containing image sizes.
   * @var array
   */
  protected $sizeArray;

  /**
   * The area used by the image on the sprite.
   * @var integer
   */
  protected $area;

  /**
   * The image file size.
   * @var integer
   */
  protected $fileSize;

  /**
   * A associative array containing image informations.
   * @var array
   */
  protected $imageInfo;

  /**
   * The image position in the sprite.
   * @var SpriteRectangle
   */
  protected $position;

  /**
   * The image margins in the sprite.
   * @var SpriteRectangle
   */
  protected $margin;

  /**
   * A associative array of parameters.
   * @var array
   */
  protected $params;

  /**
   * This object hash.
   * @var string
   */
  protected $hash;

  /**
   * The SpriteImageRegistry parent object.
   * @var SpriteImageRegistry
   */
  protected $spriteImageRegistry;

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
   * Instanciate a new SpriteImage.
   *
   * Accepted params are:
   *  - name : the sprite name.
   *  - imageType : the image type.
   *  - sprite-margin : margins of the image in the sprite.
   *  - hoverXOffset : Offset to the background X position on hover
   *  - hoverYOffset : Offset to the background Y position on hover
   *
   * @param  SpriteImageRegistry $spriteImageRegistry  The parent object.
   * @param  string              $path                 The image absolute path.
   * @param  array               $params               A associative parameters array.
   * @access public
   * @return SpriteImage the new image.
   */
  public function __construct(SpriteImageRegistry $spriteImageRegistry, $path, array $params = array())
  {
    $this->hoverXOffset = isset($params['hoverXOffset']) ? $params['hoverXOffset'] : 0;
    $this->hoverYOffset = isset($params['hoverYOffset']) ? $params['hoverYOffset'] : 0;

    $this->spriteImageRegistry = $spriteImageRegistry;
    $this->hash = null;

    $this->imgPath = $path;
    $this->relativePath = CSpriteTools::relativePath($this->getSpriteConfig()->get('rootDir'), $path);;

    if(!($this->fileSize = filesize($this->imgPath)))
    {
      $this->getSpriteConfig()->debug("file existence problem");
      throw new SpriteException($this->imgPath.' : File does not exist or is size 0', 202);
    }

    if(!($this->sizeArray = getimagesize($this->imgPath, $this->imageinfo)))
    {
      $this->getSpriteConfig()->debug($this->imgPath."image size read problem");
      throw new SpriteException($this->imgPath.' : Image size could not be read', 203);
    }

    if(isset($this->sizeArray['bits']) && isset($this->sizeArray['mime']))
    {
      $this->getSpriteConfig()->debug('bits: '.$this->sizeArray['bits'].' channels:'.@$this->sizeArray['channels'].' mime:'.$this->sizeArray['mime']);
    }

    else
    {
      $this->getSpriteConfig()->debug('bits: unknown channels:'.@$this->sizeArray['channels'].' mime: unknown');
    }

    $this->processType();
    if(!$this->sizeArray)
    {
      $this->getSpriteConfig()->debug('Image size misread');
      throw new SpriteException($this->imgPath.' : Image size could not be read', 204);
    }

    $this->setMargins($params);
    $this->params = $params;
  } // __construct()

  /**
   * Get this object parent SpriteImageRegistry.
   * 
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
   * 
   * @access  public
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->spriteImageRegistry->getSpriteCache();
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

  public function getPath()
  {
    return $this->imgPath;
  }

  public function getRelativePath()
  {
    return $this->relativePath;
  }

  public function getType()
  {
    return $this->imgType;
  }

  public function getWidth()
  {
    return $this->sizeArray[0] + $this->margin->left + $this->margin->right;
  }

  public function getOriginalWidth()
  {
    return $this->sizeArray[0];
  }

  public function getHeight()
  {
    return $this->sizeArray[1] + $this->margin->top + $this->margin->bottom;
  }

  public function getOriginalHeight()
  {
    return $this->sizeArray[1];
  }

  public function getExtension()
  {
    return $this->imgExtension;
  }

  public function getArea()
  {
    return ($this->sizeArray[0] + $this->margin->left + $this->margin->right) * ($this->sizeArray[1] + $this->margin->top + $this->margin->bottom);
  }

  public function getOriginalArea()
  {
    return $this->getWidth() * $this->getHeight();
  }

  public function getSizeArray()
  {
    return $this->sizeArray;
  }

  public function getFileSize()
  {
    return $this->fileSize;
  }

  public function getImageInfo()
  {
    return $this->imageInfo;
  }

  public function getColorDepth()
  {
    return $this->sizeArray['bits'];
  }

  public function getMimeType()
  {
    return $this->sizeArray['mime'];
  }

  public function getPosition()
  {
    return $this->position;
  }

  public function getMargin()
  {
    return $this->margin;
  }

  public function getParams()
  {
    return $this->params;
  }

  public function setPosition(SpriteRectangle $rect)
  {
    $this->position = $rect;
  }

  public function isTall()
  {
    return ($this->getHeight() > $this->getWidth());
  }

  public function isWide()
  {
    return ($this->getWidth() > $this->getHeight());
  }

  public function isSquare()
  {
    return ($this->getWidth() == $this->getHeight());
  }

  public function getLongestDimension()
  {
    return ($this->isTall())?($this->getHeight()):($this->getWidth());
  }

  public function getKey()
  {
    return $this->getRelativePath();
  }

  public function __toString()
  {
    $output = ''."\n";
    $output .= '<li>Path :'.$this->getPath().'</li>'."\n";
    $output .= '<li>Type :'.$this->getType().'</li>'."\n";
    $output .= '<li>Extension :'.$this->getExtension().'</li>'."\n";
    $output .= '<li>FileSize :'.$this->getFileSize().'</li>'."\n";
    $output .= '<li>Dimension :'.$this->getWidth().'x'.$this->getHeight().'</li>'."\n";
    $output .= ''."\n";
    return $output;
  }

  /**
   * Compute this object hash, based on the relative path.
   *
   * @access  public
   * @return  string A MD5 hash.
   */
  public function getHash(){
    if(!$this->hash)
    {
      $this->hash = md5($this->getRelativePath());
    }

    return $this->hash;
  } // getHasht();

  /**
   * Compute the CSS class name for this image.
   *
   * @access  public
   * @return  string A CSS class name.
   */
  public function getCssClass()
  {
    return 'csprite-' . $this->getHash();
  } // getCssClass()

  public function updateAlignment(array $spriteParams = array())
  {
    if(isset($spriteParams['longestWidth']) && isset($spriteParams['longestHeight']))
    {
      if(isset($this->params['sprite-align']))
      {
        switch($this->params['sprite-align'])
        {
          case 'left':
            $rightMargin = $spriteParams['longestWidth'] - ($this->margin->left + $this->sizeArray[0]);
            $this->margin = new SpriteRectangle($this->spriteImageRegistry, $this->margin->left, $this->margin->top, $rightMargin, $this->margin->bottom);
            $this->position = new SpriteRectangle($this->spriteImageRegistry, 0,0, $spriteParams['longestWidth'], $this->position->bottom);
            break;

          case 'right':
            $leftMargin = $spriteParams['longestWidth'] - ($this->margin->right + $this->sizeArray[0]);
            $this->margin = new SpriteRectangle($this->spriteImageRegistry, $leftMargin, $this->margin->top, $this->margin->right, $this->margin->bottom);
            $this->position = new SpriteRectangle($this->spriteImageRegistry, 0,0, $spriteParams['longestWidth'], $this->position->bottom);
            break;

          case 'top':
            $bottomMargin = $spriteParams['longestHeight'] - ($this->margin->top + $this->sizeArray[1]);
            $this->margin = new SpriteRectangle($this->spriteImageRegistry, $this->margin->left, $this->margin->top, $this->margin->right, $bottomMargin);
            $this->position = new SpriteRectangle($this->spriteImageRegistry, 0,0, $this->position->right, $spriteParams['longestHeight']);
            break;

          case 'bottom':
            $topMargin = $spriteParams['longestHeight'] - ($this->margin->bottom + $this->sizeArray[1]);
            $this->margin = new SpriteRectangle($this->spriteImageRegistry, $this->margin->left, $topMargin, $this->margin->right, $this->margin->bottom);
            $this->position = new SpriteRectangle($this->spriteImageRegistry, 0,0, $this->position->right, $spriteParams['longestHeight']);
            break;
        }//end switch
      }
    }
  } // updateAlignment()

  protected function processType()
  {
    $this->imgExtension = strtolower(MimeTypeTool::getExtensionByMimeType(MimeTypeTool::detectMimeType($this->getPath())));

    if($this->getExtension() == 'png')
    {
      //$this->imgType = $this->getExtension().'-'.$this->getColorDepth();
      $this->imgType = $this->getExtension();
    }
    else
    {
      $this->imgType = $this->getExtension();
    }
    if(!in_array(strtolower($this->getExtension()), $this->getSpriteConfig()->get('acceptedTypes')))
    {
      $this->getSpriteConfig()->debug('Extension Type Mismatch: '.$this->getExtension());
      throw new SpriteException($this->getPath() . ' : "' . $this->getExtension().'" is not an acceptable image type.', 201);
    }
  } // processType()

  protected function setMargins(array $params = array())
  {
    //First Handle Margins
    if(isset($params['sprite-margin']))
    {
      if(is_array($params['sprite-margin']))
      {
        $this->margin = new SpriteRectangle($this->spriteImageRegistry, $params['sprite-margin'][3], $params['sprite-margin'][0], $params['sprite-margin'][1], $params['sprite-margin'][2]);
        $this->position = new SpriteRectangle($this->spriteImageRegistry, 0, 0, $this->sizeArray[0] + $this->margin->left + $this->margin->right, $this->sizeArray[1] + $this->margin->top + $this->margin->bottom);
      }
    }
    else
    {
        $this->margin = new SpriteRectangle($this->spriteImageRegistry, 0,0,0,0);
        $this->position = new SpriteRectangle($this->spriteImageRegistry, 0, 0, $this->sizeArray[0], $this->sizeArray[1]);
    }
  } // setMargins()
}

