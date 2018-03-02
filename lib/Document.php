<?php

class Document
{
  private $file = null;

  public function __construct($filepath, $mode)
  {
    $this->file = $this->open($filepath, $mode);
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