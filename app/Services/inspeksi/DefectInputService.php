<?php

namespace App\Services\Inspeksi;

use App\Models\DefectInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DefectInputService
{
    public function generateUniqueCode(): string
    {
        return 'DF-' . now()->format('YmdHis') . '-' . rand(100, 999);
    }

    public function calculateFields(array $data): array
    {
        $totalCheck = (int) ($data['total_check'] ?? 0);
        $totalNg = (int) ($data['total_ng'] ?? 0);

        $data['ok'] = $data['ok'] ?? max($totalCheck - $totalNg, 0);
        $data['reject'] = $data['reject'] ?? 0;
        $data['repair'] = $data['repair'] ?? 0;

        return $data;
    }

    public function create(array $data): DefectInput
    {
        $data['id_defect'] = $this->generateUniqueCode();
        $data = $this->calculateFields($data);

        return DefectInput::create($data);
    }

    public function update(DefectInput $defectInput, array $data): DefectInput
    {
        $data = $this->calculateFields($data);
        $defectInput->update($data);

        return $defectInput;
    }

    public function getSummary(?int $year = null): \Illuminate\Support\Collection
    {
        $year = $year ?? now()->year;

        $query = DefectInput::select(
            DB::raw('MONTH(tgl) as month'),
            'departments.dept_name as dept',
            DB::raw('SUM(total_ng) as total_ng')
        )
        ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
        ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
        ->join('departments', 'workstations.id_dept', '=', 'departments.id')
        ->whereYear('defect_inputs.tgl', $year)
        ->groupBy('month', 'dept')
        ->orderBy('month', 'asc');

        $groups = $query->get();

        // Add ID and month name
        $groups->each(function ($group, $key) use ($year) {
            $group->id = $key + 1;
            $group->bulan = date('F Y', mktime(0, 0, 0, $group->month, 1, $year));
        });

        return $groups;
    }

    public function getFilteredInputs(Request $request)
    {
        $query = DefectInput::orderBy('tgl', 'desc');

        $hasDeptFilter = $request->filled('dept');
        $hasMonthFilter = $request->filled('month');

        if ($hasMonthFilter) {
            $query->whereMonth('tgl', $request->month);
        }

        if ($hasDeptFilter) {
            $query->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                  ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                  ->join('departments', 'workstations.id_dept', '=', 'departments.id')
                  ->where('departments.dept_name', $request->dept);
        }

        // Handle search if present
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('defect_inputs.id', 'like', "%$search%")
                  ->orWhere('defect_inputs.npk', 'like', "%$search%")
                  ->orWhere('defect_inputs.line', 'like', "%$search%")
                  ->orWhere('defect_inputs.shift', 'like', "%$search%")
                  ->orWhere('defect_inputs.tgl', 'like', "%$search%");
            });
        }

        // Handle pagination
        $perPage = $request->input('per_page', 5);

        return $query->with('user')
            ->when($hasDeptFilter, function ($q) {
                return $q->select('defect_inputs.*'); // Avoid column ambiguity with joins
            })
            ->paginate($perPage)->appends($request->all());
    }
}