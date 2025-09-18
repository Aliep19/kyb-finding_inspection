<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\DefectInput;
use App\Models\SubWorkstation;
use App\Services\Inspeksi\DefectInputService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DefectInputController extends Controller
{
    protected $service;

    public function __construct(DefectInputService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = DefectInput::orderBy('id', 'asc');

    // Handle search kalau ada
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%$search%")
            ->orWhere('npk', 'like', "%$search%")
            ->orWhere('line', 'like', "%$search%")
            ->orWhere('shift', 'like', "%$search%")
            ->orWhere('tgl', 'like', "%$search%");
            // tambahin field lain sesuai tabel DefectInput
        });
    }
        // handle entries (default 10)
        $perPage = $request->input('per_page', 10);

        $inputs = $query->with('user')->paginate($perPage)->appends($request->all());
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
