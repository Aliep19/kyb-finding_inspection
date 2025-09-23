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

        function updateChartAndCards(departmentId) {
            fetch('/painting-ratio-chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update chart
                chart.data = data;
                chart.update();

                // Update Painting comparison card
                let paintingComparison = data.painting.comparison;
                let paintingTarget = document.getElementById('painting-comparison-card');

                if (paintingComparison !== null) {
                    if (paintingComparison > 0) {
                        paintingTarget.innerHTML = `<span class="text-danger">${paintingComparison}% ↑</span>`;
                    } else if (paintingComparison < 0) {
                        paintingTarget.innerHTML = `<span class="text-success">${Math.abs(paintingComparison)}% ↓</span>`;
                    } else {
                        paintingTarget.innerHTML = `<span class="text-muted">0% (stagnan)</span>`;
                    }
                } else {
                    paintingTarget.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
                }

                // Update Painting month values
                document.getElementById('painting-this-month').innerHTML =
                    `${data.painting.thisMonthName} : <strong>${data.painting.thisMonthValue}</strong> pcs`;

                if (data.painting.prevMonthValue !== null) {
                    document.getElementById('painting-prev-month').innerHTML =
                        `${data.painting.prevMonthName} : <strong>${data.painting.prevMonthValue}</strong> pcs`;
                } else {
                    document.getElementById('painting-prev-month').innerHTML = '';
                }

                // Update Not Painting comparison card
                let notPaintingComparison = data.notPainting.comparison;
                let notPaintingTarget = document.getElementById('not-painting-comparison-card');

                if (notPaintingComparison !== null) {
                    if (notPaintingComparison > 0) {
                        notPaintingTarget.innerHTML = `<span class="text-danger">${notPaintingComparison}% ↑</span>`;
                    } else if (notPaintingComparison < 0) {
                        notPaintingTarget.innerHTML = `<span class="text-success">${Math.abs(notPaintingComparison)}% ↓</span>`;
                    } else {
                        notPaintingTarget.innerHTML = `<span class="text-muted">0% (stagnan)</span>`;
                    }
                } else {
                    notPaintingTarget.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
                }

                // Update Not Painting month values
                document.getElementById('not-painting-this-month').innerHTML =
                    `${data.notPainting.thisMonthName} : <strong>${data.notPainting.thisMonthValue}</strong> pcs`;

                if (data.notPainting.prevMonthValue !== null) {
                    document.getElementById('not-painting-prev-month').innerHTML =
                        `${data.notPainting.prevMonthName} : <strong>${data.notPainting.prevMonthValue}</strong> pcs`;
                } else {
                    document.getElementById('not-painting-prev-month').innerHTML = '';
                }
            })
            .catch(error => console.error('Error fetching painting chart data:', error));
        }

        // Event listener untuk dropdown
        document.getElementById('departmentFilterPainting').addEventListener('change', function () {
            updateChartAndCards(this.value);
        });

        // Trigger fetch pertama kali (pakai default selected)
        let defaultDept = document.getElementById('departmentFilterPainting').value;
        updateChartAndCards(defaultDept);
    });
</script>
