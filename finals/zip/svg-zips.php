<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bazipmap</title>
    <style>

        .bay-area-zips {
            max-width: 600px;
        }

        .bay-area-zips path {
            stroke: #e7e7e7;
            stroke-width: 1px;
            fill: #e7e7e7;
        }
        
        .bay-area-zips path[data-county="alameda"] {
            stroke: white;
        }

        .bay-area-zips path[data-county="alameda"]:hover {
            stroke: black;
            stroke-width: 2px;
        }

        .tooltip {
            position: absolute;
            font-family: sans;
            background-color: rgba(255, 255, 255, 0.85);
            padding: 15px;
            display: none;
            box-shadow: 0 0 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 5px;

            transform: translateX(-50%);
            margin-top: 25px;
        }

        .tooltip .zip,
        .tooltip .city,
        .tooltip span {
            margin: 0;
            padding: 0;
            font-family: 'PT Sans';
            font-weight: normal;
        }

        .tooltip .city {
            color: gray;
            font-weight: bold;
            margin-bottom: 7.5px;
        }

        .tooltip .data {
            display: flex;
        }

        .tooltip .data:not(:first-of-type) {
            border-top: solid 1px #e7e7e7;
            padding-top: 2.5px;
            margin-top: 2.5px;
        }

        .tooltip .data > .data-label {
            color: gray;
            margin-right: 20px;
            white-space: nowrap;
        }

        .tooltip .data > .data-tests,
        .tooltip .data > .data-label {
            width: 100%;
        }

    </style>
</head>
<body>
    <?php print_r(file_get_contents('zip.svg')); ?>
    
    <div class="tooltip">
        <h3 class="zip">XXXXX</h3>
        <h4 class="city">Unknown, CA</h4>
        <div class="data">
            <span class="data-label">Population Tested</span>
            <span class="data-tests">?</span>
        </div>
        <div class="data">
            <span class="data-label">Cases</span>
            <span class="data-cases">?</span>
        </div>
    </div>

    <div id="map"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/mix-css-color"></script>
    <script>

        var data = <?php echo file_get_contents('data.json'); ?>;
        var maxValue = 0;

        $('path[data-county="alameda"]').each(function(){
            for(var zip in data) {
                zip = data[zip];
                if(zip.zip == $(this).data('zip')) {
                    $(this).attr('data-city', zip.city);
                    $(this).attr('data-tested', Math.round( (zip.tests / zip.population) * 100 ) );
                    $(this).attr('data-cases', zip.cases);
                }
            }

            if( $(this).data('tested') > maxValue ) {
                maxValue = $(this).data('tested');
            }
        })

        function getColor(value, max) {
            var percentage = (value * 100) / max;
            return mixCssColor('#800000', '#e7e7e7', percentage).hex;
        }

        for(var zip in data) {
            zip = data[zip];
            $('path[data-zip="' + zip.zip + '"').css( 'fill', getColor((zip.tests / zip.population) * 100, maxValue) );
        }

        var appendOnce = false;
        var prevValue;
        $(document).on('mousemove', function(event){
            if( $(event.target).data('county') == 'alameda' ) {
                $('.tooltip').find('.data-tests').text( '~' + $(event.target).data('tested') + '%' );
                $('.tooltip').find('.data-cases').text( $(event.target).data('cases') );
                $('.tooltip').find('.city').text( $(event.target).data('city') );
                $('.tooltip').find('.zip').text( $(event.target).data('zip') );

                if(appendOnce == false) {
                    $(event.target).appendTo('.bay-area-zips');
                    appendOnce = true;
                } else {
                    if(prevValue !== $(event.target).data('zip')) {
                        appendOnce = false;
                        prevValue = $(event.target).data('zip');
                    }
                }

                $('.tooltip').fadeIn(100);
                $('.tooltip').css({
                    left:  event.pageX,
                    top:   event.pageY
                });
            } else {
                $('.tooltip').fadeOut(100);
            }
        });

    </script>

</body>
</html>