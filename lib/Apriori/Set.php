<?php

  class Set {

    private $set = [];
    private $meta = [];

    public function __construct($values, $meta = null) {
      $this->set = $this->build($values);
      $this->meta = $meta;
    }

    public function toString() {
      $str = 'SET: { ' . implode(',', $this->values()) . ' } ';

      if(!empty($this->meta)) {
        $str .= ':';
        foreach($this->meta as $key => $value) {
          if(is_array($value)) {

            $str .= "\n\t$key = \n\t\t";
            foreach($value as $set => $conf) {
              $str .= "{ " . "$set : $conf" . " }\n\t\t";
            }
            $str .= " ";

          } else {
            $str .= "\n\t$key = { $value }";
          }
        }
      }
      return $str;
    }

    public function get($val = null) {
    }

    public function values() {
      return array_keys($this->set);
    }

    public function containsAnyOf($set) {
      foreach($set->values() as $val) {

        if(array_key_exists($val, $this->set)) {

          return true;
        }
      }
      return false;
    }

    public function isSubsetOf($set) {
      foreach($this->values() as $item) {
        if(!in_array($item, $set)){
          return false;
        }
      }
      return true;
    }

    public function equals($set) {
      $values = null;

      if($set instanceof Set) {
        $values = array_values($set->values());
      } elseif( is_array($set)) {
        $values = $set;
      }

      sort($values);
      if($values == array_values($this->values())) {
        return true;
      }

      return false;
    }

    public function add($val) {
      if(is_scalar($val)) {
        $this->set[$val] = null;
      }

      return $this;
    }

    public function remove($val) {
      if(isset($this->set[$val])){
        // Delete the key
        unset($this->set[$val]);
      }

      return $this;
    }

    public function meta($key, $value = null) {
      if(empty($key) || !is_scalar($key)) {
        throw new Exception('Invalid Key value supplied');
      }

      if(is_null($value)) {
        return isset($this->meta[$key]) ? $this->meta[$key] : null;
      } else {
        $this->meta[$key] = $value;
        return $value;
      }
    }

    private function build($values) {

      if(empty($values)) {
        return [];
      }

      if(is_scalar($values)) {
        return [$values => null];
      }

      if(!is_array($values)) {
        return [];
      }

      $set = [];

      $sorted = sort($values);

      foreach($values as $value) {
        $set[$value] = null;
      }

      return $set;
    }
  }
