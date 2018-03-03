<?php

require_once('./lib/FileSystem.php');
require_once('./lib/Document.php');
require_once('./lib/Tokenizer.php');
require_once('./lib/Indexer.php');
require_once('./lib/TFIDF.php');

//$dir = './data/reuters21578';

$fs = new FileSystem();
$tokenizer = new Tokenizer();
$tfIndex = new Indexer();
$idfIndex = new Indexer();

// Transactions index to store document => [terms];
$transactions = new Indexer();

$tfidf = new TFIDF();

//$files = $fs->listFiles($dir, 'sgm');

//print_r($files);
$dir = './data/samples';
$files = $fs->listFiles($dir, 'txt');

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

foreach($tfIndex->getIndex() as $doc => $terms) {

  foreach($terms as $term => $count) {

   // print("Document: $doc | Term: $term | score: " . $tfidf->process($term, $doc) . "\n");
    $transactions->index($doc, $term, $tfidf->process($term, $doc));
  }
}

print_r($transactions->getIndex());

