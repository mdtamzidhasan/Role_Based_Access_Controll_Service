<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\UserRole;
use App\Services\EmsApiService;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(protected EmsApiService $emsApi) {}

    // ── User List Page ────────────────────────────────────────
    public function index()
    {
        try {
            $users = $this->emsApi->getUsers();
        } catch (\Exception $e) {
            $users = [];
            return view('users.index', compact('users'))
                ->with('error', 'Unable to connect to EMS. Make sure EMS is running.');
        }

        $roles    = Role::where('is_active', true)->get();
        $userIds  = collect($users)->pluck('id');
        $userRoles = UserRole::with('role')
                              ->whereIn('user_id', $userIds)
                              ->get()
                              ->keyBy('user_id');

        $usersWithRoles = collect($users)->map(function ($user) use ($userRoles) {
            $userRole = $userRoles->get($user['id']);
            return array_merge($user, [
                'role_id'   => $userRole?->role_id,
                'role_name' => $userRole?->role?->name,
            ]);
        });
        return view('layouts.users.index', compact('usersWithRoles', 'roles'));
    }

    // ── User Role Assign ──────────────────────────────────────
    public function assignRole(Request $request, int $userId)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        UserRole::updateOrCreate(
            ['user_id' => $userId],
            ['role_id' => $validated['role_id']]
        );

        $role = Role::find($validated['role_id']);

        return redirect()->route('users.index')
            ->with('success', "Role '{$role->name}' assigned successfully.");
    }

    // ── User Role Remove ──────────────────────────────────────
    public function removeRole(int $userId)
    {
        UserRole::where('user_id', $userId)->delete();

        return redirect()->route('users.index')
            ->with('success', 'Role removed successfully.');
    }
}