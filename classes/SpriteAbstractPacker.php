<?php
abstract class SpriteAbstractPacker
{
  //abstract public static function pack(array &$registry, array $longestWidth, array $longestHeight, $totalArea);
  abstract public function pack(SpriteSprite &$sprite);
}
