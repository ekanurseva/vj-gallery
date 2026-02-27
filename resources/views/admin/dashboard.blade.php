@extends('layouts.app')

@section('content')

<!-- HERO VIDEO -->
<div class="w-full h-[350px] overflow-hidden">
    <video autoplay muted loop class="w-full h-full object-cover opacity-70">
        <source src="{{ asset('videos/vj.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- CARDS -->
<div class="px-16 py-6 grid grid-cols-1 md:grid-cols-4 gap-8">

    <a href="/admin/users" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Jumlah Pengguna</h2>
        <p class="text-3xl font-bold mt-2">{{ $totalUsers }}</p>
    </a>

    <a href="/admin/contents" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Jumlah Konten Pending</h2>
        <p class="text-3xl font-bold mt-2">{{ $totalContents }}</p>
    </a>

    <a href="/admin/stage_templates" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Template Panggung</h2>
        <p class="text-3xl font-bold mt-2">{{ $totalTemplates }}</p>
    </a>

    <a href="/admin/karya" class="bg-white/5 border border-white/10 p-8 rounded-xl hover:bg-white/10 transition">
        <h2 class="text-lg opacity-70">Jumlah Karya</h2>
        <p class="text-3xl font-bold mt-2">{{ $totalKarya }}</p>
    </a>

</div>

@endsection