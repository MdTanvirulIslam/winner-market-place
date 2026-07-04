<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

// Staff / super admin accounts. Customers register themselves on the public
// site and are managed from the Customers section (Phase 2).
class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::whereIn('role', ['staff', 'super_admin'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:staff,super_admin',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin user created.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->isAdmin(), 404);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isAdmin(), 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:staff,super_admin',
        ]);

        // Don't let a super admin demote their own account and lock
        // themselves out of this page.
        if ($user->is($request->user()) && $data['role'] !== 'super_admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Admin user updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isAdmin(), 404);

        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Admin user deleted.');
    }
}
