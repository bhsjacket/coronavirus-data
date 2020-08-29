<?php

// (OUTDATED) DATABASE SAMPLE: https://i.imgur.com/IEKjor1.png

require_once('../database.php');

// UPDATE TESTING
// Dataset: https://data.acgov.org/datasets/5d6bf4760af64db48b6d053e7569a47b_1/data

$endpoint = 'https://services5.arcgis.com/ROBnTHSNjoZ2Wm1P/arcgis/rest/services/COVID_19_Statistics/FeatureServer/1/query?where=1%3D1&outFields=Zip_Number,Positives,NumberOfTests,PercentagePositiveTests,Population&returnGeometry=false&outSR=4326&f=json';

$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];
 
$statement = $database->prepare("UPDATE ac_zip SET tests = ?, positive = ?, percent_positive = ?, tests_per_mille = ? WHERE zip = ?");
$statement->bind_param('iiddi', $tests, $positive, $percent_positive, $tests_per_mille, $zip); // Bind prepared statement
foreach($data as &$item) {
    $item = $item['attributes']; // Remove unnecessary array wrapper

    $tests = $item['NumberOfTests'];
    $positive = $item['Positives'];
    $percent_positive = round( $item['PercentagePositiveTests'], 2 );
    $tests_per_mille = $item['NumberOfTests'] / $item['Population'] * 1000;
    $zip = $item['Zip_Number'];
    
    $statement->execute();
}
$statement->close();

// UPDATE CASES
// Dataset: https://data.acgov.org/datasets/5d6bf4760af64db48b6d053e7569a47b_0/data

$endpoint = 'https://services5.arcgis.com/ROBnTHSNjoZ2Wm1P/arcgis/rest/services/COVID_19_Statistics/FeatureServer/0/query?where=1%3D1&outFields=Zip_Number,Cases&returnGeometry=false&outSR=4326&f=json';

$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];

$statement = $database->prepare("UPDATE ac_zip SET cases = ? WHERE zip = ?");
$statement->bind_param('ii', $cases, $zip); // Bind prepared statement
foreach($data as &$item) {
    $item = $item['attributes']; // Remove unnecessary array wrapper

    $cases = (int)$item['Cases'];
    $zip = $item['Zip_Number'];
    
    $statement->execute();
}
$statement->close();