<?php

require_once('../database.php');

// Dataset: https://data.acgov.org/datasets/AC-HCSA::alameda-county-covid-19-data-by-date/data
$endpoint = 'https://services5.arcgis.com/ROBnTHSNjoZ2Wm1P/arcgis/rest/services/COVID_19_Statistics/FeatureServer/4/query?where=1%3D1&outFields=dtcreate,Berkeley_Berkeley_LHJ,Berkeley_Berkeley_LHJ_Cumulativ,Berkeley_Berkeley_LHJ_Deaths,Berkeley_Berkeley_LHJ_Deaths_Cu,Alameda_County,Alameda_County__Cumulative,Alameda_County_Deaths,Alameda_County_Deaths__Cumulati&orderByFields=dtcreate%20DESC&outSR=4326&f=json';
$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];

// var_dump($data); die;

$latestData = $database->query("SELECT `date` FROM `alameda_county` ORDER BY `date` DESC LIMIT 1");
$latestData = $latestData->fetch_assoc();
$latestData = $latestData['date'];
$latestData = new DateTime($latestData);

$statement = $database->prepare("INSERT INTO alameda_county (date, berkeley_new_cases, berkeley_cumul_cases, berkeley_new_deaths, berkeley_cumul_deaths, ac_new_cases, ac_cumul_cases, ac_new_deaths, ac_cumul_deaths) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$statement->bind_param('siiiiiiii', $date, $berkeley_new_cases, $berkeley_cumul_cases, $berkeley_new_deaths, $berkeley_cumul_deaths, $ac_new_cases, $ac_cumul_cases, $ac_new_deaths, $ac_cumul_deaths);

foreach($data as $item) {
    $item = $item['attributes'];

    $date = $item['dtcreate'] / 1000;
    $date = date('Y-m-d', $date);
    $dateObject = new DateTime($date);

    $berkeley_new_cases = $item['Berkeley_Berkeley_LHJ'];
    $berkeley_cumul_cases = $item['Berkeley_Berkeley_LHJ_Cumulativ'];
    $berkeley_new_deaths = $item['Berkeley_Berkeley_LHJ_Deaths'];
    $berkeley_cumul_deaths = $item['Berkeley_Berkeley_LHJ_Deaths_Cu'];
    $ac_new_cases = $item['Alameda_County'];
    $ac_cumul_cases = $item['Alameda_County__Cumulative'];
    $ac_new_deaths = $item['Alameda_County_Deaths'];
    $ac_cumul_deaths = $item['Alameda_County_Deaths__Cumulati'];

    if( $dateObject > $latestData ) {
        if( $dateObject < new DateTime() ) {
            $statement->execute();
            echo '<strong>Data added for ' . $dateObject->format('F j, Y') . '</strong><br>';
        } else {
            echo '<em>Data skipped for ' . $dateObject->format('F j, Y') . ' because it\'s in the future...somehow.</em><br>';
        }
    } else {
        echo 'Data skipped for ' . $dateObject->format('F j, Y') . '<br>';
    }

}
$statement->close();

/*

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

*/