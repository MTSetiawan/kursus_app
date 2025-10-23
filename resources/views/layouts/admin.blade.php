<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css') {{-- jika pakai Vite --}}
</head>

<body class="bg-gray-100 min-h-screen flex text-gray-800">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white shadow-lg flex flex-col">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-bold text-blue-600">Admin Panel</h1>
        </div>

        <nav class="flex-1 px-4 py-2 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="block px-3 py-2 rounded hover:bg-blue-50 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 font-semibold' : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.plans.index') }}"
                class="block px-3 py-2 rounded hover:bg-blue-50 {{ request()->is('admin/plans*') ? 'bg-blue-100 font-semibold' : '' }}">
                💳 Kelola Plan
            </a>
            <a href="{{ route('admin.plan-requests.index') }}"
                class="block px-3 py-2 rounded hover:bg-blue-50 {{ request()->is('admin/plan-requests*') ? 'bg-blue-100 font-semibold' : '' }}">
                📨 Plan Requests
            </a>
            <a href="{{ route('admin.listings.index') }}"
                class="block px-3 py-2 rounded hover:bg-blue-50 {{ request()->is('admin/listings*') ? 'bg-blue-100 font-semibold' : '' }}">
                📚 Listing
            </a>
            {{-- <a href="{{ route('admin.users') }}"
                class="block px-3 py-2 rounded hover:bg-blue-50 {{ request()->is('admin/users*') ? 'bg-blue-100 font-semibold' : '' }}">
                👤 Pengguna
            </a> --}}
        </nav>

        <div class="px-4 py-4 border-t">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded hover:bg-red-100 text-red-600 font-semibold">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-6 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            @yield('content')
        </div>
    </main>

</body>

</html>
