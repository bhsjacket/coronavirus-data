<?php

require_once('../../database.php');

$data = $database->query("SELECT `daily_tests`, `weekly_percent_positive`, `date` FROM `bk_testing` ORDER BY `date` ASC");
while($row = $data->fetch_assoc()) {
    $label = new DateTime($row['date']);
    $labels[] = $label->format('c');
    $tests[] = $row['daily_tests'];
    $positive[] = $row['weekly_percent_positive'] * 100;
}

$labels = implode("','", $labels);
$tests = implode(",", $tests);
$positive = implode(",", $positive);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkeley Testing Statistics</title>
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
                type: 'line',
                height: 300,
            },
            series: [{
                name: 'Weekly Positivity Rate',
                data: [<?php echo $positive; ?>],
            }, {
                name: 'Daily Tests',
                data: [<?php echo $tests; ?>],
                type: 'bar',
            }],
            stroke: { curve: 'stepline' },
            labels: ['<?php echo $labels; ?>'],
            xaxis: {
                tooltip: { enabled: false },
                type: 'datetime',
                datetimeUTC: false,
                labels: {
                    format: 'MMMM d'
                }
            },
            yaxis: [{
                labels: {
                    formatter: value => {
                        if( value == Number.MIN_VALUE ) {
                            value = 0;
                        }
                        return value + '%';
                    }
                },
                max: 10
            }, {
                opposite: true,
                decimalsInFloat: 0
            }],
            annotations: {
                xaxis: [{
                    x: new Date('June 9, 2020').getTime(),
                    strokeDashArray: 5,
                    borderColor: '#808080',
                    label: {
                        text: "Free tests offered to all residents",
                        textAnchor: 'end',
                        offsetX: -10,
                        offsetY: 60,
                        orientation: 'horizontal',
                        borderColor: false,
                        style: {
                            background: 'transparent',
                            fontSize: '13px'
                        }
                    }
                }]
            },
            tooltip: { x: { format: 'MMMM d, yyyy' } },
            legend: { show: false }
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();


    </script>
</body>
</html>