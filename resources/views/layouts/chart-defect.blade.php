
<div class="card shadow-sm">
    <div class="card-header pb-2">
        <h6 class="mb-0">Final Inspection 4W Finding {{ date('Y') }}</h6>
        <!-- Dropdown department -->
        <div class="mt-2">
            <select id="departmentFilter" class="form-select" style="width: 200px;">
                <option value="">Departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ $defaultDepartment && $defaultDepartment->id == $department->id ? 'selected' : '' }}>
                        {{ $department->dept_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body">
        <div style="height:300px;">
            <canvas id="findingChart"></canvas>
        </div>
    </div>
</div>

<x-script></x-script>

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
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'FI 4W Finding (Pcs)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tahun {{ date("Y") }}'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    datalabels: {
                        color: '#000',
                        anchor: 'end',
                        align: 'end',
                        font: {
                            weight: 'bold'
                        },
                        formatter: function (value) {
                            return value !== null ? value : '';
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Event listener untuk dropdown
        document.getElementById('departmentFilter').addEventListener('change', function () {
            var departmentId = this.value;

            fetch('/chart-data' + (departmentId ? '/' + departmentId : ''), {
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
            .catch(error => console.error('Error fetching chart data:', error));
        });
    });
</script>

