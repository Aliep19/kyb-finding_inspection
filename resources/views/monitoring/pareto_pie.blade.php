{{-- views/monitoring/pareto_pie.blade.php --}}
<x-head></x-head>

<div class="col-2">
    <div class="card mt-4 shadow-sm border-0 rounded-3" style="height: 420px;">
        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white fw-bold">
                <i class="fa fa-pie-chart me-2"></i> Repair vs Reject Ratio
            </h6>
        </div>
        <div class="card-body bg-light text-center d-flex flex-column justify-content-center">

            <!-- Pie Chart Container -->
            <div class="pie-wrapper mx-auto">
                <div class="pie-bg"></div>
                <div class="pie-fill" style="--reject: {{ $paretoProblemChartData['pie_data']['reject_ratio'] }}%;"></div>
                <div class="pie-center">
                    <div class="fw-bold fs-5 text-danger">{{ $paretoProblemChartData['pie_data']['total_ng'] }}</div>
                    <small class="text-muted">Total NG</small>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-3">
                <span class="badge bg-danger px-3 py-2 shadow-sm">
                    Reject: {{ $paretoProblemChartData['pie_data']['reject'] }} ({{ $paretoProblemChartData['pie_data']['reject_ratio'] }}%)
                </span>
                <br>
                <span class="badge px-3 py-2 shadow-sm mt-2" style="background: #002f5e; color: #fff;">
                    Repair: {{ $paretoProblemChartData['pie_data']['repair'] }} ({{ $paretoProblemChartData['pie_data']['repair_ratio'] }}%)
                </span>
            </div>
        </div>
    </div>
</div>

<style>
    .pie-wrapper {
        position: relative;
        width: 180px;
        height: 200px;
        border-radius: 50%;
        margin-top: 10px;
    }

    .pie-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: radial-gradient(circle at center, #ffffff 35%, #00008B 100%);
        box-shadow: inset 0 0 15px rgba(0,0,0,0.2), 0 4px 10px rgba(0,0,0,0.15);
    }

    .pie-fill {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: conic-gradient(
            #FF0000 0% var(--reject),
            #002f5e var(--reject) 100%
        );
        transition: all 0.8s ease-in-out;
    }

    .pie-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 8px rgba(0,0,0,0.15);
    }
</style>

<x-script></x-script>
