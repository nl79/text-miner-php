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
$tfidf = new TFIDF();

//$files = $fs->listFiles($dir, 'sgm');

//print_r($files);
$dir = './data/samples';
$files = $fs->listFiles($dir, 'txt');

print_r($files);

$document = null;

// On term event handler that will capture every term produce by the tokenizer and index it
$tokenizer->on('term', function($term) use ($tfIndex, $idfIndex, &$document) {
  print("term: $term | document: ". $document->name() . "\n");

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

foreach($idfIndex->getIndex() as $term => $docs) {

  foreach($docs as $doc => $count) {
    print('result; ' . $tfidf->process($term, $doc) . "\n");
  }
}


//print_r($tfIndex->getIndex());
//print_r($idfIndex->getIndex());

