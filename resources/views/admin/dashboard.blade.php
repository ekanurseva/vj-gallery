@extends('layouts.app')

@section('content')

<!-- HERO VIDEO -->
<div class="w-full h-[350px] overflow-hidden">
    <video autoplay muted loop class="w-full h-full object-cover opacity-70">
        <source src="{{ asset('videos/vj.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- CARDS -->
<div class="px-16 py-16 grid grid-cols-1 md:grid-cols-4 gap-8">

    <a href="/admin/users" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Jumlah Pengguna</h2>
        <p class="text-3xl font-bold mt-2">120</p>
    </a>

    <a href="/admin/contents" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Jumlah Konten</h2>
        <p class="text-3xl font-bold mt-2">340</p>
    </a>

    <a href="/admin/templates" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Template Panggung</h2>
        <p class="text-3xl font-bold mt-2">45</p>
    </a>

    <a href="/admin/karya" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Jumlah Karya</h2>
        <p class="text-3xl font-bold mt-2">98</p>
    </a>

</div>

@endsection