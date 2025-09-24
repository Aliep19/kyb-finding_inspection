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

    public function edit(DefectInput $defectInput, DefectInputDetail $detail)
    {
        $categories = DefectCategory::all();
        $subs = DefectSub::all();
        return view('defect_inputs_details.edit', compact('defectInput','detail','categories','subs'));
    }

    public function update(Request $request, DefectInput $defectInput, DefectInputDetail $detail)
    {
        $validated = $request->validate([
            'keterangan' => 'nullable|in:repair,reject,',
        ]);

        $this->service->update($detail, $validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui!']);
        }

        return redirect()->route('defect-input-details.index', $defectInput->id)
            ->with('success','Data berhasil diperbarui!');
    }
}