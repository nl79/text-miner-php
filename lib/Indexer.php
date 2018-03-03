<?php

class Item {
  private $meta = [];
  private $count = 0;
  private $value = null;

  public function __construct($value)
  {
  }

}

class Indexer
{
  private $index = [];

  public function __construct()
  {
  }

  public function getIndex() {
    return $this->index;
  }

  public function index($key, $val, $meta = null) {
    if(!isset($this->index[$key])) {
      $this->index[$key] = [$val => 1];
      return $this;
    }

    $key = &$this->index[$key];

    if(isset($key[$val])) {
      $key[$val] = !is_null($meta) ? $meta : $key[$val] + 1;
    } else {
      $key[$val] = !is_null($meta) ? $meta : 1;
    }

    return $this;
  }

  public function get($key) {

  }

  public function count($key) {

  }
}