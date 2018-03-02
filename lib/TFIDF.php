f<?php

class TFIDF
{

  private $tfIndex = null;
  private $dfIndex = null;

  public function __construct()
  {

  }

  public function process($t, $d) {
    return $this->tf($t, $d) * $this->idf($t);
  }

  private function tf($t, $d) {
    // number of times a term appears in the document.
    $terms = isset($this->tfIndex[$d]) ? $this->tfIndex[$d] : [];

    return isset($terms[$t]) ? $terms[$t] : 0;
  }

  private function df($t) {
    // Number of documents containing term $t
    $documents  = isset($this->dfIndex[$t]) ? $this->dfIndex[$t] : [];

    return count($documents);

  }

  private function idf($t) {
    $N = count($this->tfIndex);
    $df = $this->df($t);

    $result = log($N / $df);
    //print("N: $N | df: $df | result: $result");
    return $result;
  }

  public function setTermIndex($value) {
    $this->tfIndex = $value;
  }

  public function setDocumentIndex($value) {
    $this->dfIndex = $value;
  }

}