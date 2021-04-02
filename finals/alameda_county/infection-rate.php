<?php

require_once('../../database.php');

// cases, deaths, ifr, dates

$data = $database->query("SELECT `ac_cumul_cases`, `ac_cumul_deaths`, `date` FROM `alameda_county` ORDER BY `date` ASC");
while( $row = $data->fetch_assoc() ) {
    $cases[] = $row['ac_cumul_cases'];
    $deaths[] = $row['ac_cumul_deaths'];
    $dates[] = $row['date'];
    $ifr[] = round( ($row['ac_cumul_deaths'] / $row['ac_cumul_cases']) * 100, 2 );
}

$cases = implode(',', $cases);
$deaths = implode(',', $deaths);
$dates = implode("','", $dates);
$ifr = implode(',', $ifr);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>

        .apexcharts-xaxis-label {
            font-weight: normal!important;
        }

      body { margin: 0; }

    </style>
</head>
<body>

    <div id="chart"></div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.20.2/dist/apexcharts.min.js"></script>
    <script>

        <?php require_once('../global.php'); ?>

        var options = {
            // colors: ['#800000', '#e7e7e7'],
            chart: {
                type: 'line',
                height: 300,
            },
            series: [{
                name: 'Cumulative Cases',
                data: [<?php echo $cases; ?>]
            }, {
                name: 'Cumulative Deaths',
                data: [<?php echo $deaths; ?>]
            }, {
                name: 'Case Fatality Rate',
                data: [<?php echo $ifr; ?>]
            }],
            labels: ['<?php echo $dates; ?>'],
            xaxis: {
                tooltip: { enabled: false },
                type: 'datetime',
                datetimeUTC: false,
                labels: {
                    format: 'MMMM d'
                }
            },
            yaxis: [
                {
                    show: false,
                    seriesName: 'Cumulative Deaths'
                },
                {
                    labels: {
                        formatter: value => {
                            return value.toLocaleString();
                        }
                    }
                }
            ],
            tooltip: {
                x: { format: 'MMMM d, yyyy' },
                y: {
                    formatter: (value, series) => {
                        if( series.seriesIndex == 2 ) {
                            return value + '%';
                        }
                        return value;
                    }
                }    
            },
            legend: { show: false }
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();


    </script>
</body>
</html>