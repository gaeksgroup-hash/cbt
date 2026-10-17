<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCbtAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = AdminUser::query()
            ->whereKey($request->session()->get('cbt.admin_user_id'))
            ->where('status', 'active')
            ->first();

        if (! $admin) {
            $request->session()->forget('cbt.admin_user_id');

            return redirect()->guest(route('admin.login'));
        }

        $request->attributes->set('cbt.admin', $admin);

        return $next($request);
    }
}
