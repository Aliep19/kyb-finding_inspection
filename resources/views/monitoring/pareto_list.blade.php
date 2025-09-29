{{-- views/monitoring/pareto_list.blade.php --}}
<x-head></x-head>
<div class="col-5">
    <div class="card mt-4 shadow-sm border-0 rounded-3" style="height: 420px;">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-white">
                <i class="fa fa-list me-2"></i> List Pareto FI Finding by Problem
            </h6>
        </div>
        <div class="card-body bg-light flex-grow-1 d-flex flex-column overflow-auto">

            <!-- List Container -->
            <div class="list-group list-group-flush">
                @foreach($paretoProblemChartData['list_data'] as $item)
                    <div class="list-group-item d-flex align-items-center justify-content-between rounded-3 shadow-sm mb-2 custom-item">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger rounded-circle me-3" style="width: 14px; height: 14px;"></span>
                            <span class="fw-semibold text-dark">{{ $item }}</span>
                        </div>
                        <i class="fa fa-exclamation-circle text-danger"></i>
                    </div>
                @endforeach
            </div>

            <!-- Group Section -->
            <div class="mt-3">
                <h6 class="fw-bold text-secondary">Groups</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($paretoProblemChartData['groups'] as $group)
                        <span class="badge bg-primary shadow-sm px-3 py-2">{{ $group }}</span>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .custom-item {
        background: #fff;
        transition: all 0.3s ease;
        border: none;
    }

    .custom-item:hover {
        background: #f8f9fa;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<x-script></x-script>
