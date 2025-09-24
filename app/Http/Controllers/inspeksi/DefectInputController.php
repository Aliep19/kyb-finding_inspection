<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\DefectInput;
use App\Models\SubWorkstation;
use App\Models\DefectCategory;
use App\Models\DefectSub;
use App\Services\Inspeksi\DefectInputService;
use App\Services\Inspeksi\DefectInputDetailService;
use Illuminate\Http\Request;

class DefectInputController extends Controller
{
    protected $defectInputService;
    protected $defectInputDetailService;

    public function __construct(DefectInputService $defectInputService, DefectInputDetailService $defectInputDetailService)
    {
        $this->defectInputService = $defectInputService;
        $this->defectInputDetailService = $defectInputDetailService;
    }
    
    public function summary(Request $request)
    {
        $groups = $this->defectInputService->getSummary();

        return view('defect_inputs.summary', compact('groups'));
    }

    public function index(Request $request)
    {
        $inputs = $this->defectInputService->getFilteredInputs($request);

        return view('defect_inputs.index', compact('inputs'));
    }

    public function create()
    {
        $lines = SubWorkstation::all();
        $categories = DefectCategory::all();
        $subsByCategory = DefectSub::all()->groupBy('id_defect_category');

        return view('defect_inputs.create', compact('lines', 'categories', 'subsByCategory'));
    }

    public function store(Request $request)
    {
        // Validate DefectInput data
        $validatedInput = $request->validate([
            'tgl'   => 'required|date',
            'shift' => 'required|string',
            'npk'   => 'required|string',
            'line'  => 'required|string',
            'marking_number' => 'nullable|string',
            'lot'   => 'nullable|string',
            'kayaba_no' => 'nullable|string',
            'total_check' => 'required|integer|min:0',
            'ok'          => 'nullable|integer|min:0',
            'reject'      => 'nullable|integer|min:0',
            'repair'      => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',

        ]);

        // Validate DefectInputDetail data
        $validatedDetails = $request->validate([
            'defect_category_id' => 'nullable|array',
            'defect_category_id.*' => 'nullable|exists:defect_categories,id',
            'defect_sub_id' => 'nullable|array',
            'defect_sub_id.*' => 'nullable|exists:defect_subs,id',
            'jumlah_defect' => 'nullable|array',
            'jumlah_defect.*' => 'nullable|integer|min:0',
        ]);

        // Calculate total_ng from jumlah_defect, default to 0 if empty
        $totalNg = !empty($validatedDetails['jumlah_defect']) && is_array($validatedDetails['jumlah_defect'])
            ? array_sum(array_filter($validatedDetails['jumlah_defect'], fn($value) => is_numeric($value)))
            : 0;
        $validatedInput['total_ng'] = $totalNg;

        // Validate total_ng <= total_check
        if ($totalNg > $validatedInput['total_check']) {
            return back()->withErrors(['total_ng' => 'Total NG tidak boleh melebihi Total Check!'])->withInput();
        }

        // Validate reject + repair = total_ng
        $reject = $validatedInput['reject'] ?? 0;
        $repair = $validatedInput['repair'] ?? 0;
        if ($reject + $repair !== $totalNg) {
            return back()->withErrors(['reject' => 'Total Reject + Repair harus sama dengan Total NG (' . $totalNg . ')!'])->withInput();
        }

        // Save DefectInput
        $defectInput = $this->defectInputService->create($validatedInput);

        // Save DefectInputDetail only if defect_category_id is not empty and valid
        if (!empty($validatedDetails['defect_category_id'])) {
            foreach ($validatedDetails['defect_category_id'] as $index => $categoryId) {
                // Skip if categoryId or subId is empty or jumlah_defect is not valid
                if (empty($categoryId) || empty($validatedDetails['defect_sub_id'][$index]) || !isset($validatedDetails['jumlah_defect'][$index])) {
                    continue;
                }

                $detailData = [
                    'defect_input_id' => $defectInput->id,
                    'defect_category_id' => $categoryId,
                    'defect_sub_id' => $validatedDetails['defect_sub_id'][$index],
                    'jumlah_defect' => $validatedDetails['jumlah_defect'][$index] ?? 0,
                ];

                $this->defectInputDetailService->create($detailData);
            }
        }

        return redirect()->route('defect-inputs.summary')
            ->with('success', 'Defect Input dan Details berhasil ditambahkan!');
    }

    public function edit(DefectInput $defectInput)
    {
        $lines = SubWorkstation::all();
        $categories = DefectCategory::all();
        $subsByCategory = DefectSub::all()->groupBy('id_defect_category');

        return view('defect_inputs.edit', compact('defectInput', 'lines', 'categories', 'subsByCategory'));
    }

    public function update(Request $request, DefectInput $defectInput)
    {
        // Validate DefectInput data
        $validatedInput = $request->validate([
            'tgl'   => 'required|date',
            'shift' => 'required|string',
            'npk'   => 'required|string',
            'line'  => 'required|string',
            'marking_number' => 'nullable|string',
            'lot'   => 'nullable|string',
            'kayaba_no' => 'nullable|string',
            'total_check' => 'required|integer|min:0',
            'ok'          => 'nullable|integer|min:0',
            'reject'      => 'nullable|integer|min:0',
            'repair'      => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Validate DefectInputDetail data
        $validatedDetails = $request->validate([
            'defect_category_id' => 'nullable|array',
            'defect_category_id.*' => 'nullable|exists:defect_categories,id',
            'defect_sub_id' => 'nullable|array',
            'defect_sub_id.*' => 'nullable|exists:defect_subs,id',
            'jumlah_defect' => 'nullable|array',
            'jumlah_defect.*' => 'nullable|integer|min:0',
        ]);

        // Calculate total_ng from jumlah_defect
        $totalNg = !empty($validatedDetails['jumlah_defect']) && is_array($validatedDetails['jumlah_defect'])
            ? array_sum(array_filter($validatedDetails['jumlah_defect'], fn($value) => is_numeric($value)))
            : 0;
        $validatedInput['total_ng'] = $totalNg;

        // Validate total_ng <= total_check
        if ($totalNg > $validatedInput['total_check']) {
            return back()->withErrors(['total_ng' => 'Total NG tidak boleh melebihi Total Check!'])->withInput();
        }

        // Validate reject + repair = total_ng
        $reject = $validatedInput['reject'] ?? 0;
        $repair = $validatedInput['repair'] ?? 0;
        if ($reject + $repair !== $totalNg) {
            return back()->withErrors(['reject' => 'Total Reject + Repair harus sama dengan Total NG (' . $totalNg . ')!'])->withInput();
        }

        // Update DefectInput
        $this->defectInputService->update($defectInput, $validatedInput);

        // Delete existing details and recreate
        $defectInput->details()->delete();
        if (!empty($validatedDetails['defect_category_id'])) {
            foreach ($validatedDetails['defect_category_id'] as $index => $categoryId) {
                if (empty($categoryId) || empty($validatedDetails['defect_sub_id'][$index]) || !isset($validatedDetails['jumlah_defect'][$index])) {
                    continue;
                }

                $detailData = [
                    'defect_input_id' => $defectInput->id,
                    'defect_category_id' => $categoryId,
                    'defect_sub_id' => $validatedDetails['defect_sub_id'][$index],
                    'jumlah_defect' => $validatedDetails['jumlah_defect'][$index] ?? 0,
                ];

                $this->defectInputDetailService->create($detailData);
            }
        }

        return redirect()->route('defect-inputs.summary')
            ->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(DefectInput $defectInput)
    {
        $defectInput->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}