<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RbacObject;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    //Get all roles
    public function index()
    {
        $roles = Role::withCount('permissions')->get();

        return response()->json(['roles' => $roles]);
    }
    //Create a new role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
        ]);

        $role = Role::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name'], '_'),
            'description' => $validated['description'] ?? null,
            'is_active'   => true,
        ]);

        return response()->json([
            'message' => 'Role created successfully.',
            'role'    => $role,
        ], 201);
    }

    // Get a specific role with its permissions
    public function permissions(int $roleId)
    {
        $role = Role::with('permissions.object', 'permissions.operation')
                     ->findOrFail($roleId);

        $objects    = RbacObject::all();
        $allPermissions = Permission::with('object', 'operation')->get();
        $rolePermissionSlugs = $role->permissions->pluck('slug')->toArray();

        // Create permission matrix for the role
        $matrix = [];
        foreach ($objects as $object) {
            $objectPermissions = $allPermissions->where('object_id', $object->id);
            $ops = [];
            foreach ($objectPermissions as $perm) {
                $ops[$perm->operation->slug] = in_array($perm->slug, $rolePermissionSlugs);
            }
            $matrix[] = [
                'object_id'   => $object->id,
                'object_name' => $object->name,
                'object_slug' => $object->slug,
                'operations'  => $ops,
            ];
        }

        return response()->json([
            'role'   => $role->only(['id', 'name', 'slug', 'description']),
            'matrix' => $matrix,
        ]);
    }

    // Update permissions for a role
    public function updatePermissions(Request $request, int $roleId)
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);

        $role = Role::findOrFail($roleId);

        $permissionIds = Permission::whereIn('slug', $validated['permissions'])
                                    ->pluck('id');

        $role->permissions()->sync($permissionIds);

        return response()->json([
            'message' => 'Permissions updated successfully.',
            'role'    => $role->load('permissions'),
        ]);
    }

    // Delete a role
    public function destroy(int $roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}