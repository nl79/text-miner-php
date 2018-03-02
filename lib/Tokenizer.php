<?php

require_once ('EventEmitter.php');


class Tokenizer extends EventEmitter
{
  private $parser = null;

  private $onTerm = null;

  public function __construct($parser = null)
  {


    $this->parser($parser);
  }

  private function parser($parser) {
    $parser = !is_array($parser) ? [$parser] : $parser;
  }

  private function defaultTokenizer($str) {

    $result = [];
    $parts = explode(' ', $str);

    $count = count($parts);

    for($i = 0; $i < $count; ++$i) {
      $item = $parts[$i];
      $term = trim($item);
      $term = preg_replace('/[^a-zA-Z0-9]/i', '', $term);

      if(empty($term)) {
        continue;
      }

      // Emit the term event.
      $this->emit('term', $term);

      $result[] = $term;
    }

    return $result;
  }

  public function tokenize($str) {
    // check if custom tokenizers are supplied.
    return $this->defaultTokenizer($str);

  }
}