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
        $userRole = UserRole::with('role.permissions.object')
                             ->where('user_id', $userId)
                             ->first();

        if (!$userRole) {
            return response()->json([
                'user_id'     => $userId,
                'role'        => null,
                'permissions' => [],
                'objects'     => [],
            ]);
        }

        $permissions = $userRole->role->permissions->pluck('slug')->toArray();

        // Object গুলো group করে পাঠাও (slug → operations + metadata)
        $objects = [];
        foreach ($userRole->role->permissions as $perm) {
            $objSlug = $perm->object->slug;
            if (!isset($objects[$objSlug])) {
                $objects[$objSlug] = [
                    'slug'            => $objSlug,
                    'name'            => $perm->object->name,
                    'object_type'     => $perm->object->object_type,
                    'department_name' => $perm->object->department_name,
                    'description'     => $perm->object->description,
                    'operations'      => [],
                ];
            }
            $objects[$objSlug]['operations'][] = $perm->operation->slug;
        }

        return response()->json([
            'user_id'     => $userId,
            'role'        => $userRole->role->only(['id', 'name', 'slug']),
            'permissions' => $permissions,
            'objects'     => array_values($objects),
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