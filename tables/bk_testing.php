<?php

require_once('../database.php');

$latestData = $database->query("SELECT `date` FROM `bk_testing` ORDER BY `date` DESC LIMIT 1");
$latestData = $latestData->fetch_assoc();
$latestData = $latestData['date'];
$latestData = new DateTime($latestData);

$dailyEndpoint = 'https://data.cityofberkeley.info/resource/dnau-e757.json';
$weeklyEndpoint = 'https://data.cityofberkeley.info/resource/mc9x-5kpz.json';

$dailyData = file_get_contents($dailyEndpoint);
$dailyData = json_decode($dailyData, true);

$weeklyData = file_get_contents($weeklyEndpoint);
$weeklyData = json_decode($weeklyData, true);

$statement = $database->prepare("INSERT INTO bk_testing (`date`, `daily_tests`, `cumulative_tests`, `weekly_positive_tests`, `weekly_percent_positive`) VALUES (?, ?, ?, ?, ?)");
$statement->bind_param('siiid', $date, $daily_tests, $cumulative_tests, $weekly_positive_tests, $weekly_percent_positive);

function fixDateString($input) {
    return str_replace("\xc2\xa0", ' ', $input);
}

foreach($weeklyData as $wdata) {
    @$startDate = new DateTime( fixDateString($wdata['weekstartdate']) );
    @$endDate = new DateTime( fixDateString($wdata['weekenddate']) );
    $weekly_positive_tests = $wdata['weeklypositives'];
    $weekly_percent_positive = $wdata['percentpositive'];

    foreach($dailyData as $ddata) {
        @$dateObject = new DateTime( $ddata['dtcount'] );
        $date = $dateObject->format('Y-m-d');
        $daily_tests = $ddata['dailytests'];
        $cumulative_tests = $ddata['cumultests'];

        if( $dateObject >= $startDate && $dateObject <= $endDate ) {
            if( $dateObject > $latestData ) {
                echo '<strong>Data added for ' . $dateObject->format('F j, Y') . '</strong><br>';
                $statement->execute();
            } else {
                echo 'Data skipped for ' . $dateObject->format('F j, Y') . '<br>';
            }
        }
    }
}