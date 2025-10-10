<x-head></x-head>
<style>
    .pica-container {
        height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 5px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        gap: 10px;
    }

    .defect-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        max-width: 32%;
    }

    .defect-item h6 {
        margin-bottom: 8px;
        font-size: 0.75rem;
        font-weight: bold;
        text-align: center;
        color: #333;
        text-transform: uppercase;
        line-height: 1.2;
        min-height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .defect-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        border-radius: 6px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border: 2px solid #ddd;
    }

    .defect-item img:hover {
        transform: scale(1.08);
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        border-color: #dc3545;
    }

    .defect-item p {
        margin-top: 8px;
        font-size: 0.7rem;
        color: #999;
        text-align: center;
    }

    /* Styling untuk no image */
    .no-image-placeholder {
        width: 100%;
        height: 150px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px dashed #ccc;
    }

    .no-image-placeholder p {
        color: #999;
        font-size: 0.8rem;
        margin: 0;
    }

    @media (max-width: 768px) {
        .pica-container {
            flex-wrap: wrap;
            height: auto;
            max-height: 400px;
        }
        .defect-item {
            max-width: 48%;
            margin-bottom: 10px;
        }
        .defect-item img {
            height: 120px;
        }
        .no-image-placeholder {
            height: 120px;
        }
    }

    @media (max-width: 576px) {
        .defect-item {
            max-width: 100%;
        }
        .defect-item img {
            height: 140px;
        }
        .no-image-placeholder {
            height: 140px;
        }
    }
</style>

<div class="row">
    <div class="col-6">
        <div class="card mt-4 shadow-sm border-0 rounded-3">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-white fw-bold">
                    <i class="fa fa-chart-bar me-2"></i>
                    <span id="paretoAssemblingTitle">TOP 3 Pareto</span>
                </h6>
            </div>
            <div class="card-body bg-light flex-grow-1 d-flex flex-column">
                <div class="chart-container" style="height: 200px;">
                    <canvas id="paretoAssemblingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card mt-4 shadow-sm border-0 rounded-3">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-white fw-bold">
                    <i class="fa fa-images me-2"></i>
                    <span id="picaTitle">TOP 3 PICA Images</span>
                </h6>
            </div>
            <div class="card-body bg-light flex-grow-1 d-flex flex-column p-2">
                <div id="picaContainer" class="pica-container">
                    <!-- PICA images will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<x-script></x-script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elemen DOM
        const ctx = document.getElementById('paretoAssemblingChart').getContext('2d');
        const titleElement = document.getElementById('paretoAssemblingTitle');
        const picaTitleElement = document.getElementById('picaTitle');
        const picaContainer = document.getElementById('picaContainer');
        const chartData = {!! json_encode($paretoAssemblingChartData) !!};
        const workstations = Object.keys(chartData);
        let currentIndex = 0;

        // Fungsi untuk memeriksa apakah warna gelap
        function isDarkColor(hexColor) {
            let c = hexColor.replace('#', '');
            if (c.length === 8) c = c.substring(0, 6);
            const rgb = parseInt(c, 16);
            const r = (rgb >> 16) & 0xff;
            const g = (rgb >> 8) & 0xff;
            const b = (rgb >> 0) & 0xff;
            const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
            return luminance < 140;
        }

        // Inisialisasi Chart
        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData[workstations[currentIndex]].labels,
                datasets: [{
                    label: 'FI Findings (Pcs)',
                    data: chartData[workstations[currentIndex]].values,
                    backgroundColor: chartData[workstations[currentIndex]].colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'FI Findings (Pcs)'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true },
                    datalabels: {
                        color: function (context) {
                            const bgColor = context.dataset.backgroundColor[context.dataIndex];
                            return isDarkColor(bgColor) ? '#fff' : '#000';
                        },
                        anchor: 'center',
                        align: 'center',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value) => value
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Fungsi untuk memperbarui PICA images - tampil 3 bersamaan
        function updatePica() {
            const data = chartData[workstations[currentIndex]];
            picaContainer.innerHTML = ''; // Hapus konten sebelumnya
            picaTitleElement.textContent = 'TOP 3 PICA Images ' + data.title.replace('TOP 3 Pareto ', '').toUpperCase();

            data.labels.forEach((label, index) => {
                const images = data.images[index] || [];
                const defectDiv = document.createElement('div');
                defectDiv.className = 'defect-item';

                const defectTitle = document.createElement('h6');
                defectTitle.textContent = label;
                defectDiv.appendChild(defectTitle);

                if (images.length > 0) {
                    const img = document.createElement('img');
                    img.src = images[0]; // Gunakan URL publik dari backend
                    img.alt = label;
                    img.onclick = function() {
                        // Buka gambar dalam tab baru saat diklik
                        window.open(this.src, '_blank');
                    };
                    img.onerror = function() {
                        // Fallback jika gambar gagal dimuat
                        const placeholder = document.createElement('div');
                        placeholder.className = 'no-image-placeholder';
                        placeholder.innerHTML = '<p>Image not available</p>';
                        this.parentNode.replaceChild(placeholder, this);
                    };
                    defectDiv.appendChild(img);
                } else {
                    const noImgDiv = document.createElement('div');
                    noImgDiv.className = 'no-image-placeholder';
                    noImgDiv.innerHTML = '<p>No image available</p>';
                    defectDiv.appendChild(noImgDiv);
                }

                picaContainer.appendChild(defectDiv);
            });
        }

        // Fungsi untuk memperbarui Chart dan PICA
        function updateChart() {
            currentIndex = (currentIndex + 1) % workstations.length;
            const data = chartData[workstations[currentIndex]];
            titleElement.textContent = data.title;
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.values;
            chart.data.datasets[0].backgroundColor = data.colors;
            chart.update();
            updatePica();
        }

        // Inisialisasi PICA pertama
        updatePica();

        // Rotasi Chart dan PICA setiap 5 detik jika lebih dari 1 workstation
        if (workstations.length > 1) {
            setInterval(updateChart, 5000);
        }
    });
</script>
