<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between">

        <div>
            <div class="p-6 text-2xl font-bold border-b border-slate-700">
                VJ Gallery
            </div>

            <nav class="mt-6 space-y-2 px-4">

                <a href="/admin/dashboard"
                   class="block py-2 px-4 rounded-lg hover:bg-slate-700">
                    Dashboard
                </a>

                <a href="#"
                   class="block py-2 px-4 rounded-lg hover:bg-slate-700">
                    Manajemen Pengguna
                </a>

                <a href="#"
                   class="block py-2 px-4 rounded-lg hover:bg-slate-700">
                    Manajemen Konten
                </a>

                <a href="#"
                   class="block py-2 px-4 rounded-lg hover:bg-slate-700">
                    Manajemen Karya
                </a>

            </nav>
        </div>

        <!-- Logout -->
        <div class="p-4 border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg">
                    Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">@yield('page-title')</h1>

            <div class="text-gray-600">
                {{ auth()->user()->name }}
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>