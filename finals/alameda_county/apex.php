<?php

require_once('../../database.php');

$value = 'ac_new_cases';

$data = $database->query("SELECT {$value}, date FROM alameda_county ORDER BY date ASC");
while($row = $data->fetch_assoc()) {
    $label = new DateTime($row['date']);
    $labels[] = $label->format('c');
    $cases[] = $row[$value];
}

$data = $database->query("SELECT date, {$value}, AVG({$value}) OVER (ORDER BY date ASC ROWS 6 PRECEDING) AS average FROM alameda_county");
while($row = $data->fetch_assoc()) {
    $average[] = round($row['average']);
}

$currentAverage = array_reverse($average)[0];

// rate = (cases / demographic) * 100000

$labels = implode("','", $labels);
$cases = implode(",", $cases);
$average = implode(",", $average);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alameda County Statistics</title>

    <link rel="stylesheet" href="//bhsjacket.local/coronavirus/coronavirus-data/tooltip.css">

    <style>

        .apexcharts-xaxis-label {
            font-weight: normal!important;
        }

    </style>
</head>
<body>
    <div id="chart" style="max-width:800px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>

        <?php require_once('../global.php'); ?>

        var options = {
            colors: ['#800000', '#e7e7e7'],
            chart: {
                type: 'line',
                height: 300,
            },
            series: [{
                name: 'Rolling 7-Day Average',
                data: [<?php echo $average; ?>],
            }, {
                name: 'New Daily Cases',
                data: [<?php echo $cases; ?>],
                type: 'bar',
            }],
            labels: ['<?php echo $labels; ?>'],
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
            annotations: {
                yaxis: [{
                    y: <?php echo $currentAverage ?>,
                    strokeDashArray: 5,
                    borderColor: '#808080',
                    label: {
                        text: 'Current rolling 7-day average',
                        position: 'left',
                        textAnchor: 'start',
                        borderColor: false,
                        style: {
                            background: 'transparent',
                            fontSize: '13px'
                        }
                    }
                }]
            },
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();


    </script>
</body>
</html>