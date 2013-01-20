<?php
class SpriteTemplateRegistry
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

  public function __construct(cSprite &$cSprite)
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

  public function registerTemplate($relTemplatePath, $outputName, $outputPath = null){
    $absPath = SpriteConfig::get('rootDir').$relTemplatePath;
    if(file_exists($absPath))
    {
      $this->registry[] = new SpriteTemplate($relTemplatePath, $outputName, $outputPath);  
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
      if(!is_dir(SpriteConfig::get('rootDir').SpriteConfig::get('relPreprocessorDirectory')))
      {
        throw new SpriteException(SpriteConfig::get('rootDir').SpriteConfig::get('relPreprocessorDirectory').' - this is not a valid directory');
      }
      if(!is_writable(SpriteConfig::get('rootDir').SpriteConfig::get('relPreprocessorDirectory')))
      {
        throw new SpriteException(SpriteConfig::get('rootDir').SpriteConfig::get('relPreprocessorDirectory').' - this directory is not writable');
      }
      if(is_array($this->registry))
      {
        foreach($this->registry as $template)
        {
          if(SpriteCache::needsCreation(SpriteConfig::get('rootDir').SpriteConfig::get('relPreprocessorDirectory').'/'.$template->getPreprocessName())){
            call_user_func(SpriteConfig::get('parser').'::parse', $template);
            $this->preprocess($template);
          }
        }
      }
    }
  }

  protected function preprocess($template)
  {
    $inputFile  = SpriteConfig::get('rootDir') . $template->getRelativePath();
    $outputFile = SpriteConfig::get('rootDir') . SpriteConfig::get('relPreprocessorDirectory').'/'.$template->getPreprocessName();
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
        if(SpriteCache::needsCreation(SpriteConfig::get('rootDir').$template->getRelOutputPath()))
        {
          $this->process($template);
        }
      }
    }
  }

  protected function process($template){
    $inputFile  = SpriteConfig::get('rootDir').SpriteConfig::get('relPreprocessorDirectory').'/'.$template->getPreprocessName();
    $outputFile = SpriteConfig::get('rootDir').$template->getRelOutputPath();
    if(file_exists($outputFile)){
      unlink($outputFile);
    }

    ob_start();
    require_once($inputFile);
    $output = ob_get_clean();
    if(SpriteConfig::get('deletePreprocess')){
      unlink($inputFile);
    }

    if(file_put_contents($outputFile, $output) === false){
      throw new SpriteException($outputFile.' - could not write preprocess file.');
    }
    return;
  }
}