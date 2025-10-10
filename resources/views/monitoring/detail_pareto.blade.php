<div class="row mt-3" id="paretoDetailsContainer">
    @foreach ($paretoAssemblingDetails as $sectionName => $defectData)
        <div class="workstation-details" data-workstation="{{ $sectionName }}" style="display: none;">
            @foreach ($defectData as $defectName => $data)
                <div class="col-12 mb-4">
                    <div class="p-3 border rounded-3 shadow-sm bg-white">
                        <h5 class="fw-bold text-danger mb-3 fs-6">
                            Pareto {{ $loop->iteration }}{{ $loop->iteration == 1 ? 'st' : ($loop->iteration == 2 ? 'nd' : ($loop->iteration == 3 ? 'rd' : 'th')) }} - {{ $defectName }}
                        </h5>

                        <div class="row g-3">
                            <!-- Kolom Kiri: Tabel Detail -->
                            <div class="col-md-6">
                                <div class="p-2 border rounded bg-light shadow-sm" style="max-height: 180px; overflow-y:auto;">
                                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                                        <thead class="table-danger">
                                            <tr>
                                                <th>Model</th>
                                                <th>Line</th>
                                                <th>Lot</th>
                                                <th>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data['details'] as $detail)
                                                <tr>
                                                    <td>{{ $detail->model }}</td>
                                                    <td>{{ $detail->line }}</td>
                                                    <td>{{ $detail->lot }}</td>
                                                    <td>{{ $detail->qty }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Grafik Trend -->
                            <div class="col-md-6">
                                <div class="p-2 border rounded bg-light shadow-sm" style="height: 180px;">
                                    <canvas id="trendChart_{{ preg_replace('/[^a-zA-Z0-9]/', '', $sectionName . '_' . $defectName) }}"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Script untuk render Chart.js -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var ctx = document.getElementById('trendChart_{{ preg_replace('/[^a-zA-Z0-9]/', '', $sectionName . '_' . $defectName) }}').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($data['trend']['labels']) !!},
                                datasets: [
                        {
                            type: 'bar',
                            label: 'Quantity (Bar)',
                            data: {!! json_encode($data['trend']['values']) !!},
                            backgroundColor: {!! json_encode($data['trend']['colors']) !!},
                            borderColor: {!! json_encode($data['trend']['colors']) !!},     
                            borderWidth: 1,
                            yAxisID: 'y'
                        },

                                    {
                                        type: 'line',
                                        label: 'Trend Line',
                                        data: {!! json_encode($data['trend']['values']) !!},
                                        fill: false,
                                        borderColor: 'rgba(25, 135, 84, 1)',
                                        borderWidth: 2,
                                        tension: 0.2,
                                        yAxisID: 'y1'
                                    }
                                ]
                            },
                            options: {
                                plugins: {
                                    datalabels: {
                                        display: (ctx) => ctx.dataset.type === 'bar',
                                        color: '#000',
                                        anchor: 'end',
                                        align: 'top',
                                        font: { weight: 'bold', size: 11 },
                                        formatter: (value) => value
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        position: 'left',
                                        title: {
                                            display: true,
                                            text: 'Quantity'
                                        }
                                    },
                                    y1: {
                                        beginAtZero: true,
                                        position: 'right',
                                        title: {
                                            display: true,
                                            text: 'Trend'
                                        },
                                        grid: { drawOnChartArea: false }
                                    }
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            },
                            plugins: [ChartDataLabels]
                        });
                    });
                </script>
            @endforeach
        </div>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const containers = document.querySelectorAll('.workstation-details');
        let currentIndex = 0;

        function showWorkstation() {
            containers.forEach((container, index) => {
                container.style.display = index === currentIndex ? 'block' : 'none';
            });
            currentIndex = (currentIndex + 1) % containers.length;
        }

        // Tampilkan workstation pertama
        if (containers.length > 0) {
            showWorkstation();
            // Ganti workstation setiap 5 detik
            if (containers.length > 1) {
                setInterval(showWorkstation, 5000);
            }
        }
    });
</script>
