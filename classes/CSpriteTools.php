<?php
/**
 * The CSpriteTools class. Regroup static usefull functions.
 *
 * @package  CSprite
 * @author   Adrian Mummey
 * @author   Pierre-Yves Landuré <pierre-yves.landure@biapy.fr>
 * @version  2.0.0
 */
class CSpriteTools
{

  /**
   * Compute the relative path between two absolute paths.
   *
   * @param  string $from_path      The source path.
   * @param  string $to_path        The target path.
   * @param  string $path_separator An optionnal path separator.
   * @access public
   * @static
   * @return string                 A relative path.
   */
  public static function relativePath($from_path, $to_path, $path_separator = DIRECTORY_SEPARATOR)
  {
    $exploded_from = explode($path_separator, rtrim($from_path, $path_separator));
    $exploded_to = explode($path_separator, rtrim($to_path, $path_separator));
    while(count($exploded_from) && count($exploded_to) && ($exploded_from[0] == $exploded_to[0]))
    {
      array_shift($exploded_from);
      array_shift($exploded_to);
    }
    return str_pad("", count($exploded_from) * 3, '..'.$path_separator).implode($path_separator, $exploded_to);
  } // relativePath()

  /**
   * Compute the PHP string representation of an array.
   * @param  array  $array An array.
   * @param  integer $depth An optionnal depth limit.
   * @access public
   * @static
   * @return string An array written in PHP (array(..))
   */
  public static function arrayToString($array, $depth = 0)
  {
    $tab = '';
    if($depth > 0){
      $tab = implode('', array_fill(0, $depth, "\t"));
    }

    $text="array(\n";
    $count=count($array);
    $x =0 ;
    foreach ($array as $key=>$value){
       $x++;
       if (is_array($value)){
         if(substr($text,-1,1)==')')    $text .= ',';
         $text.=$tab."\t".'"'.$key.'"'." => ".self::arToStr($value, $depth+1);
         if ($count!=$x) $text.=",\n";
         continue;
       }

       $text.=$tab."\t"."\"$key\" => \"$value\""; 
       if ($count!=$x) $text.=",\n";
    }

    $text.="\n".$tab.")\n";
    if(substr($text, -4, 4)=='),),')$text.='))';
    return $text;
  } // arrayToString()

  /**
   * Get the files present if the given path and sub-directories.
   *
   * @param  string $path A path.
   * @access  public
   * @static
   * @return array An array of files path.
   */
  public static function buildFileList($path)
  {
    $files = array();
    $fileObjs = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach($fileObjs as $name=>$fileObj)
    {
      if($fileObj->isFile()
          && strpos($name, DIRECTORY_SEPARATOR . '.svn' . DIRECTORY_SEPARATOR) === false
          && strpos($name, '.DS_Store') === false
          && strpos($name, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR) === false)
      {
        $files[] = $name;
      }
    }

    return $files;
  } // buildFileList()
}