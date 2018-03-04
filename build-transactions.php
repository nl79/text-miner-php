<?php

require_once('./lib/FileSystem.php');
require_once('./lib/Document.php');
require_once('./lib/Tokenizer.php');
require_once('./lib/Indexer.php');
require_once('./lib/TFIDF.php');

$shortopts = "s:c:k:";
$longopts = [
  'supp:',
  'conf:',
  'k-terms:'
];
$options = getopt($shortopts, $longopts);
$kterms = null;

if(isset($options['k-terms'])) {
  $kterms = $options['k-terms'];
}

$fs = new FileSystem();
$tokenizer = new Tokenizer();
$tfIndex = new Indexer();
$idfIndex = new Indexer();
$tfidf = new TFIDF();

$outputDir = './output';

//$dir = './data/samples';
//$files = $fs->listFiles($dir, 'txt');
$dir = './data/reuters21578';
$files = $fs->listFiles($dir, 'sgm');

print_r($files);

$document = null;

// On term event handler that will capture every term produce by the tokenizer and index it
$tokenizer->on('term', function($term) use ($tfIndex, $idfIndex, &$document) {
  $tfIndex->index($document->name(), $term);
  $idfIndex->index($term, $document->name());
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

    // Write the term into the transaction.
    if(!is_null($kterms) && $kterms !== $count) {
      //$k_terms[] = $term;
      $transList->write($term . ',');
      $count++;
    } else {
      //$k_terms[] = $term;
      $transList->write($term . ',');
    }

    // Write the term and value to the file.
    $tfIdfOutDoc->writeLine($term . ' : ' . $score);
  }

  // Write the transactions base on the k value.
  //$trans = $doc . ',' . implode(',', $k_terms);
  //$transList->writeLine($trans);
  $transList->writeLine('');
}