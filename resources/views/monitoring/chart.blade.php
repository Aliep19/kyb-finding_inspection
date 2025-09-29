<!-- resources/views/monitoring/chart.blade.php -->
<div class="row mt-3">
    <!-- Kiri Atas: Final Inspection Finding -->
    <div class="col-md-6 d-flex">
        <div class="card border-0 shadow-sm rounded-3 h-100 w-100 d-flex flex-column">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white">
                    <i class="fa fa-chart-line me-2"></i> Final Inspection 4W Finding {{ date('Y') }}
                </h6>
                <div>
                    <select id="departmentFilter" class="form-select form-select-sm border-0 shadow-sm"
                        style="width: 200px; font-size: 0.85rem;">
                        <option value="">4W Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                                {{ $department->dept_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body bg-light flex-grow-1 d-flex flex-column">
                <div style="height: 250px;">
                    <canvas id="findingChart"></canvas>
                </div>
                <div class="row mt-3">
                    <!-- Stats kiri -->
                    <div class="col-8">
                        <div class="card border-0 shadow-sm rounded-3 p-2 bg-white d-flex flex-row align-items-center gap-3 h-100">
                            <div class="d-flex flex-row small gap-2">
                                <span id="this-month-finding" class="fw-bold"></span>
                                <span id="prev-month-finding" class="text-muted"></span>
                                <span id="comparison-card-finding" class="text-muted"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanan Atas: Defect Ratio -->
    <div class="col-md-6 d-flex">
        <div class="card border-0 shadow-sm rounded-3 h-100 w-100 d-flex flex-column">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white">
                    <i class="fa fa-chart-line me-2"></i> DEFECT RATIO FI Finding 4W {{ date('Y') }}
                </h6>
                <div>
                    <select id="departmentFilterRatio" class="form-select form-select-sm border-0 shadow-sm"
                        style="width: 200px; font-size: 0.85rem;">
                        <option value="">4W Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                                {{ $department->dept_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body bg-light flex-grow-1 d-flex flex-column">
                <div style="height: 250px;">
                    <canvas id="ratioChart"></canvas>
                </div>
<div class="row mt-3">
    <!-- Defect -->
    <div class="col-4">
        <div class="card border-0 shadow-sm rounded-3 p-2 bg-white text-dark h-100">
            <div class="d-flex flex-column small">
                <span class="fw-bold text-danger">Defect</span>
                <span id="this-month-defect" class="fw-bold"></span>
                <span id="prev-month-defect" class="text-dark"></span>
                <span id="comparison-card-defect" class="fw-semibold"></span>
            </div>
        </div>
    </div>

    <!-- Repair -->
    <div class="col-4">
        <div class="card border-0 shadow-sm rounded-3 p-2 bg-white text-dark h-100">
            <div class="d-flex flex-column small">
                <span class="fw-bold text-warning">Repair</span>
                <span id="this-month-repair" class="fw-bold"></span>
                <span id="prev-month-repair" class="text-dark"></span>
                <span id="comparison-card-repair" class="fw-semibold"></span>
            </div>
        </div>
    </div>

    <!-- Reject -->
    <div class="col-4">
        <div class="card border-0 shadow-sm rounded-3 p-2 bg-white text-dark h-100">
            <div class="d-flex flex-column small">
                <span class="fw-bold text-primary">Reject</span>
                <span id="this-month-reject" class="fw-bold"></span>
                <span id="prev-month-reject" class="text-dark"></span>
                <span id="comparison-card-reject" class="fw-semibold"></span>
            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart Final Inspection
        var ctxFinding = document.getElementById('findingChart').getContext('2d');
        var findingChart = new Chart(ctxFinding, {
            type: 'bar',
            data: {!! json_encode($chartData) !!},
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    legend: { display: true, labels: { font: { size: 10 } } },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#333',
                        font: { size: 10, weight: 'bold' },
                        formatter: function(value) {
                            return value; // angka ditampilkan sesuai dataset
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // <-- aktifkan plugin
        });


        function updateFindingChart(departmentId) {
            fetch('/monitoring/chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(response => response.json()).then(data => {
                findingChart.data.labels = data.labels;
                findingChart.data.datasets = data.datasets;
                findingChart.update();

                let comparison = data.comparison;
                let target = document.getElementById('comparison-card-finding');
                if (comparison !== null) {
                    target.innerHTML = comparison > 0 ? `<span class="text-danger">${comparison}% ↑</span>` :
                        (comparison < 0 ? `<span class="text-success">${Math.abs(comparison)}% ↓</span>` : `<span class="text-muted">0% (stagnan)</span>`);
                } else target.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;

                document.getElementById('this-month-finding').innerHTML = `${data.thisMonthName} : <strong>${data.thisMonthValue}</strong> pcs`;
                document.getElementById('prev-month-finding').innerHTML = data.prevMonthValue !== null ? `${data.prevMonthName} : <strong>${data.prevMonthValue}</strong> pcs` : '';
            }).catch(error => console.error('Error:', error));
        }

        // Chart Defect Ratio
var ctxRatio = document.getElementById('ratioChart').getContext('2d');
var ratioChart = new Chart(ctxRatio, {
    type: 'bar',
    data: {!! json_encode($ratioChartData) !!},
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { callback: function(value) { return value + '%'; } } }
        },
        plugins: {
            legend: { display: true, labels: { font: { size: 10 } } },
            datalabels: {
                anchor: 'end',
                align: 'top',
                color: '#333',
                font: { size: 10, weight: 'bold' },
                formatter: function(value) {
                    return value !== null && value !== undefined ? value + '%' : ''; // Skip labels for null/undefined
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

function updateRatioChart(departmentId) {
    fetch('/monitoring/ratio-chart-data' + (departmentId ? '/' + departmentId : ''), {
        method: 'GET',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(response => response.json()).then(data => {
        // Filter out months with no data (null or undefined)
        const filteredData = {
            labels: [],
            datasets: data.datasets.map(dataset => ({
                ...dataset,
                data: []
            }))
        };

        // Only include months with valid data
        data.labels.forEach((label, index) => {
            const hasData = data.datasets.some(dataset => dataset.data[index] !== null && dataset.data[index] !== undefined);
            if (hasData) {
                filteredData.labels.push(label);
                filteredData.datasets.forEach((dataset, dsIndex) => {
                    dataset.data.push(data.datasets[dsIndex].data[index] ?? 0); // Use 0 for missing data in valid months
                });
            }
        });

        // Update chart with filtered data
        ratioChart.data.labels = filteredData.labels;
        ratioChart.data.datasets = filteredData.datasets;
        ratioChart.update();

        // Update stats for defect, repair, reject
        let defect = data.defect, repair = data.repair, reject = data.reject;
        updateStat('defect', defect);
        updateStat('repair', repair);
        updateStat('reject', reject);
    }).catch(error => console.error('Error:', error));
}

function updateStat(type, stat) {
    let compId = `comparison-card-${type}`, thisId = `this-month-${type}`, prevId = `prev-month-${type}`;
    let comparison = stat.comparison, thisMonthValue = stat.thisMonthValue, prevMonthValue = stat.prevMonthValue,
        thisMonthName = stat.thisMonthName, prevMonthName = stat.prevMonthName;

    document.getElementById(thisId).innerHTML = thisMonthValue !== null ? `${thisMonthName} : <strong>${thisMonthValue}%</strong>` : `${thisMonthName} : <strong>Data tidak tersedia</strong>`;
    document.getElementById(prevId).innerHTML = prevMonthValue !== null ? `${prevMonthName} : <strong>${prevMonthValue}%</strong>` : '';
    let target = document.getElementById(compId);
    if (comparison !== null) {
        target.innerHTML = comparison > 0 ? `<span class="text-danger">${comparison}% ↑</span>` :
            (comparison < 0 ? `<span class="text-success">${Math.abs(comparison)}% ↓</span>` : `<span class="text-muted">0% (stagnan)</span>`);
    } else {
        target.innerHTML = `<span class="text-muted">Data tidak tersedia</span>`;
    }
}

        // Event Listeners
        ['departmentFilter', 'departmentFilterRatio'].forEach(id => {
            document.getElementById(id).addEventListener('change', function () {
                let deptId = this.value;
                updateFindingChart(deptId);
                updateRatioChart(deptId);
            });
        });

        // Initial Load
        let defaultDept = document.getElementById('departmentFilter').value;
        updateFindingChart(defaultDept);
        updateRatioChart(defaultDept);
    });
</script>
