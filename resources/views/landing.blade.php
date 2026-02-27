<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Visual Jockey - Digital Gallery System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-900 text-white">

    <!-- NAVBAR -->
    <nav class="flex justify-between items-center px-10 py-6 bg-slate-950 shadow-lg">
        <h1 class="text-2xl font-bold text-cyan-400 tracking-wide">
            VISUAL JOCKEY
        </h1>

        <div class="space-x-6">
            <a href="{{ route('login') }}" class="hover:text-cyan-400 transition">Login</a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 rounded-lg text-black font-semibold transition">
                Get Started
            </a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="min-h-screen flex flex-col justify-center items-center text-center px-6">

        <h2 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
            Digital Visual Performance <br>
            <span class="text-cyan-400">Gallery & Stage Simulation</span>
        </h2>

        <p class="text-slate-300 max-w-2xl mb-8 text-lg">
            Sistem gallery digital untuk mengelola karya visual, audio,
            dan membuat simulasi panggung secara profesional.
        </p>

        <div class="space-x-4">
            <a href="{{ route('register') }}"
               class="px-6 py-3 bg-cyan-500 hover:bg-cyan-600 rounded-xl text-black font-semibold transition shadow-lg">
                Mulai Sekarang
            </a>

            <a href="{{ route('login') }}"
               class="px-6 py-3 border border-cyan-400 hover:bg-cyan-400 hover:text-black rounded-xl transition">
                Masuk
            </a>
        </div>

    </section>

    <!-- FEATURE SECTION -->
    <section class="py-20 bg-slate-950">
        <div class="text-center mb-16">
            <h3 class="text-3xl font-bold text-cyan-400">
                Fitur Utama
            </h3>
        </div>

        <div class="grid md:grid-cols-3 gap-10 px-10">

            <div class="bg-slate-800 p-8 rounded-2xl shadow-xl hover:scale-105 transition">
                <h4 class="text-xl font-semibold mb-4 text-cyan-400">
                    🎨 Digital Gallery
                </h4>
                <p class="text-slate-300">
                    Upload dan kelola karya visual, video, dan audio
                    dengan sistem approval admin.
                </p>
            </div>

            <div class="bg-slate-800 p-8 rounded-2xl shadow-xl hover:scale-105 transition">
                <h4 class="text-xl font-semibold mb-4 text-cyan-400">
                    🎭 Stage Simulation
                </h4>
                <p class="text-slate-300">
                    Buat dan edit simulasi panggung secara interaktif
                    menggunakan layer visual dan audio.
                </p>
            </div>

            <div class="bg-slate-800 p-8 rounded-2xl shadow-xl hover:scale-105 transition">
                <h4 class="text-xl font-semibold mb-4 text-cyan-400">
                    🔐 User Control
                </h4>
                <p class="text-slate-300">
                    Setiap user memiliki workspace pribadi.
                    Konten hanya terlihat jika sudah disetujui admin.
                </p>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-8 text-center bg-slate-900 text-slate-400">
        © {{ date('Y') }} Visual Jockey - Digital Gallery System
    </footer>

</body>
</html>