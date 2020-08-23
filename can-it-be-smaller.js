function getColor( percentage, start = [128,0,0], end = [255,255,255] ) {

    return 'rgb(' + start.map( (channel, index) => {
        return Math.round( channel + percentage * ( end[index] - channel ) );
    }).join(',') + ')';

}

console.log( getColor( 0.5, [0,0,0], [255,255,255] ) );