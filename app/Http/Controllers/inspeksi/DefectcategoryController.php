<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Defectcategory;
use Illuminate\Http\Request;

class DefectcategoryController extends Controller
{
public function index(Request $request)
{
    $query = Defectcategory::orderBy('id', 'asc');

    // Handle search kalau ada
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('defect_name', 'like', "%$search%");
    }

    // Handle entries (default 10)
    $perPage = $request->input('per_page', 10);

    $defcategories = $query->paginate($perPage)->appends($request->all());

    return view('defect.viewdefect', compact('defcategories'));
}



    public function create()
    {
        return view('defect.createdefect');
    }

        public function store(Request $request)
        {
            $request->validate([
                'defect_name' => 'required|string|max:255',
                'jenis_defect' => 'required|in:0,1', // hanya boleh 0 atau 1
            ]);

            Defectcategory::create($request->only('defect_name', 'jenis_defect'));

            return redirect()->route('defect.index')->with('success', 'Defect Category berhasil ditambahkan');
        }

    public function edit(Defectcategory $defect)
    {
        return view('defect.edit', compact('defect'));
    }

    public function update(Request $request, Defectcategory $defect)
    {
        $request->validate([
            'defect_name' => 'required|string|max:255',
            'jenis_defect' => 'required|in:0,1',
        ]);

        $defect->update($request->only('defect_name', 'jenis_defect'));

        return redirect()->route('defect.index')->with('success', 'Defect Category berhasil diupdate');
    }


    public function destroy(Defectcategory $defect)
    {
        $defect->delete();

        return redirect()->route('defect.index')->with('success', 'Data berhasil dihapus');
    }

}
