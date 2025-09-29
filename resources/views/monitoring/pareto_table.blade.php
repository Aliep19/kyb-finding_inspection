{{-- views/monitoring/pareto_table.blade.php --}}
<x-head></x-head>

<div class="col-5">
    <div class="card mt-4 shadow-sm border-0 rounded-3" style="height: 420px;">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white fw-bold">
                <i class="fa fa-table me-2"></i> PARETO FI 4W Finding
            </h6>
        </div>
        <div class="card-body bg-light flex-grow-1 d-flex flex-column p-0">
            <div class="table-responsive" style="max-height: 340px; overflow-y:auto;">
                <table class="table table-sm mb-0 align-middle w-100" style="border-collapse: separate;border-block-color: #000000; border-spacing: 0;">
                    <thead class="text-dark sticky-top" style="background: #ea7500;">
                        <tr class="text-white">
                            <th class="text-center py-2" style="width: 12%;">Rank</th>
                            <th class="py-2" style="width: 48%;">Problem Total</th>
                            <th class="text-center py-2" style="width: 20%;">Q'TY</th>
                            <th class="text-center py-2" style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paretoProblemChartData['table_data'] as $row)
                            <tr style="transition: background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='transparent'">
                                <td class="text-center fw-bold text-secondary py-2">{{ $row['rank'] }}</td>
                                <td class="text-start text-wrap py-2 px-2" style="word-break: break-word; white-space: normal; color:#333;">
                                    {{ $row['problem'] }}
                                </td>
                                <td class="text-center fw-semibold py-2 text-primary">{{ $row['qty'] }}</td>
                                <td class="text-center py-2">
                                    <span class="badge px-3 py-2 rounded-pill shadow-sm
                                        @if(strtolower($row['status']) === 'repair') bg-warning text-dark
                                        @elseif(strtolower($row['status']) === 'reject') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ strtoupper($row['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light border-top">
                            <td colspan="4" class="text-end pe-3 py-2 text-muted">
                                Total Check : <span class="text-dark">{{ number_format($paretoProblemChartData['total_check']) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-script></x-script>
