<?php

require_once('./lib/FileSystem.php');
require_once('./lib/Document.php');
require_once('./lib/Tokenizer.php');

$dir = './data/reuters21578';

$fs = new FileSystem();
$tokenizer = new Tokenizer();


$files = $fs->listFiles($dir, 'sgm');

//print_r($files);

$testfile = $files[0];

print($testfile);
$document = new Document($testfile, 'r');

while(!$document->finished()) {
  print_r($tokenizer->tokenize($document->readSafe()));
}

