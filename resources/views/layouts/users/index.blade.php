@extends('layouts.app')
@section('title', 'User Roles — RBAC')

@section('content')

<div class="mb-6">
    <h2 class="text-lg font-semibold text-slate-800">User Role Assignment</h2>
    <p class="text-sm text-slate-500 mt-0.5">Assign roles to employees</p>
</div>

@if(isset($error))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200
                text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
        {{ $error }}
    </div>
@endif

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Employee</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Department</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Current Role</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Assign Role</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($usersWithRoles ?? [] as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-slate-800">{{ $user['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $user['email'] }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-slate-500">
                        {{ $user['department'] ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        @if($user['role_name'])
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600
                                         text-xs font-medium rounded-full">
                                {{ $user['role_name'] }}
                            </span>
                        @else
                            <span class="text-slate-400 text-xs">No role assigned</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <form method="POST"
                                  action="{{ route('users.role.assign', $user['id']) }}">
                                @csrf
                                <div class="flex items-center gap-2">
                                    <select name="role_id"
                                        class="px-3 py-1.5 border border-slate-200 rounded-lg
                                               text-sm focus:outline-none focus:ring-2
                                               focus:ring-indigo-500 bg-white">
                                        <option value="">Select role...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ ($user['role_id'] ?? null) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700
                                               text-white text-xs font-medium rounded-lg
                                               transition-colors">
                                        Assign
                                    </button>
                                </div>
                            </form>

                            @if($user['role_id'])
                                <form method="POST"
                                      action="{{ route('users.role.remove', $user['id']) }}"
                                      onsubmit="return confirm('Remove role from {{ $user['name'] }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-red-50 hover:bg-red-100
                                               text-red-600 text-xs font-medium rounded-lg
                                               transition-colors">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-slate-400">
                        No users found or EMS is not connected.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection