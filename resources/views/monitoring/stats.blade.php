<!-- resources/views/monitoring/stats.blade.php -->
<div class="row mt-3">
    <!-- Kiri Bawah: Painting Ratio -->
    <div class="col-md-6 d-flex">
        <div class="card shadow-sm border-0 rounded-3 h-100 w-100 d-flex flex-column">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white">
                    <i class="fa fa-chart-line me-2"></i> TREND of Painting vs not Painting Finding
                </h6>
                <div>
                    <select id="departmentFilterPainting" class="form-select form-select-sm border-0 shadow-sm"
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
                    <canvas id="paintingChart"></canvas>
                </div>
                <div class="row mt-1">
                    <!-- Painting -->
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white text-dark mb-2 h-100">
                            <div class="card-body p-2 d-flex align-items-center">
                                <!-- Icon bulat -->
                                <div class="icon-sm bg-dark shadow rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 35px; height: 35px;">
                                    <i class="fa fa-paint-brush text-warning"></i>
                                </div>
                                <!-- Text sejajar -->
                                <div class="d-flex flex-wrap align-items-center gap-2 small">
                                    <span id="this-month-painting" class="fw-bold"></span>
                                    <span id="prev-month-painting" class="text-dark"></span>
                                    <span id="comparison-card-painting" class="fw-semibold"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Not Painting -->
                    <div class="col-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white text-dark mb-2 h-100">
                            <div class="card-body p-2 d-flex align-items-center">
                                <!-- Icon bulat -->
                                <div class="icon-sm bg-dark shadow rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 35px; height: 35px;">
                                    <i class="fa fa-ban text-warning"></i>
                                </div>
                                <!-- Text sejajar -->
                                <div class="d-flex flex-wrap align-items-center gap-2 small">
                                    <span id="this-month-not-painting" class="fw-bold"></span>
                                    <span id="prev-month-not-painting" class="text-dark"></span>
                                    <span id="comparison-card-not-painting" class="fw-semibold"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanan Bawah: Pareto Findings -->
    <div class="col-md-6 d-flex">
        <div class="card shadow-sm border-0 rounded-3 h-100 w-100 d-flex flex-column">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white">
                    <i class="fa fa-chart-line me-2"></i> PARETO FI Findings by Line
                </h6>
                <div>
                    <select id="departmentFilterPareto" class="form-select form-select-sm border-0 shadow-sm"
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
                    <canvas id="paretoChart"></canvas>
                </div>
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3 bg-white text-dark h-100">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-start">
                                    <div class="icon-sm bg-dark shadow rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 35px; height: 35px;">
                                        <i class="fa fa-bar-chart text-info"></i>
                                    </div>
                                    <div id="top-workstations" class="small fw-semibold"></div>
                                </div>
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
<!-- Tambahkan library plugin datalabels -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart Painting Ratio
        var ctxPainting = document.getElementById('paintingChart').getContext('2d');
        var paintingChart = new Chart(ctxPainting, {
            type: 'bar',
            data: {!! json_encode($paintingRatioChartData) !!},
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    legend: { display: true, labels: { font: { size: 10 } } },
                    datalabels: {
                        display: (context) => context.dataset.type !== 'line', // hanya bar
                        anchor: 'end',
                        align: 'end',
                        font: { size: 10, weight: 'bold' },
                        formatter: (value) => value
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Chart Pareto Findings
        var ctxPareto = document.getElementById('paretoChart').getContext('2d');
        var paretoChart = new Chart(ctxPareto, {
            type: 'bar',
            data: {!! json_encode($paretoFindingsChartData) !!},
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: { x: { beginAtZero: true } },
                indexAxis: 'x',
                plugins: {
                    legend: { display: true, labels: { font: { size: 10 } } },
                    datalabels: {
                        display: (context) => context.dataset.type !== 'line', // hanya bar
                        anchor: 'end',
                        align: 'middle', // supaya muncul di tengah bar
                        font: { size: 10, weight: 'bold' },
                        formatter: (value) => value > 0 ? value : ''
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Function updatePaintingChart
        function updatePaintingChart(departmentId) {
            fetch('/monitoring/painting-ratio-chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(response => response.json()).then(data => {
                paintingChart.data.labels = data.labels;
                paintingChart.data.datasets = data.datasets;
                paintingChart.update();

                let painting = data.painting, notPainting = data.notPainting;
                updateStat('painting', painting);
                updateStat('not-painting', notPainting);
            }).catch(error => console.error('Error:', error));
        }

        // Function updateParetoChart
        function updateParetoChart(departmentId) {
            fetch('/monitoring/pareto-findings-chart-data' + (departmentId ? '/' + departmentId : ''), {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(response => response.json()).then(data => {
                paretoChart.data.labels = data.labels;
                paretoChart.data.datasets = data.datasets;
                paretoChart.update();

                let topWs = data.topWorkstations;
                document.getElementById('top-workstations').innerHTML = `Pareto FI Finding by Line, ${topWs.monthName}:
                    ${topWs.workstations.map(w => `${w.workstation} (${w.total_ng} Pcs)`).join(', ')}`;
            }).catch(error => console.error('Error:', error));
        }

        function updateStat(type, stat) {
            let compId = `comparison-card-${type}`, thisId = `this-month-${type}`, prevId = `prev-month-${type}`;
            let comparison = stat.comparison, thisMonthValue = stat.thisMonthValue, prevMonthValue = stat.prevMonthValue,
                thisMonthName = stat.thisMonthName, prevMonthName = stat.prevMonthName;

            document.getElementById(thisId).innerHTML = `${thisMonthName} : <strong>${thisMonthValue}</strong> ${type === 'painting' || type === 'not-painting' ? 'pcs' : '%'}`;
            document.getElementById(prevId).innerHTML = prevMonthValue !== null ? `${prevMonthName} : <strong>${prevMonthValue}</strong> ${type === 'painting' || type === 'not-painting' ? 'pcs' : '%'}` : '';
            let target = document.getElementById(compId);
            if (comparison !== null) {
                target.innerHTML = comparison > 0 ? `<span class="bg-white shadow-sm p-1 rounded-3 text-danger">${comparison}% ↑</span>` :
                    (comparison < 0 ? `<span class="bg-white shadow-sm p-1 rounded-3 text-success ">${Math.abs(comparison)}% ↓</span>` : `<span class="text-muted">0% (stagnan)</span>`);
            } else target.innerHTML = `<span class="bg-white shadow-sm p-1 rounded-3 text-muted">Data tidak tersedia</span>`;
        }

        // Event Listeners
        ['departmentFilterPainting', 'departmentFilterPareto'].forEach(id => {
            document.getElementById(id).addEventListener('change', function () {
                let deptId = this.value;
                updatePaintingChart(deptId);
                updateParetoChart(deptId);
            });
        });

        // Initial Load
        let defaultDept = document.getElementById('departmentFilterPainting').value;
        updatePaintingChart(defaultDept);
        updateParetoChart(defaultDept);
    });
</script>

