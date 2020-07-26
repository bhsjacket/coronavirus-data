<?php

require_once('../database.php');

$endpoint = 'https://data.ca.gov/api/3/action/datastore_search_sql?sql=';

$sql = 'SELECT "totalcountconfirmed", "newcountdeaths", "totalcountdeaths", "county", "newcountconfirmed", "date" FROM "926fd08f-cc91-4828-af38-bd45de97f8c3" WHERE "date" = (SELECT "date" FROM "926fd08f-cc91-4828-af38-bd45de97f8c3" ORDER BY "_id" DESC LIMIT 1)';
$sql = urlencode($sql);

$data = file_get_contents($endpoint . $sql);
$data = json_decode($data, true);
$data = $data['result']['records'];

$statement = $database->prepare("INSERT INTO california (date, county, cases, deaths, new_cases, new_deaths) VALUES (?, ?, ?, ?, ?, ?)");
$statement->bind_param('ssiiii', $date, $county, $cases, $deaths, $new_cases, $new_deaths);
foreach($data as $item) {
    $date = (new DateTime($item['date']))->format('Y-m-d');
    $county = $item['county'];
    $cases = (int)$item['totalcountconfirmed'];
    $deaths = (int)$item['totalcountdeaths'];
    $new_cases = (int)$item['newcountconfirmed'];
    $new_deaths = (int)$item['newcountdeaths'];

    $statement->execute();
}
$statement->close();