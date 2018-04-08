<?php

require_once('./lib/Apriori/Apriori.php');
require_once('./lib/Settings.php');

$settings = new Settings();
// Default Values
$confidence = $settings->get('conf', .2);
$support = $settings->get('supp', .3);
$kterms = $settings->get('k-terms', 2500);
$fileList = $settings->get('files');

echo("------Executing Job With the following parameters:\n");
echo("Confidence: $confidence \n");
echo("Support: $support \n");
echo("File List: $fileList \n");
echo("K Terms to analyze: " . (is_null( $kterms) ? ' All' :  $kterms) . "\n");

// Split the file list into an array of paths.
$fileList = !is_null($fileList) ? explode(',', $fileList) : null;

// Validate files were supplied.
if(is_null($fileList) || count($fileList) < 1) {
  echo("Filepaths invalid or missing \n");
  exit;
}

$config = [
  'confidence' => $confidence,
  'support' => $support
];

// Instantiate the object.
$miner;
$result = [];

// Array of input files
//$files = [
//  './output/transactions.txt'
//];
$files = $fileList;

foreach($files as $file) {
  $fh = fopen($file,'r');
  $data = [];
  $miner = new Apriori($config);

  /*
   * Pre-process the input and transform it into a simple array.
   */
  while ($line = fgets($fh)) {
    // Split line on space
    $parts = explode(',', $line);

    // If k-terms options are supplied, only process the k terms
    if(!is_null($kterms) && is_numeric($kterms)) {
      $parts = array_slice($parts, 0, $kterms);
    }

    // Remove the transaction ID value (first column)
    // trim surrounding whitespace
    // lowercase the tokens.
    $data[] = array_map(function($o) {
      return trim(strtolower($o));
    }, array_slice($parts, 1));
  }

  /*
   * Execute the Apriori algorithm.
   */
  $result[$file] = $miner->process($data)->result()->toString();
}


print("SUPPORT: $support\n");
print("CONFIDENCE: $confidence\n\n");
foreach($result as $key => $list) {
  print("--------------------Input File: $key--------------------\n");
  if(!empty($list)) {
    print($list);
  } else {
    print("\nNo Results Found\n\n");
  }
}
