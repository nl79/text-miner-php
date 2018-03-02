<?php

class EventEmitter
{
  private $events = [];

  public function __construct()
  {
  }

  public function on($e, $cb) {
    if(isset($this->events[$e])) {
      $this->events[$e][] = $cb;
    } else {
      $this->events[$e] = [$cb];
    }

    return $this;
  }

  public function emit($e, $payload) {
    if(isset($this->events[$e])) {
      $handlers = $this->events[$e];
      $count = count($handlers);
      for($i = 0; $i < $count; ++$i) {
        $funk = $handlers[$i];
        $funk($payload);
      }
    }
    return $this;
  }

}