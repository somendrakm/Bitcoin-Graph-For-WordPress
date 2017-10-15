jQuery(document).ready(function() {
				function formatUSD(str) {
					return str.toFixed(2);
				}

				Chart.plugins.register({
					beforeDraw: function(chartInstance) {
					var ctx = chartInstance.chart.ctx;
						ctx.fillStyle = "#202326";
						ctx.fillRect(0, 0, chartInstance.chart.width, chartInstance.chart.height);
					}
				});

				var ctx = document.getElementById("myChart");
				var data = {
				    labels: chart_data[1],
				    datasets: [{
						label: "Value",
						fill: false,
						lineTension: 0.1,
						backgroundColor: "#202326",
						borderColor: "#deb510",
						borderWidth: 2,
						borderCapStyle: 'butt',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBackgroundColor: "#fff",
						pointBorderWidth: 1,
						pointHoverRadius: 3,
						pointHoverBackgroundColor: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",
						pointHoverBorderWidth: 2,
						pointRadius: 0,
						pointHitRadius: 10,
						data: chart_data[0],
						spanGaps: false,
					}]
				};

				var myChart = new Chart(ctx, {
					type: 'line',
					backgroundColor: "#202326",
					data: data,
					options: {
						responsive: true,
				        scales: {
				            yAxes: [{
				            	stacked: false,
				            	display: true,
				            	gridLines:{
					            	color: "#313437",
					            	drawBorder: false
					            },
								ticks: {
									fontColor: "#ccc", // this here
								},
				            }],
				            xAxes: [{
				            	display: false,
				            }],
				        },
				        legend: {
				        	display: false
				        },
						tooltips: {
							callbacks: {
								label: function(tooltipItem, data) { return formatUSD(tooltipItem.yLabel) }
							},
							displayColors: false
						}
				    }
				});
			})