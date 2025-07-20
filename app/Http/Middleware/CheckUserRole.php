<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // إذا لم يكن المستخدم مسجلاً دخوله، ولا يُسمح بالوصول كـ 'guest_user'
        if (!Auth::check() && !in_array('guest_user', $roles)) {
            return redirect('/login');
        }

        // إذا كان المستخدم مسجلاً دخوله
        if (Auth::check()) {
            $user = Auth::user();
            foreach ($roles as $role) {
                if ($user->role === $role) {
                    return $next($request);
                }
            }
        }
        // إذا كان المستخدم غير مسجل دخول ولكنه يُسمح كـ 'guest_user'
        elseif (in_array('guest_user', $roles)) {
             return $next($request);
        }


        abort(403, 'Unauthorized action.'); // إذا لم يكن لديه أي من الأدوار المطلوبة أو لم يكن ضيفاً مسموحاً
    }
}