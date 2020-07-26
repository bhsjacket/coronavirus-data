<?php

require_once('../database.php');

$endpoint = 'https://data.ca.gov/api/3/action/datastore_search_sql?sql=';

$sql = 'SELECT "todays_date", "county", "icu_covid_confirmed_patients", "icu_suspected_covid_patients", "hospitalized_covid_confirmed_patients", "hospitalized_suspected_covid_patients" FROM "42d33765-20fd-44b8-a978-b083b7542225" WHERE "todays_date" = (SELECT "todays_date" FROM "42d33765-20fd-44b8-a978-b083b7542225" ORDER BY "_id" DESC LIMIT 1)';
$sql = urlencode($sql);

$data = file_get_contents($endpoint . $sql);
$data = json_decode($data, true);
$data = $data['result']['records'];

$statement = $database->prepare("INSERT INTO hospital (date, county, patients, icu) VALUES (?, ?, ?, ?)");
$statement->bind_param('ssii', $date, $county, $patients, $icu);
foreach($data as $item) {
    $date = (new DateTime($item['todays_date']))->format('Y-m-d');
    $county = $item['county'];
    $patients = $item['hospitalized_covid_confirmed_patients'] + $item['hospitalized_suspected_covid_patients'];
    $icu = $item['icu_covid_confirmed_patients'] + $item['icu_suspected_covid_patients'];

    $statement->execute();
}
$statement->close();