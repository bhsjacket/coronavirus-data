window.Apex = {
    colors: ['#800000', '#808080', '#14506e', '#006d67', '#622956', '#ffd53e'],
    markers: { hover: { size: 0 } },
    fill: { opacity: 1 },
    chart: {
        fontFamily: "pt-sans, 'PT Sans', sans-serif",
        animations: { enabled: false },
        toolbar: { show: false },
        zoom: { enabled: false },
    },
    stroke: {
        width: 2,
        curve: 'straight'
    },
    states: {
        hover: { filter: { type: 'none' } },
        active: { filter: { type: 'none' } }
    },
    noData: { text: "Oops, this visualization is broken. We're on it!" },
}