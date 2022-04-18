<?php
$file = "csv_files/products.csv";
$files = fopen($file, 'r');
$products = fgetcsv($files, 1000, ",");
while (($data = fgetcsv($files, 1000, ",")) !== FALSE)
{

  echo '<pre>';
  print_r($data);
}

// print_r(fgetcsv($files, 1000, ","));

