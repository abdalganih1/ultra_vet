<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->password) {
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', __('messages.profile_updated_success'));
    }

    public function requestTeamUpgrade(Request $request)
    {
        $user = $request->user();
        // يمكنك هنا إضافة منطق إضافي، مثل إرسال إشعار للمدير
        // حاليًا، سنقوم فقط بتغيير الدور مباشرة إذا كان المستخدم مستخدمًا عاديًا
        if ($user->isRegularUser()) {
            $user->role = 'team_request';
            $user->save();
            return redirect()->route('profile.edit')->with('success', __('messages.upgrade_request_success'));
        }
        return redirect()->route('profile.edit')->with('error', __('messages.upgrade_request_error'));
    }
}