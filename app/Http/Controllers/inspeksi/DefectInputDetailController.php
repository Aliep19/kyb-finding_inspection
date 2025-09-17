<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\DefectInput;
use App\Models\DefectInputDetail;
use App\Models\DefectCategory;
use App\Models\DefectSub;
use App\Services\Inspeksi\DefectInputDetailService;
use Illuminate\Http\Request;

class DefectInputDetailController extends Controller
{
    protected $service;

    public function __construct(DefectInputDetailService $service)
    {
        $this->service = $service;
    }

    public function index(DefectInput $defectInput)
    {
        $details = DefectInputDetail::with(['category','sub'])
            ->where('defect_input_id', $defectInput->id)
            ->paginate(10);

        return view('defect_inputs_details.index', compact('details','defectInput'));
    }

    public function create(DefectInput $defectInput)
    {
        $categories = DefectCategory::all();
        $subs = DefectSub::all();
        return view('defect_inputs_details.create', compact('defectInput','categories','subs'));
    }

    public function store(Request $request, DefectInput $defectInput)
    {
        $validated = $request->validate([
            'defect_category_id' => 'required|array',
            'defect_category_id.*' => 'exists:defect_categories,id',
            'defect_sub_id' => 'required|array',
            'defect_sub_id.*' => 'exists:defect_subs,id',
            'jumlah_defect' => 'required|array',
            'jumlah_defect.*' => 'integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        foreach ($validated['defect_category_id'] as $index => $categoryId) {
            $data = [
                'defect_input_id' => $defectInput->id,
                'defect_category_id' => $categoryId,
                'defect_sub_id' => $validated['defect_sub_id'][$index],
                'jumlah_defect' => $validated['jumlah_defect'][$index],
                'keterangan' => $validated['keterangan'] ?? null,
            ];

            $this->service->create($data);
        }

        return redirect()
            ->route('defect-input-details.index', $defectInput->id)
            ->with('success', 'Beberapa defect berhasil ditambahkan!');
    }


    public function edit(DefectInput $defectInput, DefectInputDetail $detail)
    {
        $categories = DefectCategory::all();
        $subs = DefectSub::all();
        return view('defect_inputs_details.edit', compact('defectInput','detail','categories','subs'));
    }

    public function update(Request $request, DefectInput $defectInput, DefectInputDetail $detail)
    {
        $validated = $request->validate([
            'defect_category_id' => 'required|exists:defect_categories,id',
            'defect_sub_id'      => 'required|exists:defect_subs,id',
            'jumlah_defect'      => 'required|integer|min:0',
            'keterangan'         => 'nullable|string',
        ]);

        $this->service->update($detail,$validated);

        return redirect()->route('defect-input-details.index', $defectInput->id)
            ->with('success','Data berhasil diperbarui!');
    }

    public function destroy(DefectInput $defectInput, DefectInputDetail $detail)
    {
        // pastikan detail memang milik defectInput (safety)
        if ($detail->defect_input_id != $defectInput->id) {
            return redirect()->route('defect-input-details.index', $defectInput->id)
                ->with('error', 'Detail tidak ditemukan untuk header ini.');
        }

        $detail->delete();
        return redirect()->route('defect-input-details.index', $defectInput->id)
            ->with('success','Data berhasil dihapus!');
    }

}
