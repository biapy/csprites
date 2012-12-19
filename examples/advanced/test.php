<?php
/**
* @version $Id$
* @package cSprites
* @copyright (C) 2008 Adrian Mummey, DevRepublik
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Adrian Mummey <amummey@gmail.com>
* cSprites is Free Software
*/

/*This example show how to quickly generate everything you need to use cSprite.
  Here we will have cSprite scan and generate a Sprite from the whole `images`
  directory and then display some pseudo image tags. 
  Also remember here we are setting many config variables, however
  you can always just edit the config.yml so you don't have to set them during
  runtime. 
  Check out the advanced example to see cssMetaFiles and more technical use of the
  css postprocessing.
  */


//We can define the location of the Sprite Config file we want to use PRIOR to the inclusion of Sprite.php
define('SPRITE_CONFIG_FILE', realpath(dirname(__FILE__)).'/config.yml');

//This is the relative path directory we are in from the web root
define('SPRITE_EXAMPLE_REL_DIR', dirname(getenv("SCRIPT_NAME")));
//We need to include the main Sprite File which will setup the autoloading and config
require_once('../../Sprite.php');

//Remember all of these config settings can be set in the config.yml file so no need to set during runtime
//relative to web root, this is where the generated sprite images will go
SpriteConfig::set('relImageOutputDirectory', SPRITE_EXAMPLE_REL_DIR.'/cache');
SpriteConfig::set('relTmplOutputDirectory', SPRITE_EXAMPLE_REL_DIR.'/cache');
SpriteConfig::set('relPreprocessorDirectory', SPRITE_EXAMPLE_REL_DIR.'/preprocess');

//Set the cacheTime to 0 to prevent any caching
SpriteConfig::set('cacheTime', 0);
SpriteConfig::set('transparentImagePath', SPRITE_EXAMPLE_REL_DIR.'/1_1_trans.gif');


//Let's register our templates
Sprite::registerTemplate(SPRITE_EXAMPLE_REL_DIR.'/templates/style.php', 'style.css');
Sprite::registerTemplate(SPRITE_EXAMPLE_REL_DIR.'/templates/tmpl.php', 'tmpl.php');
Sprite::process();

require_once('cache/tmpl.php');
?>


