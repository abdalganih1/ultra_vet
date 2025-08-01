<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StrayPet;

class CheckPetDataEntryAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        /** @var StrayPet $strayPet */
        $strayPet = $request->route('stray_pet');

        // Admin has access to everything.
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Data entry can access if they belong to the same team.
        if ($user->role === 'data_entry' && $user->independent_team_id == $strayPet->independent_team_id) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}