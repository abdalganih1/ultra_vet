<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::withCount('independentTeams')->latest()->paginate(20);
        return view('admin.governorates.index', compact('governorates'));
    }

    public function create()
    {
        return view('admin.governorates.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:governorates']);
        Governorate::create($request->only('name'));
        return redirect()->route('admin.governorates.index')->with('success', __('messages.governorate_added_success'));
    }

    public function edit(Governorate $governorate)
    {
        return view('admin.governorates.edit', compact('governorate'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $request->validate(['name' => 'required|string|max:255|unique:governorates,name,' . $governorate->id]);
        $governorate->update($request->only('name'));
        return redirect()->route('admin.governorates.index')->with('success', __('messages.governorate_updated_success'));
    }

    public function destroy(Governorate $governorate)
    {
        // Add logic to prevent deletion if teams are associated with it
        if ($governorate->independentTeams()->count() > 0) {
            return back()->with('error', 'Cannot delete a governorate that has teams assigned to it.');
        }
        $governorate->delete();
        return redirect()->route('admin.governorates.index')->with('success', __('messages.governorate_deleted_success'));
    }
}