<?php

require_once('../database.php');

$endpoint = 'https://data.ca.gov/api/3/action/datastore_search_sql?sql=';

$latestData = $database->query("SELECT `date` FROM `hospital` ORDER BY `date` DESC LIMIT 1");
$latestData = $latestData->fetch_assoc();
$latestData = $latestData['date'];
$latestData = new DateTime($latestData);

// Dataset: https://data.ca.gov/dataset/covid-19-hospital-data/resource/42d33765-20fd-44b8-a978-b083b7542225
$sql = "SELECT \"todays_date\", \"county\", \"icu_covid_confirmed_patients\", \"icu_suspected_covid_patients\", \"hospitalized_covid_confirmed_patients\", \"hospitalized_suspected_covid_patients\" FROM \"42d33765-20fd-44b8-a978-b083b7542225\" WHERE \"todays_date\" > '{$latestData->format('Y-m-d')}'";
$sql = urlencode($sql);

$data = file_get_contents($endpoint . $sql);
$data = json_decode($data, true);
$data = $data['result']['records'];

if( !$data ) {
    die( 'The data is up to date. There is no data later than ' . $latestData->format('F j, Y') . '.' );
}

$statement = $database->prepare("INSERT INTO hospital (date, county, patients, icu) VALUES (?, ?, ?, ?)");
$statement->bind_param('ssii', $date, $county, $patients, $icu);
foreach($data as $item) {
    $dateObject = new DateTime($item['todays_date']);
    $date = $dateObject->format('Y-m-d');
    $county = $item['county'];
    $patients = $item['hospitalized_covid_confirmed_patients'] + $item['hospitalized_suspected_covid_patients'];
    $icu = $item['icu_covid_confirmed_patients'] + $item['icu_suspected_covid_patients'];

    if( $dateObject > $latestData ) {
        $statement->execute();
        echo '<strong>Data added for ' . $dateObject->format('F j, Y') . '</strong><br>';
    } else {
        echo 'Data skipped for ' . $dateObject->format('F j, Y') . '<br>';
    }
}
$statement->close();