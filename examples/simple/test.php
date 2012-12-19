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
//relative to web root, this is where template files and generated CSS will go
SpriteConfig::set('relTmplOutputDirectory', SPRITE_EXAMPLE_REL_DIR.'/cache');

//Set the cacheTime to 0 to prevent any caching
SpriteConfig::set('cacheTime', 0);
SpriteConfig::set('transparentImagePath', SPRITE_EXAMPLE_REL_DIR.'/1_1_trans.gif');
//Now we send the path where all the image files we want to use for sprite generation
//to the SpriteImageRegistry. You can do this multiple times for different directories
//See the advanced example for registering individual filesize
Sprite::ppRegister(SPRITE_EXAMPLE_REL_DIR.'/images');



//Now we run the processSprites() funciton. This MUST be run before you can access any of the
//Sprites in your template or elsewhere.
Sprite::process();

//Let's start out template output!
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>cSprite Simple Example</title>
  <?php echo Sprite::getAllCssInclude(); /*This will output the complete css of all of the sprites*/ ?>
</head>

<body style="background-color: #f2f2f2">
<h2>cSprites Simple Example</h2>
<table border="0" width="100%" cellpadding="20">
  <tr valign="top">
    <td width="50%">
      <h3>The Full Sprite Images</h3>
      <p>Notice that it scans all the files and divides them up by image type. So you get a sprite for each, jpg, png and gif;</p>
      <?php
      //Outputting the full sprites wouldn't be something you normally do so it takes a little more code
      foreach(SpriteStyleRegistry::getStyleNodes() as $spriteGroup):?>
        <?php $backgroundNode = $spriteGroup->getBackgroundStyleNode(); ?>
        <h4><?php echo $backgroundNode->getBackgroundImage(); ?></h4> 
        <img src="<?php echo $backgroundNode->getBackgroundImage(); ?>"/>
      <?php endforeach; ?>
      <br>
    </td>
    <td>
      <h3>Individual Images</h3>
      <p>These images come from the generated sprites and dynamic css styles.</p>
      <p>
        <h4>Pseudo Image Tag w/Css Classes</h4>
        <code>
        &lt;?php echo Sprite::image_tag(SPRITE_EXAMPLE_REL_DIR.'/images/wave.jpg'); ?&gt;<br/><br/>
        <?php $output =  Sprite::image_tag(SPRITE_EXAMPLE_REL_DIR.'/images/wave.jpg'); ?>
        HTML Output is:<br/><?php echo htmlspecialchars($output); ?>
        </code><br/>
        <?php echo $output; ?>
      </p>
      <p>
        <h4>Pseudo Image Tag w/Inline Css Styles</h4>
        <code>
        &lt;?php echo Sprite::image_tag(SPRITE_EXAMPLE_REL_DIR.'/images/camels.jpg', array('inline'=>true)); ?&gt;<br/><br/>
        <?php $output =  Sprite::image_tag(SPRITE_EXAMPLE_REL_DIR.'/images/camels.png', array('inline'=>true)); ?>
        HTML Output is:<br/><?php echo htmlspecialchars($output); ?>
        </code><br/>
        <?php echo $output; ?>
      </p>
      <p>
        <h4>Div Element with Background Image using Css Classes</h4>
        <code>
        &lt;div class="&lt;?php echo Sprite::styleClass(SPRITE_EXAMPLE_REL_DIR.'/images/Box In Hand.png'); ?&gt;"&gt;&lt;/div&gt;
        <br/><br/>
        <?php $output =  Sprite::styleClass(SPRITE_EXAMPLE_REL_DIR.'/images/Box In Hand.png');  ?>
        HTML Output is:<br/>&lt;div class="<?php echo htmlspecialchars($output); ?>"&gt;&lt;/div&gt;
        </code><br/>
        <div class="<?php echo $output?>"></div>
      </p>
      <p>
        <h4>Div Element with Background Image using Inline Css Styles</h4>
        <code>
        &lt;div style="&lt;?php echo Sprite::styleWithBackground(SPRITE_EXAMPLE_REL_DIR.'/images/coffee.JPG', array('inline'=>true)); ?&gt;"&gt;&lt;/div&gt;
        <br/><br/>
        <?php $output =  Sprite::styleWithBackground(SPRITE_EXAMPLE_REL_DIR.'/images/coffee.JPG', array('inline'=>true));  ?>
        HTML Output is:<br/>&lt;div style="<?php echo htmlspecialchars($output); ?>"&gt;&lt;/div&gt;
        </code><br/>
        <div style="<?php echo $output?>"></div>
      </p>
    </td>
  </tr>
  

</table>
</body>

</html>


