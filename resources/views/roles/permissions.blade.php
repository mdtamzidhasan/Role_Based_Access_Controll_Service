@extends('layouts.app')
@section('title', 'Edit Permissions — ' . $role->name)

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('roles.index') }}"
         class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
         ← Back to Roles
        </a>
        <h2 class="text-lg font-semibold text-slate-800 mt-2">
             {{ $role->name }} — Edit Permissions
        </h2>
        <p class="text-sm text-slate-500 mt-0.5">
             Check the permissions this role should have
        </p>
    </div>
    {{-- Add New Object Button --}}
    <button onclick="document.getElementById('new-object-modal').classList.remove('hidden')"
        class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm
               font-medium rounded-lg transition-colors">
        + Add New Object
    </button>
</div>

<form method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
    @csrf

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-5">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase w-40">
                        Object
                    </th>
                    @foreach($operations as $operation)
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase">
                            {{ $operation->name }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($objects as $object)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-700">{{ $object->name }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $object->slug }}</p>
                        </td>
                        @foreach($operations as $operation)
                            @php
                                $slug = $object->slug . '.' . $operation->slug;
                                $permExists = $allPermissions->where('slug', $slug)->first();
                                $isChecked  = in_array($slug, $rolePermissionSlugs);
                            @endphp
                            <td class="px-4 py-3.5 text-center">
                                @if($permExists)
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $slug }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-slate-300
                                                  rounded focus:ring-indigo-500 cursor-pointer">
                                @else
                                    <span class="text-slate-200">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm
                   font-medium rounded-lg transition-colors">
            Save Permissions
        </button>
        <a href="{{ route('roles.index') }}"
           class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600
                  text-sm font-medium rounded-lg transition-colors">
            Cancel
        </a>
    </div>
</form>

{{-- New Object Modal --}}
<div id="new-object-modal"
     class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl border border-slate-200 p-6 w-full max-w-md">
        <h3 class="text-base font-semibold text-slate-800 mb-1">Add New Object</h3>
        <p class="text-xs text-slate-400 mb-4">
            All operations (view, create, edit, delete, export) will be
            automatically created for this object.
        </p>

        <form method="POST" action="{{ route('objects.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Object Name
                    </label>
                    <input type="text" name="name" required
                        placeholder="e.g. Payroll, Attendance"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Description
                    </label>
                    <input type="text" name="description"
                        placeholder="Optional description"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white
                           text-sm font-medium rounded-lg transition-colors">
                    Create Object
                </button>
                <button type="button"
                    onclick="document.getElementById('new-object-modal').classList.add('hidden')"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600
                           text-sm font-medium rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection