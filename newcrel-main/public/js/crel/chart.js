var chartCreate = {
    exampleChart: function(el, d){
        let addrs = [],
        d_datas = [], y_datas = [];
        d.forEach((item, idx) => {
            var d_obj = {x: `'${idx}'`, y: item.ori_dgp, fillColor: (idx)? '#a3a3a3':'#fbb900'},
            y_obj = {x: `'${idx}'`, y: item.ori_yp, fillColor: (idx)? '#bbb':'#F9CE1D'};

            addrs.push(item.simple_addr.split(" "));
            d_datas.push(d_obj);
            y_datas.push(y_obj);

        });
        // var colors = ['#269ffb', '#26e7a5', '#febb3b', '#ff6077', '#6f5fa7', '#6d838d', '#46b3a9', '#d730eb'];
        var labelColors = ['#FF9800', '#bbb', '#bbb', '#bbb', '#bbb', '#bbb', '#bbb', '#bbb'];
        var options = {
            series: [{
                name: '대지평당가',
                data: d_datas,
             }, {
                name: '연면적평당가',
                data: y_datas,
            }],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            chart: {
                height: 340,
                width: 430,
                type: 'bar',
                events: {
                    click: function(chart, w, e) {
                        // console.log(chart, w, e)
                    }
                }
            },
            // colors: ['transparent'],
            plotOptions: {
                bar: {
                    columnWidth: '55%',
                    distributed: true,

                }
            },
            fill: {
                opacity: 1
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                y: {
                  formatter: (val) =>{
                    return "￦ " + custom.comma(val) + " 원"
                  }
                }
            },
            legend: {
                show: true,
                showForSingleSeries: true,
                labels: {
                    colors: '#000',
                    useSeriesColors: false
                },
                markers: {
                    fillColors: ['#a3a3a3', '#bbb'],
                }
            },
            xaxis: {
                categories: addrs,
                labels: {
                    style: {
                        colors: labelColors,
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: (val)=>{
                        return custom.comma(val/10000) + '만';
                    }
                }
            },
            title: {
                text: '대지/연면적 평당가',
                align: 'center',
                style: {
                    fontSize: '20px'
                }
            }

        };

        var chart = new ApexCharts(el, options);
        chart.render();



    },
    gongsiChart: function(el, d, se){
        // console.log(d)
        let years = [],
        g_prices = [],
        simple_addr = se.simple_addr;
        if(d.lp.fg){
            d.lp.items.forEach((item, idx)=>{
                years.push(item.year)
                g_prices.push(item.lend_price)
            });
        }

        let g_rate = (g_prices[g_prices.length-1] - g_prices[g_prices.length-2]) / g_prices[g_prices.length-2] * 100;
        if(isNaN(g_rate)){
            g_rate = '-';
        }else{
            g_rate = Math.trunc(g_rate * 100) / 100;
        }

        var options = {
                series: [{
                    name: "공시지가",
                    data: g_prices,
                    // [1000, 2000, 2022, 3000, 9999]
                 }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#fbb900'],
                dataLabels: {
                    enabled: false
                 },
                stroke: {
                    curve: 'straight'
                },
                title: {
                    text: '공시지가 변동',
                    align: 'center',
                    style: {
                        fontSize: '20px'
                    }

                },
                subtitle: {
                    text: `${simple_addr} ※ 전년대비: ${g_rate}% 상승`, //'역삼동 777-777 [전년대비: 99% 상승]',
                    align: 'center'
                },
                labels: years,
                // [2018, 2019, 2020, 2021, 2022],

                yaxis: {
                    opposite: true,
                    labels: {
                        formatter: (val)=>{
                            return custom.comma(val/10000) + '만';
                        }
                    }
                },
                tooltip: {
                    y: {
                      formatter: (val) =>{
                        return "￦ " + custom.comma(val) + " 원"
                      }
                    }
                },
                legend: {
                    horizontalAlign: 'center',
                    showForSingleSeries: true,
                    labels: {
                        colors: '#000',
                        useSeriesColors: false
                    },
                    markers: {
                        fillColors: '#a3a3a3',
                    }
                }
            };

          var chart = new ApexCharts(el, options);
          chart.render();


    }
}
