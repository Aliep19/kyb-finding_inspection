<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\DefectInput;
use App\Models\SubWorkstation;
use App\Services\Inspeksi\DefectInputService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\DB;
>>>>>>> chart-dashboard

class DefectInputController extends Controller
{
    protected $service;

    public function __construct(DefectInputService $service)
    {
        $this->service = $service;
    }

    public function summary(Request $request)
    {
        $currentYear = date('Y');

        $query = DefectInput::select(
            DB::raw('MONTH(tgl) as month'),
            'departments.dept_name as dept',
            DB::raw('SUM(total_ng) as total_ng')
        )
        ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
        ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
        ->join('departments', 'workstations.id_dept', '=', 'departments.id')
        ->whereYear('defect_inputs.tgl', $currentYear)
        ->groupBy('month', 'dept')
        ->orderBy('month', 'asc');

        $groups = $query->get();

        // Tambahkan ID manual dan nama bulan
        $groups->each(function ($group, $key) use ($currentYear) {
            $group->id = $key + 1;
            $group->bulan = date('F Y', mktime(0, 0, 0, $group->month, 1, $currentYear));
        });

        return view('defect_inputs.summary', compact('groups'));
    }

    public function index(Request $request)
    {
        $query = DefectInput::orderBy('id', 'asc');

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

        // Handle search kalau ada
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('defect_inputs.id', 'like', "%$search%")
                  ->orWhere('defect_inputs.npk', 'like', "%$search%")
                  ->orWhere('defect_inputs.line', 'like', "%$search%")
                  ->orWhere('defect_inputs.shift', 'like', "%$search%")
                  ->orWhere('defect_inputs.tgl', 'like', "%$search%");
                // tambahin field lain sesuai tabel DefectInput
            });
        }

        // handle entries (default 10)
        $perPage = $request->input('per_page', 10);

<<<<<<< HEAD
        $inputs = $query->with('user')->paginate($perPage)->appends($request->all());
=======
        $inputs = $query->with('user')
            ->when($hasDeptFilter, function ($q) {
                return $q->select('defect_inputs.*'); // Hindari ambiguitas kolom jika ada join
            })
            ->paginate($perPage)->appends($request->all());

>>>>>>> chart-dashboard
        // dd(Auth::user());
        return view('defect_inputs.index', compact('inputs'));
    }

    public function create()
    {
        $lines = SubWorkstation::all();
        return view('defect_inputs.create', compact('lines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl'   => 'required|date',
            'shift' => 'required|string',
            'npk'   => 'required|string',
            'line'  => 'required|string',
            'marking_number' => 'nullable|string',
            'lot'   => 'nullable|string',
            'kayaba_no' => 'nullable|string',
            'total_check' => 'required|integer|min:0',
            'total_ng'    => 'required|integer|min:0',
            'ok'          => 'nullable|integer|min:0',
            'reject'      => 'nullable|integer|min:0',
            'repair'      => 'nullable|integer|min:0',
        ]);

        $this->service->create($validated);

        return redirect()->route('defect-inputs.index')
            ->with('success', 'Defect Input berhasil ditambahkan!');
    }

    public function edit(DefectInput $defectInput)
    {
        $lines = SubWorkstation::all();
        return view('defect_inputs.edit', compact('defectInput','lines'));
    }

    public function update(Request $request, DefectInput $defectInput)
    {
        $validated = $request->validate([
            'tgl'   => 'required|date',
            'shift' => 'required|string',
            'npk'   => 'required|string',
            'line'  => 'required|string',
            'marking_number' => 'nullable|string',
            'lot'   => 'nullable|string',
            'kayaba_no' => 'nullable|string',
            'total_check' => 'required|integer|min:0',
            'total_ng'    => 'required|integer|min:0',
            'ok'          => 'nullable|integer|min:0',
            'reject'      => 'nullable|integer|min:0',
            'repair'      => 'nullable|integer|min:0',
        ]);

        $this->service->update($defectInput, $validated);

        return redirect()->route('defect-inputs.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(DefectInput $defectInput)
    {
        $defectInput->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
