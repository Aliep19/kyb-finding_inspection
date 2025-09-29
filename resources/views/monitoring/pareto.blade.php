<x-head></x-head>
<div class="card mt-4 shadow-sm border-0 rounded-3">
    <div class="card-header bg-danger  d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-white fw-bold">
            <i class="fa fa-chart-bar me-2"></i> {{ $paretoFindingsChartData['paretoDefects']['title'] }}
        </h6>
        <select id="legendType" class="form-select form-select-sm w-auto">
            <option value="defects">Defects</option>
            <option value="workstations">Workstations</option>
        </select>
    </div>
    <div class="card-body bg-light flex-grow-1 d-flex flex-column" style="height: 420px;">
        <canvas id="paretoDefectChart"></canvas>
    </div>
</div>

<!-- Plugin datalabels -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('paretoDefectChart').getContext('2d');

        var chartConfig = {
            type: 'bar',
            data: {
                labels: {!! json_encode($paretoFindingsChartData['paretoDefects']['labels']) !!},
                datasets: [{
                    label: 'FI Findings (Pcs)',
                    data: {!! json_encode($paretoFindingsChartData['paretoDefects']['data']) !!},
                    backgroundColor: {!! json_encode($paretoFindingsChartData['paretoDefects']['backgroundColors']) !!},
                    borderColor: {!! json_encode($paretoFindingsChartData['paretoDefects']['borderColors']) !!},
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'x',
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'FI 4W Finding (Pcs)'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                return {!! json_encode($paretoFindingsChartData['legend']['defects']) !!};
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        font: {
                            weight: 'bold',
                            size: 10
                        },
                        color: '#000',
                        formatter: function(value) {
                            return value;
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            },
            plugins: [ChartDataLabels]
        };

        var paretoDefectChart = new Chart(ctx, chartConfig);

        // Dropdown ganti legend
        document.getElementById('legendType').addEventListener('change', function() {
            var selected = this.value;
            paretoDefectChart.options.plugins.legend.labels.generateLabels = function(chart) {
                return selected === 'workstations'
                    ? {!! json_encode($paretoFindingsChartData['legend']['workstations']) !!}
                    : {!! json_encode($paretoFindingsChartData['legend']['defects']) !!};
            };
            paretoDefectChart.update();
        });
    });
</script>
<x-script></x-script>
