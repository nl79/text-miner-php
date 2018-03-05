<?php

require_once('./lib/Apriori/Apriori.php');
require_once('./lib/Settings.php');

$settings = new Settings();
// Default Values
$confidence = $settings->get('conf', .2);
$support = $settings->get('supp', .3);
$kterms = $settings->get('k-terms', 2500);

$config = [
  'confidence' => $confidence,
  'support' => $support
];

// Instantiate the object.
$miner;
$result = [];

// Array of input files
$files = [
  './output/transactions.txt'
];

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
