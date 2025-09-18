<!-- resources/views/layouts/chart-painting.blade.php -->
<div class="card shadow-lg border-0 rounded-3 mt-4">
    <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-white">
            <i class="fa fa-chart-line me-2"></i> TREND OF Painting VS Not Painting Finding {{ date('Y') }}
        </h6>
        <!-- Dropdown department -->
        <div>
            <select id="departmentFilterPainting" class="form-select form-select-sm border-0 shadow-sm"
                style="width: 220px; font-size: 0.9rem;">
                <option value="">4W Departments</option>
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
            <canvas id="paintingChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('paintingChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {!! json_encode($paintingRatioChartData) !!},
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        title: {
                            display: true,
                            text: 'FI Painting Finding (Pcs)',
                            color: '#333',
                            font: { size: 13, weight: 'bold' }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
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
                            boxWidth: 10,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#212529',
                        titleColor: '#fff',
                        bodyColor: '#f8f9fa',
                        borderColor: '#dee2e6',
                        borderWidth: 1,
                        padding: 10
                    },
                    datalabels: {
                        color: '#000',
                        anchor: 'end',
                        align: 'top',
                        font: { weight: 'bold', size: 11 },
                        formatter: function (value) {
                            return value !== null ? value : '';
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Event listener untuk dropdown
        document.getElementById('departmentFilterPainting').addEventListener('change', function () {
            var departmentId = this.value;
            fetch('/painting-ratio-chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                chart.data = data;
                chart.update();
            })
            .catch(error => console.error('Error fetching painting chart data:', error));
        });
    });
</script>
