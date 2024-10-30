

jQuery(document).ready(function(){

var options = {
        series: JSON.parse(chartOrders),
        colors: ['#f4a100', '#28a745'],
        chart: {
            type: 'bar',
            height: '200px'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                // endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: JSON.parse(chartOrdersMonths),
        },
        yaxis: {
            title: {
                text: 'Orders'
            },
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
            formatter: function (val) {
                return val + (val > 1 ? " orders" : " order");
            }
            }
        }
    };
    var chart = new ApexCharts(document.querySelector("#columnchart_material"), options);
    chart.render();
})
