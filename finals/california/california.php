<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>California SVG Map</title>
    <style>

        .california path {
            fill: #e7e7e7;
            stroke: white;
            stroke-width: 1px;
        }

    </style>
</head>
<body>
    <?php echo file_get_contents('california.svg'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/mix-css-color"></script>
    <script>

        var data = <?php echo file_get_contents('data.json'); ?>;
        var maxValue = 0;
        var mapValue = 'cases';

        $('.california path').each(function(){
            for(var item in data) {
                item = data[item];
                if(item.county == $(this).data('county')) {
                    $(this).attr('data-cases', item.cases);
                    $(this).attr('data-deaths', item.deaths);
                    $(this).attr('data-new_cases', item.new_cases);
                    $(this).attr('data-new_deaths', item.new_deaths);
                }
            }

            if( $(this).data(mapValue) > maxValue ) {
                maxValue = $(this).data(mapValue);
            }
        })

        function getColor(value, max) {
            var percentage = (value * 100) / max;
            return mixCssColor('#800000', '#e7e7e7', percentage).hex;
        }

        $('.california path').each(function(){
            $(this).css('fill', getColor( $(this).data(mapValue), maxValue ));
        })

    </script>
</body>
</html>
