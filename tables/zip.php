<?php

// DATABASE SAMPLE: https://i.imgur.com/IEKjor1.png

require_once('../database.php');

// UPDATE TESTING

$endpoint = 'https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_testing_zips/FeatureServer/0/query?where=1%3D1&outFields=Zip,Tests,Positives,Population,Tests_per_1000,Perc_Positive_Sort&outSR=4326&f=json';

$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];

$statement = $database->prepare("UPDATE zip SET tests = ?, positive = ?, percent_positive = ?, tests_per_mille = ? WHERE zip = ?");
$statement->bind_param('iiddi', $tests, $positive, $percent_positive, $tests_per_mille, $zip); // Bind prepared statement
foreach($data as &$item) {
    $item = $item['attributes']; // Remove unnecessary array wrapper

    $tests = $item['Tests'];
    $positive = $item['Positives'];
    $percent_positive = $item['Perc_Positive_Sort'];
    $tests_per_mille = $item['Tests_per_1000'];
    $zip = $item['Zip'];
    
    $statement->execute();
}
$statement->close();

// UPDATE CASES

$endpoint = 'https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_Rates_Zip_Code/FeatureServer/0/query?where=1%3D1&outFields=Zip,USPS_City,Count&outSR=4326&f=json';

$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];

$statement = $database->prepare("UPDATE zip SET cases = ? WHERE zip = ?");
$statement->bind_param('ii', $cases, $zip); // Bind prepared statement
foreach($data as &$item) {
    $item = $item['attributes']; // Remove unnecessary array wrapper

    $cases = (int)$item['Count'];
    $zip = $item['Zip'];
    
    $statement->execute();
}
$statement->close();