<?php
/**
 * Load the CSprite YAML configuration file.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class CSpriteConfig
{
  const JPG   = 'jpg';
  const GIF   = 'gif';
  const PNG   = 'png';
  const PNG8  = 'png8';

  /**
   * Instance name
   *
   * @var string
   * @access protected
   */
  protected $name;

  /**
   * The YAML configuration file path.
   *
   * @access protected
   * @var string
   */
  protected $yaml_file;

  /**
   * The YAML configuration data.
   * @var Spyc
   * @access  protected
   */
  protected $config;

  /**
   * This object hash
   * @var string
   */
  protected $hash;

  /**
   * A named array of CSpriteConfig objects.
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
   * @return  CSprite              A CSpriteConfig implementation instance.
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
      self::$instances[$instance_name] = new $class(null, $instance_name);
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
  public static function hasInstance($instance_names = 'default')
  {
    return isset(self::$instances[$instance_name]);
  } // hasInstance()

  /**
   * Instanciate a new CSpriteConfig.
   *
   * @param  string $yaml_file  A YAML configuration file path.
   * @param  string $name       The instance name.
   * @access public
   * @return CSpriteConfig      The new CSpriteConfig instance.
   */
  public function __construct($yaml_file = null, $name = 'default')
  {
    $this->name = $name;
    $this->hash = null;
    $this->load($yaml_file);

    $rootDir = $this->get('rootDir');
    if((! $rootDir) || $rootDir == '/') // Test if rootDir defined.
    {
      // Compute rootDir.
      $local= getenv("SCRIPT_NAME");
      $absolute = realpath(basename($local)) . '/';
      $absolute =str_replace("\\","/",$absolute);
      $rootDir = preg_replace('`'.$local.'`si', '', $absolute, 1);
      $this->set('rootDir', $rootDir);
    } // Test if rootDir defined.

    if (!isset(self::$instances))
    {
      self::$instances = array();
    }
    self::$instances[$name] = $this;
  } // __construct()

  /**
   * Load configuration data from YAML file.
   * 
   * @param   string $yaml_file  A YAML configuration file path.
   * @access  public
   * @return  CSpriteConfig      This object.
   */
  public function load($yaml_file = null)
  {
    if((! $yaml_file) && defined('SPRITE_CONFIG_FILE'))
    {
      $yaml_file = SPRITE_CONFIG_FILE;
    }

    if(! $yaml_file)
    {
      Throw new SpriteException('No configuration file specified. Please define SPRITE_CONFIG_FILE.', 101);
    }

    $this->yaml_file = $yaml_file;
    $this->config = Spyc::YAMLLoad($this->yaml_file);

    return $this;
  } // load()

  /**
   * Get this object YAML configuration file path.
   * 
   * @access  public
   * @return string  A YAML file path.
   */
  public function getYamlFile()
  {
    return $this->yaml_file;
  } // getYamlFile()

  /**
   * Get a configuration value.
   * 
   * @param  string $name A value name.
   * @access  public
   * @return mixed        The value.
   */
  public function get($name)
  {
    return isset($this->config[$name]) ? @($this->config[$name]) : null;
  } // get()

  /**
   * Set a configuration value.
   * @param  string $name   A value name.
   * @param  mixed  $value  A value.
   * @access  public
   * @return  CSpriteConfig This object.
   */
  public function set($name, $value)
  {
    $this->config[$name] = $value;

    return $this;
  } // set()

  /**
   * Print a debug message if debug is active.
   * @param  string $message The debug message.
   * @access  public
   * @return CSpriteConfig This object.
   */
  public function debug($message)
  {
    if($this->get('debug'))
    {
      echo $message."<br/>"."\n";
    }

    return $this;
  } // debug()

  /**
   * Get this object hash.
   * 
   * @access  public
   * @return string A hash.
   */
  public function getHash()
  {
    if(!$this->hash)
    {
      $this->hash = md5(serialize($this->config));
    }

    return $this->hash;
  } // getHash()
}
