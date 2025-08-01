<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // <-- إضافة هذا السطر
use App\Models\IndependentTeam;
use App\Models\Governorate;
use Illuminate\Http\Request;

class IndependentTeamController extends Controller
{
    public function index()
    {
        $teams = IndependentTeam::with('governorate')->latest()->paginate(20);
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $governorates = Governorate::all();
        return view('admin.teams.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:independent_teams,name',
            'governorate_id' => 'required|exists:governorates,id',
        ]);

        IndependentTeam::create($request->only('name', 'governorate_id'));

        return redirect()->route('admin.teams.index')->with('success', __('messages.team_added_success'));
    }

    public function edit(IndependentTeam $team)
    {
        $governorates = Governorate::all();
        return view('admin.teams.edit', compact('team', 'governorates'));
    }

    public function update(Request $request, IndependentTeam $team)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:independent_teams,name,' . $team->id,
            'governorate_id' => 'required|exists:governorates,id',
        ]);

        $team->update($request->only('name', 'governorate_id'));

        return redirect()->route('admin.teams.index')->with('success', __('messages.team_updated_success'));
    }

    public function destroy(IndependentTeam $team)
    {
        if ($team->strayPets()->exists()) {
            return redirect()->route('admin.teams.index')->with('error', __('messages.team_delete_has_pets_error'));
        }
        
        $team->delete();
        return redirect()->route('admin.teams.index')->with('success', __('messages.team_deleted_success'));
    }
}