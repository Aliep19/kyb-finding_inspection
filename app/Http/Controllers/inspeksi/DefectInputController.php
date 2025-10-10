<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\DefectInput;
use App\Models\DefectInputDetail;
use App\Models\SubWorkstation;
use App\Models\DefectCategory;
use App\Models\DefectSub;
use App\Services\Inspeksi\DefectInputService;
use App\Services\Inspeksi\DefectInputDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $year = $request->input('year', now()->year);

        $groups = $this->defectInputService->getSummary($year);

        // daftar tahun untuk dropdown (misal 5 tahun terakhir + tahun sekarang)
        $years = range(now()->year, now()->year - 5);

        return view('defect_inputs.summary', compact('groups', 'year', 'years'));
    }

    public function index(Request $request)
{
    $filter = $request->get('filter', 'today');
    $search = $request->get('search');
    $month = $request->get('month', now()->month);
    $year  = $request->get('year', now()->year);

    $query = \App\Models\DefectInput::query();

    if ($filter === 'today') {
        $query->whereDate('tgl', now()->toDateString());
    } elseif ($filter === 'all') {
        // 🔹 Filter semua data dalam bulan & tahun yang dipilih
        $query->whereMonth('tgl', $month)
              ->whereYear('tgl', $year);
    }

    // 🔎 Search
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('id', $search)
              ->orWhere('tgl', 'like', "%{$search}%")
              ->orWhere('shift', 'like', "%{$search}%")
              ->orWhere('npk', 'like', "%{$search}%")
              ->orWhere('line', 'like', "%{$search}%")
              ->orWhere('marking_number', 'like', "%{$search}%")
              ->orWhere('lot', 'like', "%{$search}%");
        });
    }

    $inputs = $query->with(['details.sub'])->latest()->paginate(10);

    return view('defect_inputs.index', compact('inputs', 'filter', 'search', 'month', 'year'));
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

    // Validate DefectInputDetail data (tanpa keterangan)
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

    // Ambil semua detail yang sudah ada untuk defect_input_id ini
    $existingDetails = $defectInput->details()->get()->keyBy(function ($item) {
        return $item->defect_category_id . '-' . $item->defect_sub_id; // Key unik berdasarkan kombinasi category dan sub
    });

    // Proses detail baru dari input
    $processedKeys = [];
    if (!empty($validatedDetails['defect_category_id'])) {
        foreach ($validatedDetails['defect_category_id'] as $index => $categoryId) {
            if (empty($categoryId) || empty($validatedDetails['defect_sub_id'][$index]) || !isset($validatedDetails['jumlah_defect'][$index])) {
                continue;
            }

            $key = $categoryId . '-' . $validatedDetails['defect_sub_id'][$index];
            $processedKeys[] = $key;

            $detailData = [
                'defect_input_id' => $defectInput->id,
                'defect_category_id' => $categoryId,
                'defect_sub_id' => $validatedDetails['defect_sub_id'][$index],
                'jumlah_defect' => $validatedDetails['jumlah_defect'][$index] ?? 0,
                // Tidak menyertakan keterangan agar tidak diubah
            ];

            // Jika detail sudah ada, update hanya jumlah_defect; jika tidak, buat baru
            if (isset($existingDetails[$key])) {
                $this->defectInputDetailService->update($existingDetails[$key], [
                    'jumlah_defect' => $detailData['jumlah_defect'],
                ]);
            } else {
                $this->defectInputDetailService->create($detailData);
            }
        }
    }

    // Hapus detail yang tidak ada di input baru
    foreach ($existingDetails as $key => $detail) {
        if (!in_array($key, $processedKeys)) {
            $detail->delete();
        }
    }

    return redirect()->route('defect-inputs.summary')
        ->with('success', 'Data berhasil diperbarui!');
}
public function uploadPica(Request $request, DefectInput $defectInput, DefectInputDetail $detail)
{
    try {
        $request->validate([
            'pica' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $detail->refresh();

        $lockMinutes = 30; // Normal: 30. Testing: 1

        if (!$detail->canEditPica($lockMinutes)) {
            throw ValidationException::withMessages([
                'pica' => 'PICA terkunci setelah 30 menit dari upload awal. Edit tidak dapat dilakukan.'
            ]);
        }

        // Hapus lama
        if ($detail->pica) {
            Storage::disk('public')->delete($detail->pica);
        }

        // Upload baru
        $path = $request->file('pica')->store('pica', 'public');

        $detail->pica = $path;
        $detail->pica_uploaded_at = now(); // Auto timezone
        $detail->save();

        return back()->with('success', 'PICA berhasil diupload dan diperbarui!');
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }

}

public function deletePica(DefectInput $defectInput, DefectInputDetail $detail)
{
    try {
        $detail->refresh();

        $lockMinutes = 30; // Sama

        if (!$detail->canEditPica($lockMinutes)) {
            throw ValidationException::withMessages([
                'pica' => 'PICA terkunci setelah 30 menit dari upload awal. Hapus tidak dapat dilakukan.'
            ]);
        }

        // Hapus file
        if ($detail->pica) {
            Storage::disk('public')->delete($detail->pica);
        }

        $detail->pica = null;
        $detail->save(); // Timestamp tetep

        return back()->with('success', 'PICA berhasil dihapus!');
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }
}


    public function destroy(DefectInput $defectInput)
    {
        $defectInput->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
