<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\Request;

class PermissionCheckController extends Controller
{
    // Check all permissions of a user
    public function check(Request $request, int $userId)
    {
        $userRole = UserRole::with('role.permissions')
                             ->where('user_id', $userId)
                             ->first();

        if (!$userRole) {
            return response()->json([
                'user_id'     => $userId,
                'role'        => null,
                'permissions' => [],
            ]);
        }

        $permissions = $userRole->role->permissions->pluck('slug')->toArray();

        return response()->json([
            'user_id'     => $userId,
            'role'        => $userRole->role->only(['id', 'name', 'slug']),
            'permissions' => $permissions,
        ]);
    }

    // Check if a user has a specific permission
    public function has(Request $request, int $userId)
    {
        $validated = $request->validate([
            'permission' => ['required', 'string'],
        ]);

        $userRole = UserRole::with('role.permissions')
                             ->where('user_id', $userId)
                             ->first();

        if (!$userRole) {
            return response()->json([
                'user_id'    => $userId,
                'permission' => $validated['permission'],
                'has'        => false,
            ]);
        }

        $hasPermission = $userRole->role->permissions
                                         ->pluck('slug')
                                         ->contains($validated['permission']);

        return response()->json([
            'user_id'    => $userId,
            'permission' => $validated['permission'],
            'has'        => $hasPermission,
            'role'       => $userRole->role->only(['id', 'name', 'slug']),
        ]);
    }
}