<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Permission;
use App\Models\RbacObject;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    //Role List Page 
    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    // Permission Matrix Edit Page
    public function editPermissions(int $id)
    {
        $role       = Role::with('permissions')->findOrFail($id);
        $objects    = RbacObject::all();
        $operations = Operation::all();
        $allPermissions = Permission::with('object', 'operation')->get();
        $rolePermissionSlugs = $role->permissions->pluck('slug')->toArray();

        return view('roles.permissions', compact(
            'role', 'objects', 'operations',
            'allPermissions', 'rolePermissionSlugs'
        ));
    }

    //Permission Update 
    public function updatePermissions(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        $selectedSlugs = $request->input('permissions', []);

        $permissionIds = Permission::whereIn('slug', $selectedSlugs)
                                    ->pluck('id');

        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.index')
            ->with('success', "Permissions for '{$role->name}' updated successfully.");
    }

    //Create a new role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Role::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name'], '_'),
            'description' => $validated['description'] ?? null,
            'is_active'   => true,
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$validated['name']}' created successfully.");
    }


    //Role Delete 
    public function destroy(int $id)
    {
        $role = Role::findOrFail($id);
        $name = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role '{$name}' deleted successfully.");
    }

    public function storeObject(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'description'     => ['nullable', 'string', 'max:255'],
            'object_type'     => ['required', 'in:personal,hr,department,system,custom'],
            'department_name' => ['nullable', 'required_if:object_type,department', 'string'],
        ]);

        // Slug তৈরি করো
        $slug = Str::slug($validated['name'], '_');

        // Department type হলে dept_ prefix যোগ করো
        if ($validated['object_type'] === 'department') {
            $slug = 'dept_' . Str::slug($validated['department_name'], '_');
        }

        // Duplicate check করো
        if (\App\Models\RbacObject::where('slug', $slug)->exists()) {
            return redirect()->back()
                ->withErrors(['name' => 'An object with this name already exists.']);
        }

        $object = \App\Models\RbacObject::create([
            'name'            => $validated['name'],
            'slug'            => $slug,
            'description'     => $validated['description'] ?? null,
            'object_type'     => $validated['object_type'],
            'department_name' => $validated['department_name'] ?? null,
        ]);

        // এই object এর জন্য সব operation এর permission তৈরি করো
        $operations = Operation::all();
        foreach ($operations as $operation) {
            $permSlug = $object->slug . '.' . $operation->slug;
            Permission::firstOrCreate(
                ['slug' => $permSlug],
                [
                    'object_id'    => $object->id,
                    'operation_id' => $operation->id,
                    'slug'         => $permSlug,
                    'description'  => "{$operation->name} {$object->name}",
                ]
            );
        }

        return redirect()->back()
            ->with('success', "Object '{$object->name}' created successfully.");
    }
}