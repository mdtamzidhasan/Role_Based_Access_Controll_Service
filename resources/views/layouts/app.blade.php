<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RBAC Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">

    {{-- Sidebar --}}
    <div class="flex min-h-screen">
        <div class="w-56 bg-white border-r border-slate-200 flex-shrink-0">
            <div class="p-5 border-b border-slate-100">
                <h1 class="text-sm font-bold text-indigo-600">RBAC Management</h1>
                <p class="text-xs text-slate-400 mt-0.5">Access Control System</p>
            </div>
            <nav class="p-3 space-y-1">
                <a href="{{ route('roles.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('roles.*') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Roles
                </a>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    User Roles
                </a>
            </nav>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 p-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200
                            text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-50 border border-red-200
                            text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

</body>
</html>