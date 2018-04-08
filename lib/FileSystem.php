<?php

class FileSystem
{

  private $current = null;

  public function __construct()
  {
  }

  public function formatPath($path) {
    if(empty($path)) {
      return '';
    }
    $parts = preg_split('/[\\\\\/]/', trim($path));

    return rtrim(implode(DIRECTORY_SEPARATOR, $parts), DIRECTORY_SEPARATOR);
  }

  public function listFiles($dir, $ext =  null) {

    if(!is_dir($dir)) {

      return [];
    }

    $dir = $this->formatPath($dir) . DIRECTORY_SEPARATOR;

    $files = scandir($dir);

    if(empty($ext)) {
      return $files;
    }

    // If extension supplied filter the results based on the extensions.
    $ext = is_scalar($ext) ? [$ext] : $ext;

    $ext = array_map(function ($item) {
      return strtolower($item);
    }, $ext);

    $list = [];
    $count = count($files);

    for($i = 0; $i < $count; ++$i) {
      $file = $files[$i];

      $filepath = $dir . $file;

      if(!is_file($filepath)) { continue; }

      $info = pathinfo($filepath);

      if(isset($info['extension']) && in_array($info['extension'], $ext)) {
       $list[] = $filepath;
      }

    }
    return $list;
  }

  public function readFile($filepath) {

  }

}