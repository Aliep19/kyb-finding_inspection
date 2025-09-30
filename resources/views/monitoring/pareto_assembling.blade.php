<x-head></x-head>
<div class="col-12">
    <div class="card mt-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white fw-bold">
                <i class="fa fa-chart-bar me-2"></i>
                <span id="paretoAssemblingTitle">TOP 3 Pareto</span>
            </h6>
        </div>
        <div class="card-body bg-light flex-grow-1 d-flex flex-column">
            <div class="chart-container" style="height: 200px;">
                <canvas id="paretoAssemblingChart"></canvas>
            </div>
        </div>
    </div>
</div>

<x-script></x-script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('paretoAssemblingChart').getContext('2d');
        const titleElement = document.getElementById('paretoAssemblingTitle');
        const chartData = {!! json_encode($paretoAssemblingChartData) !!};
        const workstations = Object.keys(chartData);
        let currentIndex = 0;

        // Fungsi cek warna gelap/terang
        function isDarkColor(hexColor) {
            let c = hexColor.replace('#', '');
            if (c.length === 8) c = c.substring(0, 6);
            const rgb = parseInt(c, 16);
            const r = (rgb >> 16) & 0xff;
            const g = (rgb >> 8) & 0xff;
            const b = (rgb >> 0) & 0xff;
            const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
            return luminance < 140;
        }

        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData[workstations[currentIndex]].labels,
                datasets: [{
                    label: 'FI Findings (Pcs)',
                    data: chartData[workstations[currentIndex]].values,
                    backgroundColor: chartData[workstations[currentIndex]].colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'FI Findings (Pcs)'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true },
                    datalabels: {
                        color: function(context) {
                            const bgColor = context.dataset.backgroundColor[context.dataIndex];
                            return isDarkColor(bgColor) ? '#fff' : '#000';
                        },
                        anchor: 'center',
                        align: 'center',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value) => value
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Fungsi untuk update chart
        function updateChart() {
            currentIndex = (currentIndex + 1) % workstations.length;
            const data = chartData[workstations[currentIndex]];
            titleElement.textContent = data.title;
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.values;
            chart.data.datasets[0].backgroundColor = data.colors;
            chart.update();
        }

        // Ganti chart setiap 5 detik
        if (workstations.length > 1) {
            setInterval(updateChart, 5000);
        }
    });
</script>
