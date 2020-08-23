<?php

require_once('../../database.php');

$value = 'ac_new_cases';

$data = $database->query("SELECT {$value}, date FROM alameda_county ORDER BY date ASC");
$counter = 0;
while($row = $data->fetch_assoc()) {
    $label = new DateTime($row['date']);
    $labels[] = $label->format('F j');
    $counter++;
    $cases[] = $row[$value];
}

// rate = (cases / demographic) * 100000

$labels = implode("','", $labels);
$cases = implode(",", $cases);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alameda County Statistics</title>
    <style>

        ::selection {
            background-color: #800000;
            color: white;
            fill: white;
        }

        .chart-container text,
        .chart-container span,
        .chart-container ul {
            font-family: 'PT Sans';
            font-size: 14px;
        }

        .chart-container .y-markers text {
            user-select: none;
            transform: translateX(-10%);
        }

        .graph-svg-tip .title,
        .graph-svg-tip strong {
            color: white!important;
        }

    </style>
</head>
<body>
    <div id="chart"></div>    

    <script src="https://cdn.jsdelivr.net/npm/frappe-charts@1.5.2/dist/frappe-charts.min.iife.min.js"></script>
    <script>

        const data = {
            labels: ['<?php echo $labels; ?>'],
            datasets: [{
                name: 'Cumulative Cases',
                values: [<?php echo $cases; ?>],
                chartType: 'line'
            }]
        }

        const chart = new frappe.Chart('#chart', {
            data: data,
            type: 'line',
            height: 250,
            animate: false,
            barOptions: {
                spaceRatio: 0.25
            },
            colors: ['#800000'],
            tooltipOptions: {
                formatTooltipY: d => d.toLocaleString()
            },
            axisOptions: {
                xAxisMode: 'tick',
                xIsSeries: true
            },
            lineOptions: {
                hideDots: true,
                regionFill: true
            }
        })

    </script>
</body>
</html>