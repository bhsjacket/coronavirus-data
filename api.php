<?php

require_once('database.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");


// Select Alameda County & Berkeley related data
$query = "SELECT `date`, `berkeley_new_cases`, `berkeley_cumul_cases`, `ac_new_cases`, `ac_cumul_cases` FROM `alameda_county` ORDER BY `date` ASC";
$data = $database->query($query);
$rows = [];
while ( $row = mysqli_fetch_assoc($data) ) {
    $rows[] = $row;
}
$data = $rows;

// Format data for output
foreach($data as $item) {
    $berkeleyCumulativeCases[] = (int)$item['berkeley_cumul_cases'];
}

$date = new DateTime( array_reverse($data)[0]['date'] );
$date = $date->format('M j');

$data = [
    'date' => $date,
    'berkeley' => [
        'total' => (int)array_reverse($data)[0]['berkeley_cumul_cases'],
        'new' => (int)array_reverse($data)[0]['berkeley_new_cases'],
        'cumulative' => $berkeleyCumulativeCases
    ],
    'alameda' => [
        'total' => (int)array_reverse($data)[0]['ac_cumul_cases'],
        'new' => (int)array_reverse($data)[0]['ac_new_cases']
    ]
];

$data = json_encode($data);
print_r( $data );