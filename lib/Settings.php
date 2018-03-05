<?php

class Settings
{

  private $settings = [];

  public function __construct()
  {
    $this->init();
  }

  public function init() {

    $shortopts = "s:c:k:";
    $longopts = [
      'supp:',
      'conf:',
      'k-terms:'
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

    if(isset($this->settings[$key])) {
      return $this->settings[$key];
    }

    return $default;
  }

}

$conf = new Settings();