<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\UserRole;
use App\Services\EmsApiService;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(protected EmsApiService $emsApi) {}

    // Get all users with their assigned roles
    public function index()
    {
        try {
            $users = $this->emsApi->getUsers();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to fetch users from EMS.',
                'error'   => $e->getMessage(),
            ], 503);
        }

        $roles = Role::where('is_active', true)->get(['id', 'name', 'slug']);

        // Fetch user roles in bulk to avoid N+1 query problem
        $userIds    = collect($users)->pluck('id');
        $userRoles  = UserRole::with('role')
                               ->whereIn('user_id', $userIds)
                               ->get()
                               ->keyBy('user_id');

        $usersWithRoles = collect($users)->map(function ($user) use ($userRoles) {
            $userRole = $userRoles->get($user['id']);
            return array_merge($user, [
                'role_id'   => $userRole?->role_id,
                'role_name' => $userRole?->role?->name,
                'role_slug' => $userRole?->role?->slug,
            ]);
        });

        return response()->json([
            'users' => $usersWithRoles,
            'roles' => $roles,
        ]);
    }

    // Assign a role to a user
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

        return response()->json([
            'message' => "Role '{$role->name}' assigned successfully.",
            'user_id' => $userId,
            'role'    => $role,
        ]);
    }

    // Remove a role from a user
    public function removeRole(int $userId)
    {
        UserRole::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Role removed successfully.',
        ]);
    }
}