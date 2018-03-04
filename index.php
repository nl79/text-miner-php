<?php

require_once('./lib/FileSystem.php');
require_once('./lib/Document.php');
require_once('./lib/Tokenizer.php');
require_once('./lib/Indexer.php');
require_once('./lib/TFIDF.php');

require_once('./lib/Apriori/Apriori.php');


// Configuration for the Apriori object.
$shortopts = "s:c:k:";
$longopts = [
  'supp:',
  'conf:',
  'k-terms:'
];
$options = getopt($shortopts, $longopts);
// Default Values
$confidence = .5;
$support = .5;
$kterms = 5;

if(isset($options['supp'])) {
  $support = $options['supp'];
}

if(isset($options['conf'])) {
  $confidence = $options['conf'];
}

if(isset($options['k-terms'])) {
  $kterms = $options['k-terms'];
}

$config = [
  'confidence' => $confidence,
  'support' => $support
];

$fs = new FileSystem();
$tokenizer = new Tokenizer();
$tfIndex = new Indexer();
$idfIndex = new Indexer();

// Transactions index to store document => [terms];
$transactions = new Indexer();

$tfidf = new TFIDF();

// Apriori
$miner = new Apriori($config);

//$files = $fs->listFiles($dir, 'sgm');

//print_r($files);
$dir = './data/samples';
$files = $fs->listFiles($dir, 'txt');
//$dir = './data/reuters21578';
//$files = $fs->listFiles($dir, 'sgm');


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

    print("Document: $doc | Term: $term | score: " . $tfidf->process($term, $doc) . "\n");
    $transactions->index($doc, $term, $tfidf->process($term, $doc));
  }
}

//Write the transactions to a file.

//print_r($transactions->getIndex());

$data = [];
foreach($transactions->getIndex() as $trans) {
  //sort the values and extract the top k terms.
  arsort($trans);

  //slice the array upto k terms.
  $temp = array_slice($trans, 0);
  //print_r($temp);
  $data[] = array_keys($temp);
}

print_r($data);

//$results = $miner->process($data)->result()->toString();
//
//var_dump($results);

