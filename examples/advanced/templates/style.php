<?php
// SPRITE_EXAMPLE_REL_DIR - this should already be defined
?>

.dev {<?php echo Sprite::ppStyle(SPRITE_EXAMPLE_REL_DIR.'/images/dev-republik.gif', 
            array('inline'=>true)); ?>}
.icon {<?php echo Sprite::ppStyle(SPRITE_EXAMPLE_REL_DIR.'/images/icons/copy.gif', 
            array('inline'=>true, 'sprite-align'=>'bottom')); ?>}
