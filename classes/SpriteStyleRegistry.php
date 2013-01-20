<?php
class SpriteStyleRegistry
{

  /**
   * The CSprite parent object.
   *
   * @var CSprite
   * @access protected
   */
  protected $cSprite;

  /**
   * The style registry.
   * @var array
   */
  protected $registry;

  protected $hash;

  public function __construct(cSprite &$cSprite)
  {
    $this->cSprite = $cSprite;

    $this->registry = array();
    $this->hash = null;
  } // __construct()

  /**
   * Get this object CSprite instance.
   * @return CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this->cSprite;
  } // getCSprite()

  public function addSprite(SpriteSprite &$sprite)
  {
    $this->registry[$sprite->getKey()] = new SpriteStyleGroup($sprite);

    return $this;
  } // addSprite()

  public function processCss()
  {
    $allCss = '';
    foreach($this->registry as &$styleGroup){
      $filepath = SpriteConfig::get('rootDir') . $styleGroup->getRelativePath();
      $tempCss = $styleGroup->getCss();

      file_put_contents($filepath, $tempCss);
      $allCss .= $tempCss;
    }
    file_put_contents(SpriteConfig::get('rootDir') . $this->getRelativePath(), $allCss);
  } // processCss()

  public function getStyleNodes()
  {
    return $this->registry;
  }

  public function getStyleNode($path){
    $node = null;

    foreach($this->registry as $spriteGroup){
     if(isset($spriteGroup[$path])){
        $node = $spriteGroup[$path];
        break;
      }
    }
    return $node;
  }

  public function getCssInclude($spriteName, $imageType = null){
    $tempSprite = new SpriteSprite($spriteName, $imageType);
    if(isset($this->registry[$tempSprite->getKey()])){
      $sprite = $this->registry[$tempSprite->getKey()];
      return '<link rel="stylesheet" type="text/css" title="'.$sprite->getKey().'" media="all" href="'.$sprite->getRelativePath().'" />'."\n";
    }
    return '';
  }

  public function getAllCssInclude(){
    return '<link rel="stylesheet" type="text/css" title="cSprite CSS" media="all" href="'.$this->getRelativePath().'" />'."\n";
  }

  public function getRelativePath(){
    return SpriteConfig::get('relTmplOutputDirectory').'/'.$this->getFileName();
  }

  public function getFileName(){
    return $this->getHash().'.css';
  }

  public function getHash(){
    if(!$this->hash)
    {
      $this->hash = md5(serialize($this->registry));
    }

    return $this->hash;
  }

}

