@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between px-20 py-4">

    <!-- LEFT TEXT -->
    <div class="max-w-xl">
        <h1 class="text-4xl font-bold leading-snug">
            Ciptakan dan Eksplorasi <br> Panggung Impianmu!
        </h1>

        <a href="/admin/stage_templates"
           class="inline-block mt-8 bg-cyan-400 text-[#0A192F] px-6 py-3 rounded-lg font-semibold hover:bg-cyan-300 transition">
            Start
        </a>
    </div>

    <!-- RIGHT IMAGE -->
    <div>
        <img src="{{ asset('images/vj-illustration.png') }}"
             class="w-[600px] h-[500px] opacity-90">
    </div>

</div>

@endsection