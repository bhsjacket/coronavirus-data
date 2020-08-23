<?php

require_once('../../database.php');

// $data = $database->query('SELECT latinx, asian, black, white, pacific_islander, native_american, multirace FROM ac_demographics WHERE type = "rate"');
$data = $database->query('SELECT under_18, 18_to_30, 31_to_40, 41_to_50, 51_to_60, 61_to_70, 71_to_80, over_81 FROM ac_demographics WHERE type = "rate"');
$data = $data->fetch_assoc();

foreach($data as $label => $item) {
    $label = str_replace('_', ' ', $label);
    $label = ucwords($label);
    $label = str_replace('To', 'to', $label);
    $labels[] = $label;
    $rates[] = $item;
}

$labels = implode("','", $labels);
$rates = implode(",", $rates);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alameda County Demographics</title>
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
            colors: ['#800000', '#e7e7e7'],
            chart: {
                type: 'bar',
                height: 300,
            },
            series: [{
                name: 'Cases per 100k',
                data: [<?php echo $rates; ?>]
            }],
            labels: ['<?php echo $labels; ?>'],
            xaxis: { tooltip: { enabled: false } },
            yaxis: {
                decimalsInFloat: 0
            },
            legend: { show: false },
            dataLabels: { enabled: false },
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();


    </script>
</body>
</html>