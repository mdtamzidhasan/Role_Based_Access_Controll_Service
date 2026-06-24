@extends('layouts.app')
@section('title', 'Roles — RBAC')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">Roles</h2>
        <p class="text-sm text-slate-500 mt-0.5">Manage roles and their permissions</p>
    </div>

    {{-- New Role Form --}}
    <button onclick="document.getElementById('new-role-modal').classList.remove('hidden')"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm
               font-medium rounded-lg transition-colors">
        + New Role
    </button>
</div>

{{-- Role List --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Role</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Permissions</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($roles as $role)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-slate-800">{{ $role->name }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $role->slug }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-slate-500">
                        {{ $role->description ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-full">
                            {{ $role->permissions_count }} permissions
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('roles.permissions', $role->id) }}"
                               class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600
                                      text-xs font-medium rounded-lg transition-colors">
                                Edit Permissions
                            </a>
                            <form method="POST" action="{{ route('roles.destroy', $role->id) }}"
                                  onsubmit="return confirm('Delete role {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600
                                           text-xs font-medium rounded-lg transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-slate-400">
                        No roles found. Create your first role.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- New Role Modal --}}
<div id="new-role-modal"
     class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl border border-slate-200 p-6 w-full max-w-md">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Create New Role</h3>

        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Role Name</label>
                    <input type="text" name="name" required
                        placeholder="e.g. HR Manager"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <input type="text" name="description"
                        placeholder="Optional description"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white
                           text-sm font-medium rounded-lg transition-colors">
                    Create Role
                </button>
                <button type="button"
                    onclick="document.getElementById('new-role-modal').classList.add('hidden')"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600
                           text-sm font-medium rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection