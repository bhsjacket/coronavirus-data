<?php

require_once('../../database.php');

$data = $database->query("SELECT `patients`, `icu`, `date` FROM `hospital` WHERE `county` = 'Alameda' ORDER BY `date` DESC");
while( $row = $data->fetch_assoc() ) {
    $patients[] = $row['patients'];
    $icu[] = $row['icu'];
    $dates[] = $row['date'];
}

$patients = implode(',', $patients);
$icu = implode(',', $icu);
$dates = implode("','", $dates);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alameda County Statistics</title>
    <style>

        .apexcharts-xaxis-label {
            font-weight: normal!important;
        }

    </style>
</head>
<body>
    <div id="chart"></div>    

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>

        <?php require_once('../global.php'); ?>

        var options = {
            colors: ['#808080', '#800000'],
            area: {
                fillTo: 'end'
            },
            fill: {
                opacity: 0.25,
                type: 'solid'
            },
            chart: {
                type: 'area',
                height: 300,
            },
            series: [{
                name: 'Non-ICU Patients (confirmed & suspected)',
                data: [<?php echo $patients; ?>],
            }, {
                name: 'ICU Patients (confirmed & suspected)',
                data: [<?php echo $icu; ?>],
            }],
            labels: ['<?php echo $dates; ?>'],
            dataLabels: { enabled: false },
            xaxis: {
                tooltip: { enabled: false },
                type: 'datetime',
                datetimeUTC: false,
                labels: {
                    format: 'MMMM d'
                }
            },
            tooltip: { x: { format: 'MMMM d, yyyy' } },
            legend: { show: false },
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();


    </script>
</body>
</html>