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
            transition: fill 0.2s;
        }

        .california {
            max-width: 600px;
        }

    </style>
</head>
<body>
    <h2 class="date"></h2>
    <?php echo file_get_contents('../california/california.svg'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/mix-css-color"></script>
    <script>

        var data = <?php echo file_get_contents('data.json'); ?>;
        var population = <?php echo file_get_contents('../../population.json'); ?>;
        var maxValue = 0.016630633627141192;
        var mapValue = 'icu';
        var mapDate = '2020-03-29';
        var intialMapDate = mapDate;

        function formatDate(date) {
            date = new Date(date);
            return date.toISOString().slice(0, 10);
        }

        setInterval(function(){

            if( formatDate(mapDate) == formatDate('2020-07-25') ) {
                mapDate = '2020-07-25';
            } else {
                var nextDate = new Date(mapDate);
                nextDate.setDate(new Date(mapDate).getDate() + 1);
                nextDate = nextDate.toISOString();
                nextDate = nextDate.slice(0, 10);
                mapDate = nextDate;
                $('.date').text(mapDate);
            }

            $('.california path').each(function(){

                for(var item in data) {
                    item = data[item];
                    if(item.date == mapDate) {
                        if(item.county == $(this).data('county')) {
                            $(this).attr('data-patients', (item.patients / population[item.county].population) * 100);
                            $(this).attr('data-icu', (item.icu / population[item.county].population) * 100);

                            $(this).css('fill', getColor( (item[mapValue] / population[item.county].population) * 100, maxValue ));
                        }
                    }
                }

            })

        }, 100);

        function getColor(value, max) {
            var percentage = (value * 100) / max;
            return mixCssColor('#800000', '#e7e7e7', percentage).hex;
        }

/*         if( $(this).data(mapValue) > maxValue ) {
            maxValue = $(this).data(mapValue);
        } */

        $(document).click(function(){
            mapDate = intialMapDate;
        })

    </script>
</body>
</html>
