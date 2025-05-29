<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $totalArtists = User::where('role', 'artist')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalArtists',
            'totalAdmins',
            'recentUsers'
        ));
    }

    /**
     * Display a listing of the users.
     */
    public function users(): View
    {
        $query = User::query();

        // Search by name or email
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if (request('role')) {
            $query->where('role', request('role'));
        }

        // Filter by status
        if (request('status') !== null) {
            $query->where('is_active', request('status'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Return the full view
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function editUser(User $user, Request $request)
    {
        if ($request->ajax()) {
            // If it's an AJAX request, return only the form partial
            return response()->json([
                'html' => view('admin.users._edit_form', compact('user'))->render()
            ]);
        }

        // If it's a regular request, return the full page view (this might become obsolete)
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,artist',
            'is_active' => 'boolean'
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
} 