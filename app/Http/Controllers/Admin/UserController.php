<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Governorate;
use App\Models\IndependentTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('governorate', 'independentTeam')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $governorates = Governorate::all();
        $teams = IndependentTeam::all();
        return view('admin.users.create', compact('governorates', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['admin', 'data_entry', 'user', 'team_request'])],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'independent_team_id' => ['nullable', 'exists:independent_teams,id'],
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'governorate_id' => $request->governorate_id,
            'independent_team_id' => $request->independent_team_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', __('messages.user_added_success'));
    }

    public function edit(User $user)
    {
        $governorates = Governorate::all();
        $teams = IndependentTeam::all();
        return view('admin.users.edit', compact('user', 'governorates', 'teams'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'data_entry', 'user', 'team_request'])],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'independent_team_id' => ['nullable', 'exists:independent_teams,id'],
        ]);

        $data = $request->only('name', 'username', 'email', 'role', 'governorate_id', 'independent_team_id');

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', __('messages.user_updated_success'));
    }

    public function destroy(User $user)
    {
        // Prevent deleting the last admin or oneself
        if ($user->isAdmin() && User::where('role', 'admin')->count() === 1) {
            return back()->with('error', __('messages.user_delete_last_admin_error'));
        }
        if ($user->id === auth()->id()) {
            return back()->with('error', __('messages.user_delete_self_error'));
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', __('messages.user_deleted_success'));
    }
}