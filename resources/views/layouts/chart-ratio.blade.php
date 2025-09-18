<div class="card shadow-lg border-0 rounded-3 mt-4">
    <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="fa-solid fa-chart-column me-2"></i> DEFECT RATIO FI FINDING 4W {{ date('Y') }}
        </h6>
        <!-- Dropdown department -->
        <div>
            <select id="departmentFilterRatio" class="form-select form-select-sm border-0 shadow-sm"
                style="width: 220px; font-size: 0.9rem;">
                <option value="">   4W Departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ $defaultDepartment && $defaultDepartment->id == $department->id ? 'selected' : '' }}>
                        {{ $department->dept_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body bg-light">
        <div style="height:400px;">
            <canvas id="ratioChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctxRatio = document.getElementById('ratioChart').getContext('2d');

        var chartData = {!! json_encode($ratioChartData) !!};
        if (chartData.datasets) {
            chartData.datasets.forEach(ds => {
                ds.borderRadius = 6; // kasih efek rounded bar aja
            });
        }

        var ratioChart = new Chart(ctxRatio, {
            type: 'bar',
            data: chartData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        title: {
                            display: true,
                            text: 'Ratio (%)',
                            color: '#333',
                            font: { size: 13, weight: 'bold' }
                        }
                    },
                    x: {
                        grid: { display: false },
                        title: {
                            display: true,
                            text: 'Tahun {{ date("Y") }}',
                            color: '#333',
                            font: { size: 13, weight: 'bold' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 12,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#212529',
                        titleColor: '#fff',
                        bodyColor: '#f8f9fa',
                        borderColor: '#dee2e6',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.formattedValue + '%';
                            }
                        }
                    },
                    datalabels: {
                        color: '#000',
                        anchor: 'end',
                        align: 'top',
                        font: { weight: 'bold', size: 11 },
                        formatter: function (value) {
                            return value !== null ? value + '%' : '';
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Event listener untuk dropdown
        document.getElementById('departmentFilterRatio').addEventListener('change', function () {
            var departmentId = this.value;

            fetch('/ratio-chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.datasets) {
                    data.datasets.forEach(ds => {
                        ds.borderRadius = 6; // tetap rounded bar
                    });
                }
                ratioChart.data = data;
                ratioChart.update();
            })
            .catch(error => console.error('Error fetching ratio chart data:', error));
        });
    });
</script>
