<?php
/**
 * The Sprite class. Static proxy to CSprite default instance.
 * This class ensure compatibility with CSprite 1.0.
 *
 * @package  CSprite
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class Sprite
{
  public static function process()
  {
  	CSprite::getInstance()->process();
  } // process()

  /**
   * déclare un nouveau fichier template.
   * @param  string $relativeTemplatePath Le chemin relatif vers le modèle.
   * @param  string $outputName           Le nom du modèle.
   * @param  string $outputPath           Le chemin de sortie du modèle.
   * @return Sprite                       Renvoie cet objet.
   */
  public static function registerTemplate($relativeTemplatePath, $outputName = null, $outputPath = null)
  {
   	CSprite::getInstance()->registerTemplate($relativeTemplatePath, $outputName, $outputPath);
  } // registerTemplate()

  public static function getStyleNode($path)
  {
    return CSprite::getInstance()->getStyleNode($path);
  }

  public static function style($path, array $params = array())
  {
    return CSprite::getInstance()->style($path, $params);
  }

  /**
   * Add a several images from a directory to the default sprite.
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
   * @static
   * @return  CSprite The sprite object.
   */
  public static function ppRegister($path, array $params = array())
  {
    return CSprite::getInstance()->ppRegister($path, $params);
  } // ppRegister()

  /**
   * Add a single image to the default sprite.
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
   * @return  CSprite The sprite object.
   */
  public static function addImage($path, array $params = array())
  {
    return CSprite::getInstance()->addImage($path, $params);
  } // addImage()

  public static function ppStyle($path, array $params = array())
  {
    return CSprite::getInstance()->ppStyle($path, $params);
  }

  public static function styleWithBackground($path, array $params = array())
  {
    return CSprite::getInstance()->styleWithBackground($path, $params);
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
  public static function image_tag($path, $params = array(), $attributes = array())
  {
    return CSprite::getInstance()->image_tag($path, $params, $attributes);
  }

  public static function styleClass($path, $params = array())
  {
    return CSprite::getInstance()->styleClass($path, $params);
  }

  public static function getAllCssInclude()
  {
    return CSprite::getInstance()->getAllCssInclude();
  }

  public static function getCssInclude($spriteName, $imageType = null){
    return CSprite::getInstance()->getCssInclude($spriteName, $imageType);
  }

}