<?php

require_once('./lib/FileSystem.php');
require_once('./lib/Document.php');
require_once('./lib/Tokenizer.php');
require_once('./lib/Indexer.php');
require_once('./lib/TFIDF.php');
require_once('./lib/Settings.php');

$fs = new FileSystem();
$tokenizer = new Tokenizer();
$tfIndex = new Indexer();
$idfIndex = new Indexer();
$tfidf = new TFIDF();
$settings = new Settings();

$kterms = $settings->get('k-terms');
$dir = $settings->get('input-dir', './data/reuters21578');
$outputDir = $settings->get('output-dir', './output');

$stopWordList = './data/stop';
$stopDoc = new Document($stopWordList, 'r');

$stop = [];
while(!$stopDoc->finished()) {
  $stop[strtolower(trim($stopDoc->read()))] = true;
}

$files = $fs->listFiles($dir, 'sgm');

$document = null;

// On term event handler that will capture every term produce by the tokenizer and index it
$tokenizer->on('term', function($term) use (&$tfIndex, &$idfIndex, &$document, &$stop) {

  //check the stop words list.
  if(!isset($stop) || !isset($stop[strtolower(trim($term))])) {
    $tfIndex->index($document->name(), $term);
    $idfIndex->index($term, $document->name());
  }
});

foreach($files as $file) {
  $document = new Document($file, 'r');
  while(!$document->finished()) {
    $tokenizer->tokenize($document->readSafe());
  }
}

// Setup the TFIDF Algorithm
$tfidf->setDocumentIndex($idfIndex->getIndex());
$tfidf->setTermIndex($tfIndex->getIndex());

// Output document location.
// This will write the transactions to the output document.
$transList = new Document($outputDir . DIRECTORY_SEPARATOR . 'transactions.txt', 'w');

foreach($tfIndex->getIndex() as $doc => $terms) {

  $items = [];

  foreach($terms as $term => $count) {
    // get the term counts for each term and write them out to a file
    // as a transaction
    $items[$term] = $tfidf->process($term, $doc);;
  }

  // sort the terms
  arsort($items);

  // Document that will contain the term weights per document.
  $tfIdfOutDoc = new Document($outputDir . DIRECTORY_SEPARATOR . 'tf-idf_' . $doc , 'w');

  // Write the document title.
  $transList->write($doc . ',');

  $count = 0;
  $k_terms = [];

  foreach($items as $term => $score) {

    // Write the term and value to the file.
    $tfIdfOutDoc->writeLine($term . ' : ' . $score);

    // Write the term into the transaction.
    if(!is_null($kterms) && $kterms !== $count) {
      //$k_terms[] = $term;
      $transList->write($term . ',');
      $count++;
    } else {
      //$k_terms[] = $term;
      $transList->write($term . ',');
    }
  }

  // Write the transactions base on the k value.
  $transList->writeLine('');
}
