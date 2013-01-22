<?php
/**
 * The SpriteTemplateRegistry class. Manage the sprite templates.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteTemplateRegistry implements SpriteAbstractConfigSource
{

  /**
   * The CSprite parent object.
   *
   * @var CSprite
   * @access protected
   */
  protected $cSprite;

  /**
   * The template registry.
   * @var array
   */
  protected $registry;

  /**
   * Instanciate a new SpriteTemplateRegistry.
   *
   * @param  CSprite $cSprite The parent object.
   * @access public
   * @return SpriteTemplateRegistry This object.
   */
  public function __construct(CSprite &$cSprite)
  {
    $this->cSprite = $cSprite;

    $this->registry = array();
  } // __construct()

  /**
   * Get this object CSprite instance.
   * @return CSprite A CSprite instance.
   */
  public function getCSprite()
  {
    return $this->cSprite;
  } // getCSprite()

  /**
   * Get this object CSprite config instance.
   *
   * @access  public
   * @return  CSpriteConfig A CSpriteConfig instance.
   */
  public function getSpriteConfig()
  {
    return $this->cSprite->getSpriteConfig();
  } // getSpriteConfig()

  /**
   * Get this object CSprite cache manager.
   * @return SpriteCache a SpriteCache object.
   */
  public function getSpriteCache()
  {
    return $this->cSprite->getSpriteCache();
  } // getSpriteCache()

  public function registerTemplate($relTemplatePath, $outputName, $outputPath = null){
    $absPath = $this->getSpriteConfig()->get('rootDir').$relTemplatePath;
    if(file_exists($absPath))
    {
      $this->registry[] = new SpriteTemplate($this, $relTemplatePath, $outputName, $outputPath);
    }
    else
    {
      throw new SpriteException($absPath.' - This template file does not exist');
    }
  }

  public function getTemplate($tmplName){
  }

  public function preProcessTemplates(){
    //Do some directory checks
    if(count($this->registry))
    {
      if(!is_dir($this->getSpriteConfig()->get('rootDir').$this->getSpriteConfig()->get('relPreprocessorDirectory')))
      {
        throw new SpriteException($this->getSpriteConfig()->get('rootDir').$this->getSpriteConfig()->get('relPreprocessorDirectory').' - this is not a valid directory');
      }
      if(!is_writable($this->getSpriteConfig()->get('rootDir').$this->getSpriteConfig()->get('relPreprocessorDirectory')))
      {
        throw new SpriteException($this->getSpriteConfig()->get('rootDir').$this->getSpriteConfig()->get('relPreprocessorDirectory').' - this directory is not writable');
      }
      if(is_array($this->registry))
      {
        foreach($this->registry as $template)
        {
          if($this->getSpriteCache()->needsCreation($this->getSpriteConfig()->get('rootDir').$this->getSpriteConfig()->get('relPreprocessorDirectory').'/'.$template->getPreprocessName())){
            $parser_class = $this->getSpriteConfig()->get('parser');
            $parser = new $parser_class($this);
            $parser->parse($template);
            $this->preprocess($template);
          }
        }
      }
    }
  }

  protected function preprocess($template)
  {
    $inputFile  = $this->getSpriteConfig()->get('rootDir') . $template->getRelativePath();
    $outputFile = $this->getSpriteConfig()->get('rootDir') . $this->getSpriteConfig()->get('relPreprocessorDirectory').'/'.$template->getPreprocessName();
    if(file_exists($outputFile))
    {
      unlink($outputFile);
    }

    ob_start();
    require_once($inputFile);
    $output = ob_get_clean();
    $processedString = preg_replace(array('`\[\?php`si','`\?\]`si') , array('<?php', '?>'),$output);

    if(file_put_contents($outputFile, $processedString) === false)
    {
      throw new SpriteException($outputFile.' - could not write preprocess file.');
    }
    return;
  }

  public function processTemplates()
  {
    if(is_array($this->registry))
    {
      foreach($this->registry as $template)
      {
        if($this->getSpriteCache()->needsCreation($this->getSpriteConfig()->get('rootDir') . $template->getRelOutputPath()))
        {
          $this->process($template);
        }
      }
    }
  }

  protected function process($template){
    $inputFile  = $this->getSpriteConfig()->get('rootDir').$this->getSpriteConfig()->get('relPreprocessorDirectory').'/'.$template->getPreprocessName();
    $outputFile = $this->getSpriteConfig()->get('rootDir').$template->getRelOutputPath();

    if(! file_exists($inputFile))
    {
      return;
    }

    if(file_exists($outputFile))
    {
      unlink($outputFile);
    }

    ob_start();
    require_once($inputFile);
    $output = ob_get_clean();
    if($this->getSpriteConfig()->get('deletePreprocess'))
    {
      unlink($inputFile);
    }

    if(file_put_contents($outputFile, $output) === false)
    {
      throw new SpriteException($outputFile.' - could not write preprocess file.');
    }
    return;
  }
}