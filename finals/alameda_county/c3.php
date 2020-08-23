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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.18/c3.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.16.0/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.18/c3.min.js"></script>
    <script>

        var chart = c3.generate({
            bindto: '#chart',
            data: {
                columns: [
                    ['New Daily Cases', <?php echo $cases; ?>],
                ]
            },
        });

    </script>
</body>
</html>