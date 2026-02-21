<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VJ Gallery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        .grid-karya {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media(max-width:1200px){
            .grid-karya { grid-template-columns: repeat(3,1fr); }
        }

        @media(max-width:992px){
            .grid-karya { grid-template-columns: repeat(2,1fr); }
        }

        @media(max-width:576px){
            .grid-karya { grid-template-columns: 1fr; }
        }

        .card-karya {
            background:#1e293b;
            border-radius:14px;
            padding:12px;
            color:white;
            transition:0.3s;
        }

        .card-karya:hover {
            transform: translateY(-4px);
            box-shadow:0 10px 25px rgba(0,0,0,0.4);
        }

        .preview-box {
            width:100%;
            height:220px; /* ukuran seragam */
            overflow:hidden;
            border-radius:10px;
            cursor:pointer;
            position:relative;
        }

        .preview-box img,
        .preview-box video {
            width:100%;
            height:100%;
            object-fit:cover;
            transition:0.4s;
        }

        .preview-box:hover img,
        .preview-box:hover video {
            transform:scale(1.05);
        }
    </style>
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
                <a href="/admin/stage_templates" class="hover:text-cyan-400 transition">Template</a>
                <a href="/admin/karya" class="hover:text-cyan-400 transition">Karya</a>
            @endif

            @if(auth()->user()->role === 'vj')
                <a href="/vj/contents" class="hover:text-cyan-400 transition">Konten Karya</a>
                <a href="/vj/simulation" class="hover:text-cyan-400 transition">Simulasi Panggung</a>
                <a href="/gallery" class="hover:text-cyan-400 transition">Gallery Karya</a>
            @endif
        @endauth
    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-6">
        @auth
            <a href="{{ route('profile.edit') }}" style="color:white; margin-right:15px;">
                <span class="opacity-80">{{ auth()->user()->name }}</span>            
            </a>

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

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

</body>
</html>