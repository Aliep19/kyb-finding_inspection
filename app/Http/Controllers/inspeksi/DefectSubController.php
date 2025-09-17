<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\DefectSub;
use App\Models\DefectCategory;
use Illuminate\Http\Request;

class DefectSubController extends Controller
{
public function index(Request $request)
{
    // search kalau ada
    $query = DefectCategory::withCount('subs')->orderBy('defect_name');

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('defect_name', 'like', "%$search%");
    }

    // handle entries (default 10)
    $perPage = $request->input('per_page', 10);

    $categories = $query->paginate($perPage)->appends($request->all());


    return view('defect_sub.index', compact('categories'));
}


    // VIEW CREATE: form input jenis defect (terpisah)
    public function create(Request $request)
    {
        $categories = DefectCategory::orderBy('defect_name')->get();
        // optional: bisa preselect category lewat query ?category_id=..
        $selectedCategory = $request->query('category_id', null);
        return view('defect_sub.create', compact('categories', 'selectedCategory'));
    }

    // STORE: simpan defect_sub
    public function store(Request $request)
    {
        $request->validate([
            'id_defect_category' => 'required|exists:defect_categories,id',
            'jenis_defect' => 'required|string|max:255',
        ]);

        DefectSub::create($request->only('id_defect_category', 'jenis_defect'));

        return redirect()->route('defect-subs.index')->with('success', 'Jenis defect berhasil ditambahkan.');
    }

    // SUBS PER CATEGORY: tampilan semua jenis defect untuk 1 kategori
    public function subsByCategory($categoryId)
    {
        $category = DefectCategory::with('subs')->findOrFail($categoryId);
        $subs = $category->subs;
        return view('defect_sub.subs', compact('category', 'subs'));
    }

    // EDIT view (untuk satu defect_sub)
    public function edit($id)
    {
        $sub = DefectSub::findOrFail($id);
        $categories = DefectCategory::orderBy('defect_name')->get();
        return view('defect_sub.edit', compact('sub', 'categories'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_defect_category' => 'required|exists:defect_categories,id',
            'jenis_defect' => 'required|string|max:255',
        ]);

        $sub = DefectSub::findOrFail($id);
        $sub->update($request->only('id_defect_category', 'jenis_defect'));

        return redirect()->route('defect-subs.byCategory', $sub->id_defect_category)
                         ->with('success', 'Data berhasil diperbarui.');
    }

    // DESTROY
    public function destroy($id)
    {
        $sub = DefectSub::findOrFail($id);
        $categoryId = $sub->id_defect_category;
        $sub->delete();

        return redirect()->route('defect-subs.byCategory', $categoryId)
                         ->with('success', 'Data berhasil dihapus.');
    }
}
