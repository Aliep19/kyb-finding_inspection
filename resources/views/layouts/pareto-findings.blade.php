<div class="card shadow-lg border-0 rounded-3 mt-4">
    <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-black">
            <i class="fa fa-chart-line me-2"></i> PARETO FI FINDINGS BY LINE {{ date('Y') }}
        </h6>
        <!-- Dropdown department -->
        <div>
            <select id="departmentFilterPareto" class="form-select form-select-sm border-0 shadow-sm"
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
            <canvas id="paretoChart"></canvas>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('paretoChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {!! json_encode($paretoFindingsChartData) !!},
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
                        stacked: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        title: {
                            display: true,
                            text: 'FI Findings (Pcs)',
                            color: '#333',
                            font: { size: 13, weight: 'bold' }
                        }
                    },
                    x: {
                        stacked: true,
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
                        display: true,
                        color: function(context) {
                            let bgColor = context.dataset.backgroundColor;

                            // kalau dataset pakai array warna (per bar)
                            if (Array.isArray(bgColor)) {
                                bgColor = bgColor[context.dataIndex];
                            }

                            // default hitam kalau gagal parse
                            if (!bgColor) return '#000';

                            // konversi HEX -> RGB
                            function hexToRgb(hex) {
                                hex = hex.replace(/^#/, '');
                                if (hex.length === 3) {
                                    hex = hex.split('').map(x => x + x).join('');
                                }
                                const num = parseInt(hex, 16);
                                return {
                                    r: (num >> 16) & 255,
                                    g: (num >> 8) & 255,
                                    b: num & 255
                                };
                            }

                            const { r, g, b } = hexToRgb(bgColor);

                            // hitung luminance (0 = gelap, 255 = terang)
                            const luminance = 0.299 * r + 0.587 * g + 0.114 * b;

                            return luminance > 150 ? '#000' : '#fff';
                        },
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        anchor: 'center',
                        align: 'center',
                        formatter: function(value) {
                            return value > 0 ? value : ''; // Kalau 0 gak ditampilkan
                        }
                    }

                }
            },
            plugins: [ChartDataLabels] // aktifkan datalabels plugin
        });

        // Event listener untuk dropdown
        document.getElementById('departmentFilterPareto').addEventListener('change', function () {
            var departmentId = this.value;
            fetch('/pareto-findings-chart-data' + (departmentId ? '/' + departmentId : ''), {
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
            .catch(error => console.error('Error fetching pareto chart data:', error));
        });
    });
</script>

