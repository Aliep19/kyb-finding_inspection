<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\Department;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $targets = Target::with('department')
            ->search($request->get('search'))
            ->paginate($perPage)
            ->appends($request->all());

        $departments = Department::orderBy('dept_name')->get();

        return view('target.viewtarget', compact('targets', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'target_value' => 'required|integer|min:1',
            'start_month' => 'required|integer|between:1,12',
            'start_year' => 'required|integer|min:2000',
            'end_month' => 'required|integer|between:1,12',
            'end_year' => 'required|integer|min:2000',
        ]);

        Target::create($request->all());

        return redirect()->route('targets.index')->with('success', 'Target berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'target_value' => 'required|integer|min:1',
            'start_month' => 'required|integer|between:1,12',
            'start_year' => 'required|integer|min:2000',
            'end_month' => 'required|integer|between:1,12',
            'end_year' => 'required|integer|min:2000',
        ]);

        $target = Target::findOrFail($id);
        $target->update($request->all());

        return redirect()->route('targets.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $target = Target::findOrFail($id);
        $target->delete();

        return redirect()->route('targets.index')->with('success', 'Data berhasil dihapus.');
    }
}
