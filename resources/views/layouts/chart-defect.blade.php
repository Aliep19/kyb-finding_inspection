<!-- views/layouts/chart-defect.blade.php -->
<div class="card shadow-lg border-0 rounded-3 mt-4">
    <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-white">
            <i class="fa fa-chart-line me-2"></i> Final Inspection 4W Finding {{ date('Y') }}
        </h6>
        <!-- Dropdown department -->
        <div>
            <select id="departmentFilter" class="form-select form-select-sm border-0 shadow-sm"
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
            <canvas id="findingChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('findingChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {!! json_encode($chartData) !!},
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
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        title: {
                            display: true,
                            text: 'FI 4W Finding (Pcs)',
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

        function updateChartAndCard(departmentId) {
            fetch('/chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update chart
                chart.data.labels = data.labels;
                chart.data.datasets = data.datasets;
                chart.update();

                // Update card comparison
                let comparison = data.comparison;
                let target = document.getElementById('comparison-card');

                if (comparison !== null) {
                    if (comparison > 0) {
                        target.innerHTML = `<span class="text-danger">${comparison}% ↑</span>`;
                    } else if (comparison < 0) {
                        target.innerHTML = `<span class="text-success">${Math.abs(comparison)}% ↓</span>`;
                    } else {
                        target.innerHTML = `<span class="text-muted">0% (stagnan)</span>`;
                    }
                } else {
                    target.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
                }

                // Update PCS bulan ini & bulan lalu
                document.getElementById('this-month').innerHTML =
                    `${data.thisMonthName} : <strong>${data.thisMonthValue}</strong> pcs`;

                if (data.prevMonthValue !== null) {
                    document.getElementById('prev-month').innerHTML =
                        `${data.prevMonthName} : <strong>${data.prevMonthValue}</strong> pcs`;
                } else {
                    document.getElementById('prev-month').innerHTML = '';
                }
            })
            .catch(error => console.error('Error fetching chart data:', error));
        }

        // Event listener dropdown
        document.getElementById('departmentFilter').addEventListener('change', function () {
            updateChartAndCard(this.value);
        });

        // 🔥 Trigger fetch pertama kali (pakai default selected)
        let defaultDept = document.getElementById('departmentFilter').value;
        updateChartAndCard(defaultDept);
    });
</script>

