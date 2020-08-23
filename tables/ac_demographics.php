<?php

require_once('../database.php');

$endpoint = 'https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_cases/FeatureServer/0/query?where=1%3D1&outFields=Geography,Female,Male,Unknown_Sex,Hispanic_Latino,Asian,African_American_Black,White,Pacific_Islander,Native_American,Multirace,Known_Race,Other_Race,Unknown_Race,Age_LT18,Age_18_30,Age_31_40,Age_41_50,Age_51_60,Age_61_70,Age_71_80,Age_81_Up,Unknown_Age&outSR=4326&f=json';
$data = file_get_contents($endpoint);
$data = json_decode($data, true);
$data = $data['features'];

$statement = $database->prepare("UPDATE ac_demographics SET female = ?, male = ?, unknown_sex = ?, latinx = ?, asian = ?, black = ?, white = ?, pacific_islander = ?, native_american = ?, multirace = ?, other_race = ?, unknown_race = ?, under_18 = ?, 18_to_30 = ?, 31_to_40 = ?, 41_to_50 = ?, 51_to_60 = ?, 61_to_70 = ?, 71_to_80 = ?, over_81 = ?, unknown_age = ? WHERE type = ?");
$statement->bind_param('ddddddddddddddddddddds', $female, $male, $unknown_sex, $latinx, $asian, $black, $white, $pacific_islander, $native_american, $multirace, $other_race, $unknown_race, $under_18, $from_18_to_30, $from_31_to_40, $from_41_to_50, $from_51_to_60, $from_61_to_70, $from_71_to_80, $over_81, $unknown_age, $type);
foreach($data as $item) {
    $item = $item['attributes'];

    $female = $item['Female'];
    $male = $item['Male'];
    $unknown_sex = $item['Unknown_Sex'];
    $latinx = $item['Hispanic_Latino'];
    $asian = $item['Asian'];
    $black = $item['African_American_Black'];
    $white = $item['White'];
    $pacific_islander = $item['Pacific_Islander'];
    $native_american = $item['Native_American'];
    $multirace = $item['Multirace'];
    $other_race = $item['Other_Race'];
    $unknown_race = $item['Unknown_Race'];
    $under_18 = $item['Age_LT18'];
    $from_18_to_30 = $item['Age_18_30'];
    $from_31_to_40 = $item['Age_31_40'];
    $from_41_to_50 = $item['Age_41_50'];
    $from_51_to_60 = $item['Age_51_60'];
    $from_61_to_70 = $item['Age_61_70'];
    $from_71_to_80 = $item['Age_71_80'];
    $over_81 = $item['Age_81_Up'];
    $unknown_age = $item['Unknown_Age'];

    switch($item['Geography']):
        case('Alameda County'):
            $type = 'cases';
        break;
        case('Population'):
            $type = 'population';
        break;
        case('Case Rate'):
            $type = 'rate';
        break;
    endswitch;

    $statement->execute();
}
$statement->close();
