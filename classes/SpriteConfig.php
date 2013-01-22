<?php
/**
 * The SpriteConfig class. Static proxy to CSpriteConfig default instance.
 * This class ensure compatibility with CSprite 1.0.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class SpriteConfig
{
  const JPG   = 'jpg';
  const GIF   = 'gif';
  const PNG   = 'png';
  const PNG8  = 'png8';

  /**
   * Print a debug message if debug is active in default CSpriteConfig instance.
   * @param  string $message The debug message.
   * @access  public
   * @static
   * @return CSpriteConfig This object.
   */
  public static function debug($message)
  {
    return CSpriteConfig::getInstance()->debug($message);
  } // debug()

  /**
   * Get a configuration value from default CSpriteConfig instance.
   * 
   * @param  string $name A value name.
   * @access  public
   * @static
   * @return mixed        The value.
   */
  public static function get($name)
  {
    return CSpriteConfig::getInstance()->get($name);
  } // get()

  /**
   * Set a configuration value in default CSpriteConfig instance.
   * @param  string $name   A value name.
   * @param  mixed  $value  A value.
   * @access  public
   * @static
   * @return  CSpriteConfig This object.
   */
  public static function set($name, $value)
  {
    return CSpriteConfig::getInstance()->set($name, $value);
  } // set()

  /**
   * Get the hash from default CSpriteConfig instance.
   * 
   * @access  public
   * @static
   * @return string A hash.
   */
  public static function getHash()
  {
    return CSpriteConfig::getInstance()->getHash();
  } // getHash()
}
