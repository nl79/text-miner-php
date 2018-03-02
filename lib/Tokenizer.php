<?php

class Tokenizer
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
    $parts = explode(' ', $str);
    $parts = array_map(function($item) {
      $term = trim($item);
      $term = preg_replace('/[^a-zA-Z0-9]/i', '', $term);

      return $term;

    }, $parts);

    return $parts;
  }

  public function tokenize($str) {
    // check if custom tokenizers are supplied.
    return $this->defaultTokenizer($str);

  }
}