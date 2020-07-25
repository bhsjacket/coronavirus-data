<?php

require_once('database.php'); // Opens database connection w/ MySQLi. Not included for privacy reasons.

const Endpoints = [
    'berkeley',
    'alameda' => [
        'testsByZip' => 'https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_testing_zips/FeatureServer/0/query?where=1%3D1&outFields=Zip,Tests,Positives,Population,Tests_per_1000,Perc_Positive_Sort&outSR=4326&f=json',
    ]
];