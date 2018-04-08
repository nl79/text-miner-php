<?php

class Settings
{

  private $settings = [];

  public function __construct()
  {
    $this->init();
  }

  public function init() {

    $shortopts = "s:c:k:i:o:e:v::f:";
    $longopts = [
      'supp:',
      'conf:',
      'k-terms:',
      'input-dir:',
      'output-dir:',
      'ext:',
      'verbose::',
      'files:'
    ];
    $options = getopt($shortopts, $longopts);

    foreach($options as $key => $val) {
      $this->set($key, $val);
    }
  }

  public function set($key, $value) {
    $this->settings[$key] = $value;
    return $this;
  }
  public function get($key, $default = null) {

    if(isset($this->settings[$key]) && $this->settings[$key]) {
      return $this->settings[$key];
    }

    return $default;
  }

  public function raw() {
    return $this->settings;
  }
}

$conf = new Settings();