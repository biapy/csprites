<?php

class CSprite
{

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
   * This a named array of CSprite objects.
   *
   * @var array
   * @access protected
   */
  protected static $instance = null;

  /**
   * Retrieve an instance of this class.
   *
   * @param  string $instance_name A optionnal instance name.
   * @return sfContext A sfContext implementation instance.
   */
  public static function getInstance($instance_name = 'default')
  {
    if (!isset(self::$instance))
    {
      self::$instance = array();
    }

    if (!isset(self::$instance[$instance_name]))
    {
      $class = __CLASS__;
      self::$instance[$instance_name] = new $class();
    }

    return self::$instance[$instance_name];
  }

  /**
   * Retrieve an instance of this class.
   *
   * @param  string $instance_name A optionnal instance name.
   * @return sfContext A sfContext implementation instance.
   */
  public static function hasInstance($instance_name = 'default')
  {
    return isset(self::$instance[$instance_name]);
  }

  /**
   * Instanciate a new Sprite.
   * 
   * @access public
   * @return Sprite the new sprite.
   */
  public function __construct()
  {
    $this->spriteTemplateRegistry = new SpriteTemplateRegistry($this);
    $this->spriteImageRegistry = new SpriteImageRegistry($this);
    $this->spriteStyleRegistry = new SpriteStyleRegistry($this);

    return $this;
  } // __construct()

  public function getTemplateRegistry()
  {
    return $this->spriteTemplateRegistry;
  }

  public function getImageRegistry()
  {
    return $this->spriteImageRegistry;
  }

  public function getStyleRegistry()
  {
    return $this->spriteStyleRegistry;
  }

  public function process()
  {
    $this->spriteImageRegistry->processSprites();
    $this->spriteTemplateRegistry->processTemplates();    
    $this->spriteStyleRegistry->processCss();

    return $this;
  } // process()

  /**
   * déclare un nouveau fichier template.
   * @param  string $relativeTemplatePath Le chemin relatif vers le modèle.
   * @param  string $outputName           Le nom du modèle.
   * @param  string $outputPath           Le chemin de sortie du modèle.
   * @return Sprite                       Renvoie cet objet.
   */
  public function registerTemplate($relativeTemplatePath, $outputName = null, $outputPath = null)
  {
    $this->spriteTemplateRegistry->registerTemplate($relativeTemplatePath, $outputName, $outputPath);

    return $this;
  } // registerTemplate()

  public function getStyleNode($path)
  {
    return $this->spriteStyleRegistry->getStyleNode($path);
  }

  public function style($path, array $params = array())
  {
    return ($node = $this->spriteStyleRegistry->getStyleNode($path))?($node->renderStyle($params)):('');
  }

  public function ppRegister($path, array $params = array())
  {
    $this->spriteImageRegistry->register($path, $params);
  }

  public function ppStyle($path, array $params = array())
  {
    //SpriteImageRegistry::register($path, @$params['name'], @$params['imageType']);
    $this->SpriteImageRegistry->register($path, $params);
    return "<?php echo $this->style('".$path."',".self::arToStr($params)."); ?>";
  }

  public function styleWithBackground($path, array $params = array())
  {
    return ($node = $this->spriteStyleRegistry->getStyleNode($path))?($node->renderStyleWithBackground($params)):('');
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
   * @param  string $path         A relative path to the original image.
   * @param  array  $params       A array of options.
   * @param  array  $attributes   A array of HTML attributes to add to the tag.
   * @return string               A HTML img tag.
   */
  public function image_tag($path, $params = array(), $attributes = array())
  {
    return ($node = $this->spriteStyleRegistry->getStyleNode($path))?($node->image_tag($params, $attributes)):('');
  }

  public function styleClass($path, $params = array())
  {
    return ($node = $this->spriteStyleRegistry->getStyleNode($path))?($node->renderClass($params)):('');
  }

  public function getAllCssInclude()
  {
    return $this->spriteStyleRegistry->getAllCssInclude();
  }

  public function getCssInclude($spriteName, $imageType = null){
    return $this->spriteStyleRegistry->getCssInclude($spriteName, $imageType);
  }




  protected static function arToStr($array, $depth = 0)
  {
    $tab = '';
    if($depth > 0){
      $tab = implode('', array_fill(0, $depth, "\t"));
    }

    $text="array(\n";
    $count=count($array);
    $x =0 ;
    foreach ($array as $key=>$value){
       $x++;
       if (is_array($value)){
         if(substr($text,-1,1)==')')    $text .= ',';
         $text.=$tab."\t".'"'.$key.'"'." => ".self::arToStr($value, $depth+1);
         if ($count!=$x) $text.=",\n";
         continue;
       }

       $text.=$tab."\t"."\"$key\" => \"$value\""; 
       if ($count!=$x) $text.=",\n";
    }

    $text.="\n".$tab.")\n";
    if(substr($text, -4, 4)=='),),')$text.='))';
    return $text;
  }

}

