<?php

require_once('../database.php');

$endpoint = 'https://data.ca.gov/api/3/action/datastore_search_sql?sql=';

$latestData = $database->query("SELECT `date` FROM `california` ORDER BY `date` DESC LIMIT 1");
$latestData = $latestData->fetch_assoc();
$latestData = $latestData['date'];
$latestData = new DateTime($latestData);

// Dataset: https://data.ca.gov/dataset/covid-19-cases/resource/926fd08f-cc91-4828-af38-bd45de97f8c3
$sql = "SELECT \"totalcountconfirmed\", \"newcountdeaths\", \"totalcountdeaths\", \"county\", \"newcountconfirmed\", \"date\" FROM \"926fd08f-cc91-4828-af38-bd45de97f8c3\" WHERE \"date\" > '{$latestData->format('Y-m-d')}'";
$sql = urlencode($sql);

$data = file_get_contents($endpoint . $sql);
$data = json_decode($data, true);
$data = $data['result']['records'];

if( !$data ) {
    die( 'The data is up to date. There is no data later than ' . $latestData->format('F j, Y') . '.' );
}

$statement = $database->prepare("INSERT INTO california (date, county, cases, deaths, new_cases, new_deaths) VALUES (?, ?, ?, ?, ?, ?)");
$statement->bind_param('ssiiii', $date, $county, $cases, $deaths, $new_cases, $new_deaths);
foreach($data as $item) {
    $dateObject = new DateTime($item['date']);
    $date = $dateObject->format('Y-m-d');
    $county = $item['county'];
    $cases = (int)$item['totalcountconfirmed'];
    $deaths = (int)$item['totalcountdeaths'];
    $new_cases = (int)$item['newcountconfirmed'];
    $new_deaths = (int)$item['newcountdeaths'];

    if( $dateObject > $latestData ) {
        $statement->execute();
        echo '<strong>Data added for ' . $dateObject->format('F j, Y') . '</strong><br>';
    } else {
        echo 'Data skipped for ' . $dateObject->format('F j, Y') . '<br>';
    }
}
$statement->close();