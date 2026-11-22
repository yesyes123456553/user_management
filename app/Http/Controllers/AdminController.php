<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        // SUPER ADMIN: Can see all user records.
        if ($user->hasRole('super_admin')) {
            $users = User::with('role')
                ->latest()
                ->get();

            return view('Admin.dashboard', [
                'totalUsers' => User::count(),
                'activeUsers' => User::where('status', true)->count(),
                'inactiveUsers' => User::where('status', false)->count(),
                'roleCounts' => DB::table('users')
                    ->join('roles', 'users.role_id', '=', 'roles.id')
                    ->where('users.is_deleted', false)
                    ->select('roles.label', DB::raw('COUNT(users.id) as total'))
                    ->groupBy('roles.id', 'roles.label')
                    ->get(),
                'users' => $users,
            ]);
        }

        // ADMIN: Cannot see Super Admin accounts.
        if ($user->hasRole('admin')) {
            $managedUsers = User::whereHas('role', function ($query) {
                $query->whereIn('name', ['admin', 'super_user', 'user']);
            });

            $users = (clone $managedUsers)
                ->with('role')
                ->latest()
                ->get();

            return view('Admin.dashboard', [
                'totalUsers' => (clone $managedUsers)->count(),
                'activeUsers' => (clone $managedUsers)
                    ->where('status', true)
                    ->count(),
                'inactiveUsers' => (clone $managedUsers)
                    ->where('status', false)
                    ->count(),
                'roleCounts' => DB::table('users')
                    ->join('roles', 'users.role_id', '=', 'roles.id')
                    ->where('users.is_deleted', false)
                    ->whereIn('roles.name', ['admin', 'super_user', 'user'])
                    ->select('roles.label', DB::raw('COUNT(users.id) as total'))
                    ->groupBy('roles.id', 'roles.label')
                    ->get(),
                'users' => $users,
            ]);
        }

        // SUPER USER and USER: Personal dashboard only.
        if ($user->hasRole('super_user') || $user->hasRole('user')) {
            return view('Admin.dashboard', [
                'user' => $user,
            ]);
        }

        abort(403, 'Your account does not have an assigned dashboard.');
    }
}