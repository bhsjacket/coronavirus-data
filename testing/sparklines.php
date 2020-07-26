<?php

require_once('../database.php');

$sql = "SELECT berkeley_new_cases FROM alameda_county ORDER BY date ASC";
$data = $database->query($sql);
while($row = $data->fetch_row()) {
    $cases[] = $row[0];
}
$cases = implode(',', $cases);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sparklines</title>
    <style>

        /* line with highlight area */
        .sparkline {
            stroke: #800000;
            fill: transparent;
        }

    </style>
</head>
<body>

    <svg class="sparkline" width="600" height="200" stroke-width="2"></svg>
    
    <script src="https://rawcdn.githack.com/fnando/sparkline/18ed9648c26375d4dcd80fd5d1332c427bd8d954/dist/sparkline.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/mix-css-color"></script>
    <script>

        sparkline.sparkline( $('.sparkline')[0], [<?php echo $cases; ?>] );

    </script>
</body>
</html>