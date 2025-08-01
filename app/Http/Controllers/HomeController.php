<?php

namespace App\Http\Controllers;

use App\Models\StrayPet;
use App\Models\IndependentTeam;
use App\Models\User;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isDataEntry()) {
            return $this->teamDashboard($user);
        }

        if ($user->role === 'team_request') {
            return view('auth.pending_approval');
        }

        // For regular users, redirect to their profile
        return redirect()->route('profile.edit');
    }

    private function adminDashboard()
    {
        $stats = [
            'users' => User::count(),
            'teams' => IndependentTeam::count(),
            'governorates' => Governorate::count(),
            'pets' => StrayPet::count(),
            'pets_data_entered' => StrayPet::where('data_entered_status', true)->count(),
        ];

        // Corrected team stats query
        $teamStats = Governorate::with(['independentTeams' => function ($query) {
            $query->withCount('strayPets');
        }])->get();

        return view('admin.dashboard', compact('stats', 'teamStats'));
    }

    private function teamDashboard(User $user)
    {
        $team = $user->independentTeam;

        if (!$team) {
            // Handle case where data entry user is not assigned to a team
            return redirect()->route('profile.edit')->with('error', 'You are not assigned to any team.');
        }

        $stats = [
            'total_pets' => $team->strayPets()->count(),
            'data_entered' => $team->strayPets()->where('data_entered_status', true)->count(),
            'team_members' => $team->users()->count(),
        ];

        $recent_pets = $team->strayPets()->latest()->take(10)->get();

        return view('team.dashboard', compact('team', 'stats', 'recent_pets'));
    }
}
