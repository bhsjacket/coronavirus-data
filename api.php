<?php

require_once('database.php');

header('Content-Type: application/json');
$get = $_GET;
if( !$get ) { die('Error'); }

switch( $get['location'] ) {
    case 'alameda':
        switch( $get['data'] ) {
            case 'new_cases':
                $dataType = 'ac_new_cases';
            break;
            case 'total_cases':
                $dataType = 'ac_cumul_cases';
            break;
            case 'new_deaths':
                $dataType = 'ac_new_deaths';
            break;
            case 'total_deaths':
                $dataType = 'ac_cumul_deaths';
            break;
            default:
                die('Error');
            break;
        }
    break;
    case 'berkeley':
        switch( $get['data'] ) {
            case 'new_cases':
                $dataType = 'berkeley_new_cases';
            break;
            case 'total_cases':
                $dataType = 'berkeley_cumul_cases';
            break;
            case 'new_deaths':
                $dataType = 'berkeley_new_deaths';
            break;
            case 'total_deaths':
                $dataType = 'berkeley_cumul_deaths';
            break;
            default:
                die('Error');
            break;
        }
    break;
    default:
        die('Error');
    break;
}

$query = "SELECT `date`, `{$dataType}` AS '{$get['data']}' FROM `alameda_county` ORDER BY `date` DESC LIMIT 1";
$data = $database->query($query);
$data = $data->fetch_all(MYSQLI_ASSOC);
$data = json_encode($data);
echo $data;