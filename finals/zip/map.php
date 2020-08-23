<?php

require_once('../../database.php');

// HEATMAP:
// Cases per Capita
// Positivity Rate
// Total Cases

// TOOLTIP:
// ZIP Code
// City
// Cases Per 1,000
// Total Cases
// Total Tests
// Positivity Rate

function getColor($percentage) {

    $start = [128,0,0];
    $end = [231,231,231];

    foreach( $end as $index => $channel ) {
        $result[] = round( $channel + $percentage * ( $start[$index] - $channel ) );
    }
    return 'rgb(' . implode( ',', $result ) . ')';

}

$data = $database->query("SELECT `zip`, `city`, (`cases` / `population`) * 1000 AS 'cases_per_mille', `income`, `cases`, `tests`, `percent_positive`, `population` FROM `ac_zip`");
$data = $data->fetch_all(MYSQLI_ASSOC);

$svg = file_get_contents('zip.svg');
$dom = new DOMDocument();
@$dom->loadHTML($svg);
$paths = $dom->getElementsByTagName('path');

$maxPercentPositive = max( array_column( $data, 'percent_positive' ) );

foreach( $paths as $path ) {

    if( $zip = $path->getAttribute('data-zip') ) {
        foreach($data as $item) {
            if( $item['zip'] == $zip ) {
                $path->setAttribute( 'style', 'fill:' . getColor( $item['percent_positive'] / $maxPercentPositive ) );
                
                $path->setAttribute( 'data-city', $item['city'] );
                $path->setAttribute( 'data-percent_positive', $item['percent_positive'] );
                $path->setAttribute( 'data-cases_per_mille', $item['cases_per_mille'] );
                $path->setAttribute( 'data-cases', $item['cases'] );
                $path->setAttribute( 'data-tests', $item['tests'] );
                $path->setAttribute( 'data-income', $item['income'] );
                $path->setAttribute( 'data-population', $item['population'] );
            }
        }
    }

}

$svg = $dom->saveHTML();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="//bhsjacket.local/coronavirus/coronavirus-data/tooltip.css">

    <style>

        /* .bay-area-zips {
            max-width: 600px;
        } */

        .bay-area-zips path {
            stroke: #e7e7e7;
            stroke-width: 1px;
            fill: #e7e7e7;
            transition: fill 0.2s;
        }
        
        .bay-area-zips path[data-zip] {
            stroke: white;
        }

        .bay-area-zips path[data-zip]:hover {
            stroke: black;
        }

        [data-active_type="percent_positive"] .data-value[data-type="percent_positive"],
        [data-active_type="cases_per_mille"] .data-value[data-type="cases_per_mille"],
        [data-active_type="cases"] .data-value[data-type="cases"],
        [data-active_type="income"] .data-value[data-type="income"],
        [data-active_type="population"] .data-value[data-type="population"] {
            font-weight: bold;
            color: #800000;
        }

        .zip-code-map {
            text-align: center;
        }

        .map-legend {
            margin-top: -10px;
        }

        @media all and (max-width: 750px) {
            .map-legend {
                display: flex;
                flex-direction: column;

                width: 250px;
                margin: 0 auto;

                margin-top: 20px;
            }
        }

        .legend-button {
            padding: 10px 15px;
            font-family: 'pt-sans', 'PT Sans', sans-serif;
            font-size: 15px;
            border: none;
            outline: none;
            cursor: pointer;
            margin-bottom: 4px;
            transition: all 0.2s;
            box-shadow: inset 0px 0px 0px 2px #808080;
            background-color: white;
            color: #808080;
        }

        .legend-button:hover {
            box-shadow: inset 0px 0px 0px 2px #636363;
            color: #636363;
        }

        .legend-button.active-button {
            background-color: #800000;
            color: white;
            box-shadow: none;
        }

        .legend-button.active-button:hover {
            background-color: #5A0000;
        }

    </style>
