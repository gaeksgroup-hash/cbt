<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('cbt.admin_user_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $admin = AdminUser::query()
            ->whereRaw('LOWER(username) = ?', [mb_strtolower(trim($validated['username']))])
            ->where('status', 'active')
            ->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password_hash)) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password tidak cocok.']);
        }

        $request->session()->regenerate();
        $request->session()->put('cbt.admin_user_id', $admin->id);
        $admin->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
