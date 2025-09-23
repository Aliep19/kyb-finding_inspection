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
                ds.borderRadius = 6; // Kasih efek rounded bar
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

        function updateChartAndCards(departmentId) {
            fetch('/ratio-chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update chart
                if (data.datasets) {
                    data.datasets.forEach(ds => {
                        ds.borderRadius = 6; // Tetap rounded bar
                    });
                }
                ratioChart.data = data;
                ratioChart.update();

                // Update Defect Ratio comparison card
                let defectComparison = data.defect.comparison;
                let defectTarget = document.getElementById('defect-comparison-card');

                if (defectComparison !== null) {
                    if (defectComparison > 0) {
                        defectTarget.innerHTML = `<span class="text-danger">${defectComparison}% ↑</span>`;
                    } else if (defectComparison < 0) {
                        defectTarget.innerHTML = `<span class="text-success">${Math.abs(defectComparison)}% ↓</span>`;
                    } else {
                        defectTarget.innerHTML = `<span class="text-muted">0% (stagnan)</span>`;
                    }
                } else {
                    defectTarget.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
                }

                // Update Defect Ratio month values
                document.getElementById('defect-this-month').innerHTML =
                    `${data.defect.thisMonthName} : <strong>${data.defect.thisMonthValue}</strong>%`;

                if (data.defect.prevMonthValue !== null) {
                    document.getElementById('defect-prev-month').innerHTML =
                        `${data.defect.prevMonthName} : <strong>${data.defect.prevMonthValue}</strong>%`;
                } else {
                    document.getElementById('defect-prev-month').innerHTML = '';
                }

                // Update Repair Ratio comparison card
                let repairComparison = data.repair.comparison;
                let repairTarget = document.getElementById('repair-comparison-card');

                if (repairComparison !== null) {
                    if (repairComparison > 0) {
                        repairTarget.innerHTML = `<span class="text-danger">${repairComparison}% ↑</span>`;
                    } else if (repairComparison < 0) {
                        repairTarget.innerHTML = `<span class="text-success">${Math.abs(repairComparison)}% ↓</span>`;
                    } else {
                        repairTarget.innerHTML = `<span class="text-muted">0% (stagnan)</span>`;
                    }
                } else {
                    repairTarget.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
                }

                // Update Repair Ratio month values
                document.getElementById('repair-this-month').innerHTML =
                    `${data.repair.thisMonthName} : <strong>${data.repair.thisMonthValue}</strong>%`;

                if (data.repair.prevMonthValue !== null) {
                    document.getElementById('repair-prev-month').innerHTML =
                        `${data.repair.prevMonthName} : <strong>${data.repair.prevMonthValue}</strong>%`;
                } else {
                    document.getElementById('repair-prev-month').innerHTML = '';
                }

                // Update Reject Ratio comparison card
                let rejectComparison = data.reject.comparison;
                let rejectTarget = document.getElementById('reject-comparison-card');

                if (rejectComparison !== null) {
                    if (rejectComparison > 0) {
                        rejectTarget.innerHTML = `<span class="text-danger">${rejectComparison}% ↑</span>`;
                    } else if (rejectComparison < 0) {
                        rejectTarget.innerHTML = `<span class="text-success">${Math.abs(rejectComparison)}% ↓</span>`;
                    } else {
                        rejectTarget.innerHTML = `<span class="text-muted">0% (stagnan)</span>`;
                    }
                } else {
                    rejectTarget.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
                }

                // Update Reject Ratio month values
                document.getElementById('reject-this-month').innerHTML =
                    `${data.reject.thisMonthName} : <strong>${data.reject.thisMonthValue}</strong>%`;

                if (data.reject.prevMonthValue !== null) {
                    document.getElementById('reject-prev-month').innerHTML =
                        `${data.reject.prevMonthName} : <strong>${data.reject.prevMonthValue}</strong>%`;
                } else {
                    document.getElementById('reject-prev-month').innerHTML = '';
                }
            })
            .catch(error => console.error('Error fetching ratio chart data:', error));
        }

        // Event listener untuk dropdown
        document.getElementById('departmentFilterRatio').addEventListener('change', function () {
            updateChartAndCards(this.value);
        });

        // Trigger fetch pertama kali (pakai default selected)
        let defaultDept = document.getElementById('departmentFilterRatio').value;
        updateChartAndCards(defaultDept);
    });
</script>