</head>
<body>

    <div class="zip-code-map" data-active_type="percent_positive">

        <?php echo $svg; ?>
        
        <div class="tooltip">
            <h3 class="title" data-type="zip"><!-- ZIP Code --></h3>
            <h4 class="subtitle" data-type="city"><!-- City & State --></h4>
            <div class="data">
                <span class="data-label">Cases per 1,000</span>
                <span class="data-value" data-type="cases_per_mille"><!-- Cases per 1,000 --></span>
            </div>
            <div class="data">
                <span class="data-label">Total cases</span>
                <span class="data-value" data-type="cases"><!-- Total cases --></span>
            </div>
            <div class="data">
                <span class="data-label">Total tests</span>
                <span class="data-value" data-type="tests"><!-- Total tests --></span>
            </div>
            <div class="data">
                <span class="data-label">Positivity rate</span>
                <span class="data-value" data-type="percent_positive"><!-- Positivity rate --></span>
            </div>
            <div class="data">
                <span class="data-label">Median income</span>
                <span class="data-value" data-type="income"><!-- Median income --></span>
            </div>
            <div class="data">
                <span class="data-label">Population</span>
                <span class="data-value" data-type="population"><!-- Population --></span>
            </div>
        </div>

        <div class="map-legend">
            <button class="legend-button active-button" data-type="percent_positive">Positivity Rate</button>
            <button class="legend-button" data-type="cases_per_mille">Cases per 1,000</button>
            <button class="legend-button" data-type="cases">Total Cases</button>
            <button class="legend-button" data-type="population">Population</button>
            <button class="legend-button" data-type="income">Median Income</button>
        </div>

    </div>

    <script src="http://bhsjacket.local/coronavirus/coronavirus-data/color-generator.js"></script>
    <script>

        var maxValue = [];
        document.querySelectorAll('.legend-button').forEach(element => {
            element.onclick = event => {

                var zips = document.querySelectorAll('path[data-zip]');

                zips.forEach(path => {
                    maxValue.push( path.dataset[event.target.dataset.type] );
                })

                zips.forEach(path => {
                    path.style.fill = getColor( path.dataset[event.target.dataset.type] / Math.max(...maxValue) );
                })

                document.querySelector('.legend-button.active-button').classList.remove('active-button');
                element.classList.add('active-button');

                document.querySelector('.zip-code-map').dataset.active_type = event.target.dataset.type;

                maxValue = [];

            }
        })

        var appendOnce = false;
        var prevValue;
        var tooltip = document.querySelector('.tooltip');

        function changeSvgSize() {
            if( window.innerWidth < 750 ) {
                document.querySelector('svg.bay-area-zips').setAttribute('viewBox', '200 500 600 420');
            } else {
                document.querySelector('svg.bay-area-zips').setAttribute('viewBox', '0 500 800 420');
            }
        }
        changeSvgSize();
        window.onresize = () => { changeSvgSize(); }

        window.onmousemove = event => {
            var data = event.target.dataset;
            if( data.zip ) {
                tooltip.querySelector('[data-type="city"]').innerHTML = data.city;
                tooltip.querySelector('[data-type="zip"]').innerHTML = data.zip;

                tooltip.querySelector('[data-type="cases_per_mille"]').innerHTML = Math.round( data.cases_per_mille * 10 ) / 10;
                tooltip.querySelector('[data-type="cases"]').innerHTML = Number(data.cases).toLocaleString();
                tooltip.querySelector('[data-type="tests"]').innerHTML = Number(data.tests).toLocaleString();
                tooltip.querySelector('[data-type="income"]').innerHTML = '$' + Number(data.income).toLocaleString();
                tooltip.querySelector('[data-type="percent_positive"]').innerHTML = Math.round( data.percent_positive * 10 ) / 10 + '%';
                tooltip.querySelector('[data-type="population"]').innerHTML = Number(data.population).toLocaleString();

                tooltip.classList.add('tooltip-shown');

                if(appendOnce == false) {
                    event.target.parentNode.append(event.target);
                    appendOnce = true;
                } else {
                    if(prevValue !== data.zip) {
                        appendOnce = false;
                        prevValue = data.zip;
                    }
                }

                document.querySelector('.tooltip').style.left = event.pageX + 'px';
                document.querySelector('.tooltip').style.top = event.pageY + 'px';

            } else {
                tooltip.classList.remove('tooltip-shown');
            }
        }

    </script>

</body>
</html>