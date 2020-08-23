function getColor( percentage, start = [128,0,0], end = [231,231,231] ) {

    return 'rgb(' + end.map( (channel, index) => {
        return channel + percentage * ( start[index] - channel ) | 0;
    }).join(',') + ')';

}