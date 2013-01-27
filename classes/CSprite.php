<?php
/**
 * The CSprite class. Automize the creation of CSS sprites from various images.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class CSprite implements SpriteAbstractConfigSource
{

  /**
   * Instance name
   *
   * @var string
   * @access protected
   */
  protected $name;

  /**
   * Templates registry
   *
   * @var SpriteTemplateRegistry
   * @access protected
   */
  protected $spriteTemplateRegistry;

  /**
   * Images registry
   *
   * @var SpriteImageRegistry
   * @access protected
   */
  protected $spriteImageRegistry;

  /**
   * Styles registry 
   * 
   * @var SpriteStyleRegistry
   * @access protected
   */
  protected $spriteStyleRegistry;

  /**
   * Sprite configuration
   * 
   * @var CSpriteConfig
   * @access protected
   */
  protected $spriteConfig;

  /**
   * Sprite cache manager
   * 
   * @var SpriteCache
   * @access protected
   */
  protected $spriteCache;

  /**
   * A named array of CSprite objects.
   *
   * @var array
   * @static
   * @access protected
   */
  protected static $instances = null;

  /**
   * Retrieve an instance of this class.
   *
   * @param   string $instance_name  A optionnal instance name.
   * @access  public
   * @static
   * @return  CSprite              A CSprite implementation instance.
   */
  public static function getInstance($instance_name = 'default')
  {
    if (!isset(self::$instances))
    {
      self::$instances = array();
    }

    if (!isset(self::$instances[$instance_name]))
    {
      $class = __CLASS__;
      self::$instances[$instance_name] = new $class($instance_name);
    }

    return self::$instances[$instance_name];
  } // getInstance()

  /**
   * Check if a instance exists.
   *
   * @param   string $instance_name  A optionnal instance name.
   * @access  public
   * @static
   * @return  boolean                True if the instance exists.
   */
  public static function hasInstance($instance_name = 'default')
  {
    return isset(self::$instances[$instance_name]);
  } // hasInstance()

  /**
   * Instanciate a new CSprite.
   *
   * @param  string $name The instance name.
   * @access public
   * @return CSprite the new sprite.
   */
  public function __construct($name = 'default')
  {
    $this->name = $name;
    $this->spriteTemplateRegistry = new SpriteTemplateRegistry($this);
    $this->spriteImageRegistry = new SpriteImageRegistry($this);
    $this->spriteStyleRegistry = new SpriteStyleRegistry($this);
    $this->spriteConfig = CSpriteConfig::getInstance($name);
    $this->spriteCache = new SpriteCache($this);

    if (!isset(self::$instances))
    {
      self::$instances = array();
    }
    self::$instances[$name] = $this;
  } // __construct()

  /**
   * Get this object CSprite instance.
   *
   * @access  public
   * @return  CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this;
  } // getCSprite()

  /**
   * Get this instance configuration.
   * @return CSpriteConfig a CSpriteConfig object.
   */
  public function getSpriteConfig()
  {
    return $this->spriteConfig;
  } // getSpriteConfig()

  /**
   * Get this instance cache manager.
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->spriteCache;
  } // getSpriteCache()

  /**
   * Get this instance name.
   *
   * @access  public
   * @return string a name.
   */
  public function getName()
  {
    return $this->name;
  } // getName()

  /**
   * Get the template registry.
   *
   * @access  public
   * @return  SpriteTemplateRegistry a template registry.
   */
  public function getTemplateRegistry()
  {
    return $this->spriteTemplateRegistry;
  } // getTemplateRegistry()

  /**
   * Get the image registry.
   *
   * @access  public
   * @return  SpriteImageRegistry a image registry.
   */
  public function getImageRegistry()
  {
    return $this->spriteImageRegistry;
  } // getImageRegistry()

  /**
   * Get the style registry.
   *
   * @access  public
   * @return  SpriteStyleRegistry a style registry.
   */
  public function getStyleRegistry()
  {
    return $this->spriteStyleRegistry;
  } // getStyleRegistry()

  /**
   * Compute the sprite image and its CSS rules.
   *
   * @access  public
   * @return  CSprite this object.
   */
  public function process()
  {
    $this->spriteImageRegistry->processSprites();
    $this->spriteTemplateRegistry->preprocessTemplates();
    $this->spriteTemplateRegistry->processTemplates();
    $this->spriteStyleRegistry->processCss();

    return $this;
  } // process()

  /**
   * Register a new template file
   * @param  string $relativeTemplatePath A template file relative path.
   * @param  string $outputName           An optionnal template name.
   * @param  string $outputPath           An optionnal output path.
   * @return CSprite                      This object.
   */
  public function registerTemplate($relativeTemplatePath, $outputName = null, $outputPath = null)
  {
    $this->spriteTemplateRegistry->registerTemplate($relativeTemplatePath, $outputName, $outputPath);

    return $this;
  } // registerTemplate()

  /**
   * Get the SpriteStyleNode for the given image path.
   * @param  string $path     A image relative path.
   * @return SpriteStyleNode  A SpriteStyleNode.
   */
  public function getStyleNode($path)
  {
    $node = $this->spriteStyleRegistry->getStyleNode($path);

    if(! $node)
    {
      throw new SpriteException(sprintf('Error: no node found for path "%s". Available nodes are: %s.', $path, implode(', ', $this->spriteStyleRegistry->getStyleNodesPaths())), 102);
    }

    return $node;
  } // getStyleNode()

  public function style($path, array $params = array())
  {
    return $this->getStyleNode($path)->renderStyle($params);
  } // style()

  /**
   * Add a several images from a directory to the sprite.
   *
   * Accepted params are:
   *  - name : the sprite name.
   *  - imageType : the image type.
   *  - sprite-margin : margins of the image in the sprite.
   *  - hoverXOffset : Offset to the background X position on hover
   *  - hoverYOffset : Offset to the background Y position on hover
   *
   * @param string $path   The directory path.
   * @param array  $params An array of parameters
   * @access  public
   * @return  CSprite This object.
   */
  public function ppRegister($path, array $params = array())
  {
    $this->spriteImageRegistry->register($path, $params);

    return $this;
  } // ppRegister()

  /**
   * Add a single image to the sprite.
   *
   * Accepted params are:
   *  - name : the sprite name.
   *  - imageType : the image type.
   *  - sprite-margin : margins of the image in the sprite.
   *  - hoverXOffset : Offset to the background X position on hover
   *  - hoverYOffset : Offset to the background Y position on hover
   *
   * @param string $path   The image path.
   * @param array  $params An array of parameters
   * @access  public
   * @return  CSprite This object.
   */
  public function addImage($path, array $params = array())
  {
    $this->spriteImageRegistry->addImage($path, $params);

    return $this;
  } // addImage()

  public function ppStyle($path, array $params = array())
  {
    //SpriteImageRegistry::register($path, @$params['name'], @$params['imageType']);
    $this->spriteImageRegistry->register($path, $params);
    return sprintf("<?php echo CSprite::getInstance('%s')->style('%s', %s); ?>", $this->getName(), $path, CSpriteTools::arrayToString($params));
  } // ppStyle()

  public function styleWithBackground($path, array $params = array())
  {
    return $this->getStyleNode($path)->renderStyleWithBackground($params);
  } // styleWithBackground()

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
   * @param  string $path         A relative path to the original image.
   * @param  array  $params       A array of options.
   * @param  array  $attributes   A array of HTML attributes to add to the tag.
   * @return string               A HTML img tag.
   */
  public function image_tag($path, $params = array(), $attributes = array())
  {
    return $this->getStyleNode($path)->image_tag($params, $attributes);
  } // image_tag()

  public function styleClass($path, $params = array())
  {
    return $this->getStyleNode($path)->renderClass($params);
  } // styleClass()

  public function getAllCssInclude()
  {
    return $this->spriteStyleRegistry->getAllCssInclude();
  } // getAllCssInclude()

  public function getCssInclude($spriteName, $imageType = null){
    return $this->spriteStyleRegistry->getCssInclude($spriteName, $imageType);
  } // getCssInclude()

}

