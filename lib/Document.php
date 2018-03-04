<?php

class Document
{
  private $file = null;
  private $filepath = '';

  public function __construct($filepath, $mode)
  {
    $this->filepath = $filepath;
    $this->file = $this->open($filepath, $mode);
  }

  public function name() {
    $parts = preg_split('/[\\\\\/]/', trim($this->filepath));
    return $parts[count($parts)-1];
  }

  public function path() {
    return $this->filepath;
  }

  public function __toString()
  {
    // TODO: Implement __toString() method.
    return $this->filepath;
  }

  public function open($filepath, $mode) {
    // Check if file
    if(!is_file($filepath)) {
      throw new Exception('Invalid Filepath supplied');
    }

    // Open file and save handle
    return fopen($filepath, $mode);

  }

  public function close($handle = null) {
    if(!is_null($handle)) {
      return fclose($handle);
    }
    return fclose($this->file);
  }

  public function read($size = null) {
    if(!$this->finished()) {
      return is_numeric($size) ? fgetss($this->file, $size): fgets($this->file);
    }
    return null;
  }

  public function readSafe($size = null) {
    if(!$this->finished()) {
      return is_numeric($size) ? fgetss($this->file, $size): fgetss($this->file);
    }
    return null;
  }

  public function write($str, $length = null) {
    // Validate input and file handler.

    if(is_numeric($length)) {
      return fwrite($this->file, $str, $length);
    } else {
      return fwrite($this->file, $str, $length);
    }
  }

  public function writeLine($str, $length = null) {
    // Validate input and file handler.
    $str.= PHP_EOL;
    return $this->write($str, $length);
  }

  public function finished() {
    if(is_null($this->file)) {
      return true;
    }

    return feof($this->file);
  }

  public function __destruct()
  {
    // TODO: Implement __destruct() method.
    $this->close();
  }
}