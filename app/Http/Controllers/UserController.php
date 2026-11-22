<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Check whether the current user can manage the target account.
     */
    private function authorizeTarget(User $user): void
    {
        $currentUser = Auth::user();

        if (! $currentUser) {
            abort(403);
        }

        // Only Super Admin can manage Super Admin accounts.
        if (
            $user->hasRole('super_admin')
            && ! $currentUser->hasRole('super_admin')
        ) {
            abort(403, 'Only Super Admin can manage Super Admin accounts.');
        }

        // Prevent deleting or changing your own role through this CRUD.
        if ($user->id === $currentUser->id) {
            abort(403, 'You cannot modify your own account through User Management.');
        }
    }

    /**
     * Prevent Admin from assigning Super Admin.
     */
    private function validateRoleAssignment(int $roleId): void
    {
        $selectedRole = Role::findOrFail($roleId);

        if (
            Auth::user()->hasRole('admin')
            && $selectedRole->name === 'super_admin'
        ) {
            throw ValidationException::withMessages([
                'role_id' => 'Admin cannot assign the Super Admin role.',
            ]);
        }
    }

    // READ
    public function index(): View
    {
        $query = User::with('role')->latest();

        // Admin cannot view Super Admin accounts.
        if (Auth::user()->hasRole('admin')) {
            $query->whereHas('role', function ($q) {
                $q->where('name', '!=', 'super_admin');
            });
        }

        return view('Users.index', [
            'users' => $query->paginate(10),
        ]);
    }

    // CREATE FORM
    public function create(): View
    {
        $roles = Role::orderBy('label');

        // Admin cannot assign Super Admin.
        if (Auth::user()->hasRole('admin')) {
            $roles->where('name', '!=', 'super_admin');
        }

        return view('Users.create', [
            'roles' => $roles->get(),
        ]);
    }

    // STORE
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $this->validateRoleAssignment((int) $data['role_id']);

        $data['status'] = $request->boolean('status');
        $data['email_verified_at'] = now();

        User::create($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    // EDIT FORM
    public function edit(User $user): View
    {
        $this->authorizeTarget($user);

        $roles = Role::orderBy('label');

        if (Auth::user()->hasRole('admin')) {
            $roles->where('name', '!=', 'super_admin');
        }

        return view('Users.edit', [
            'user' => $user,
            'roles' => $roles->get(),
        ]);
    }

    // UPDATE
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $this->validateRoleAssignment((int) $data['role_id']);

        $data['status'] = $request->boolean('status');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    // DELETE
    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeTarget($user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
