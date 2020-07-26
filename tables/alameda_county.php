<?php

require_once('../database.php');

$endpoint = 'https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_dates2/FeatureServer/0/query?where=1%3D1&outFields=Date,BkLHJ_NewCases,BkLHJ_CumulCases,BkLHJ_NewDeaths,BkLHJ_CumulDeaths,AC_Cases,AC_CumulCases,AC_Deaths,AC_CumulDeaths&orderByFields=date%20DESC&outSR=4326&f=json';
$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];

$statement = $database->prepare("INSERT INTO alameda_county (date, berkeley_new_cases, berkeley_cumul_cases, berkeley_new_deaths, berkeley_cumul_deaths, ac_new_cases, ac_cumul_cases, ac_new_deaths, ac_cumul_deaths) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$statement->bind_param('siiiiiiii', $date, $berkeley_new_cases, $berkeley_cumul_cases, $berkeley_new_deaths, $berkeley_cumul_deaths, $ac_new_cases, $ac_cumul_cases, $ac_new_deaths, $ac_cumul_deaths);
foreach($data as $item) {
    $item = $item['attributes'];

    $date = $item['Date'] / 1000;
    $date = date('Y-m-d', $date);

    $berkeley_new_cases = $item['BkLHJ_NewCases'];
    $berkeley_cumul_cases = $item['BkLHJ_CumulCases'];
    $berkeley_new_deaths = $item['BkLHJ_NewDeaths'];
    $berkeley_cumul_deaths = $item['BkLHJ_CumulDeaths'];
    $ac_new_cases = $item['AC_Cases'];
    $ac_cumul_cases = $item['AC_CumulCases'];
    $ac_new_deaths = $item['AC_Deaths'];
    $ac_cumul_deaths = $item['AC_CumulDeaths'];

    $statement->execute();
}
$statement->close();