<?php

class Settings
{

  private $settings = [];

  public function __construct()
  {

  }

  public function init() {

    $shortopts = "s:c:k:";
    $longopts = [
      'supp:',
      'conf:',
      'k-terms:'
    ];
    $options = getopt($shortopts, $longopts);
// Default Values
    $confidence = .5;
    $support = .5;
    $kterms = 5;

    if(isset($options['supp'])) {
      $support = $options['supp'];
    }

    if(isset($options['conf'])) {
      $confidence = $options['conf'];
    }

    if(isset($options['k-terms'])) {
      $kterms = $options['k-terms'];
    }

    $config = [
      'confidence' => $confidence,
      'support' => $support
    ];

  }
  public function get($key) {

  }

}