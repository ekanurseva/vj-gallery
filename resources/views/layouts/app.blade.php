<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VJ Gallery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-[#0A192F] text-white min-h-screen">

<!-- NAVBAR -->
<nav class="w-full bg-[#0A192F] border-b border-white/10 px-10 py-5 flex justify-between items-center">

    <!-- LEFT -->
    <div class="flex items-center gap-10">
        <div class="text-2xl font-bold tracking-wider">
            VJ_GALLERY
        </div>

        @auth
            @if(auth()->user()->role === 'admin')
                <a href="/admin/users" class="hover:text-cyan-400 transition">Pengguna</a>
                <a href="/admin/contents" class="hover:text-cyan-400 transition">Konten</a>
                <a href="/admin/templates" class="hover:text-cyan-400 transition">Template</a>
                <a href="/admin/karya" class="hover:text-cyan-400 transition">Karya</a>
            @endif

            @if(auth()->user()->role === 'vj')
                <a href="/vj/contents" class="hover:text-cyan-400 transition">Konten</a>
                <a href="/vj/simulation" class="hover:text-cyan-400 transition">Simulasi Panggung</a>
                <a href="/vj/karya" class="hover:text-cyan-400 transition">Karya</a>
            @endif
        @endauth
    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-6">
        @auth
            <span class="opacity-80">{{ auth()->user()->name }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-cyan-400 text-[#0A192F] px-4 py-2 rounded-lg font-semibold hover:bg-cyan-300 transition">
                    Logout
                </button>
            </form>
        @endauth
    </div>
</nav>

<main>
    @yield('content')
</main>

</body>
</html>