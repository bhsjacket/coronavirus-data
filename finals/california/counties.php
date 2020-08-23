<?php

require_once('../../database.php');

// alameda, contra costa, marin, napa, san francisco, san mateo, santa clara, solano, sonoma
// according to the census (http://www.bayareacensus.ca.gov/counties/counties.htm)

$data = $database->query("SELECT `county`, `deaths`, `date` FROM `california` WHERE `county` IN ('Alameda', 'Contra Costa', 'Marin', 'Napa', 'San Francisco', 'San Mateo', 'Santa Clara', 'Solano', 'Sonoma')");

$population = $database->query("SELECT * FROM `population` WHERE `county` IN ('Alameda', 'Contra Costa', 'Marin', 'Napa', 'San Francisco', 'San Mateo', 'Santa Clara', 'Solano', 'Sonoma')");
$population = $population->fetch_all(MYSQLI_ASSOC);

while( $row = $data->fetch_assoc() ) {

    foreach( $population as $county ) {
        if( $county['county'] == $row['county'] ) {
            $countyPopulation = $county['population'] / 100000;
        }
    }

    switch( $row['county'] ):
        case('Alameda'):
            $alameda[] = $row['deaths'] / $countyPopulation;
        break;
        case('Contra Costa'):
            $contraCosta[] = $row['deaths'] / $countyPopulation;
        break;
        case('Marin'):
            $marin[] = $row['deaths'] / $countyPopulation;
        break;
        case('Napa'):
            $napa[] = $row['deaths'] / $countyPopulation;
        break;
        case('San Francisco'):
            $sanFrancisco[] = $row['deaths'] / $countyPopulation;
        break;
        case('San Mateo'):
            $sanMateo[] = $row['deaths'] / $countyPopulation;
        break;
        case('Santa Clara'):
            $santaClara[] = $row['deaths'] / $countyPopulation;
        break;
        case('Solano'):
            $solano[] = $row['deaths'] / $countyPopulation;
        break;
        case('Sonoma'):
            $sonoma[] = $row['deaths'] / $countyPopulation;
        break;
    endswitch;

    $dates[] = $row['date'];
}

$alameda = implode(',', $alameda);
$contraCosta = implode(',', $contraCosta);
$marin = implode(',', $marin);
$napa = implode(',', $napa);
$sanFrancisco = implode(',', $sanFrancisco);
$sanMateo = implode(',', $sanMateo);
$santaClara = implode(',', $santaClara);
$solano = implode(',', $solano);
$sonoma = implode(',', $sonoma);

$dates = array_unique($dates);
$dates = implode("','", $dates);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bay Area Deaths/100k</title>
    <style>

        .apexcharts-xaxis-label {
            font-weight: normal!important;
        }

        .apexcharts-legend-text {
            line-height: 0!important;
            margin-left: 2px!important;
        }

    </style>
</head>
<body>
    <div id="chart"></div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="http://bhsjacket.local/coronavirus/coronavirus-data/color-generator.js"></script>
    <script>

        <?php require_once('../global.php'); ?>

        var options = {
            chart: {
                type: 'line',
                height: 400,
            },
            series: [
                {
                    name: 'Napa',
                    data: [<?php echo $napa; ?>],
                },
                {
                    name: 'Sonoma',
                    data: [<?php echo $sonoma; ?>],
                },
                {
                    name: 'Solano',
                    data: [<?php echo $solano; ?>],
                },
                {
                    name: 'Marin',
                    data: [<?php echo $marin; ?>],
                },
                {
                    name: 'San Mateo',
                    data: [<?php echo $sanMateo; ?>],
                },
                {
                    name: 'San Francisco',
                    data: [<?php echo $sanFrancisco; ?>],
                },
                {
                    name: 'Contra Costa',
                    data: [<?php echo $contraCosta; ?>],
                },
                {
                    name: 'Santa Clara',
                    data: [<?php echo $santaClara; ?>],
                },
                {
                    name: 'Alameda',
                    data: [<?php echo $alameda; ?>],
                },
            ],
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
            yaxis: {
                labels: {
                    formatter: value => {
                        return value.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'right'
            },
            tooltip: {
                shared: false,
                x: { format: 'MMMM d, yyyy' },
                y: {
                    formatter: value => {
                        return Math.round( value ) + ' deaths per 100k';
                    }
                }
            }
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();

    </script>
</body>
</html>