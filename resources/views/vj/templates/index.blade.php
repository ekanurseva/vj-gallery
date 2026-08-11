@extends('layouts.app')

@section('content')

<div class="p-8 text-white">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            Template Simulasi Panggung
        </h1>

        <p class="text-gray-400 mt-2">
            Pilih template panggung atau rancangan simulasi yang sesuai
            dengan kebutuhan visual Anda.
        </p>

    </div>


    {{-- ========================================================= --}}
    {{-- SEARCH & FILTER --}}
    {{-- ========================================================= --}}

    <form
        method="GET"
        action="{{ route('vj.templates.index') }}"
        id="filterForm"
        class="flex flex-col md:flex-row md:justify-end gap-3 mb-10"
    >

        {{-- SEARCH --}}
        <input
            type="text"
            name="search"
            id="searchInput"
            value="{{ request('search') }}"
            placeholder="Cari template..."
            class="w-full md:w-64 bg-[#0A192F]
                   border border-white/20
                   rounded-lg px-4 py-2
                   text-white
                   focus:outline-none
                   focus:border-cyan-400"
        >


        {{-- TEMA --}}
        <select
            name="theme_id"
            id="themeFilter"
            class="w-full md:w-52
                   bg-[#0A192F]
                   border border-white/20
                   rounded-lg px-4 py-2
                   text-white
                   focus:outline-none
                   focus:border-cyan-400"
        >

            <option value="">
                Semua Tema
            </option>

            @foreach($themes as $theme)

                <option
                    value="{{ $theme->theme_id }}"
                    {{ request('theme_id') == $theme->theme_id ? 'selected' : '' }}
                >
                    {{ $theme->name }}
                </option>

            @endforeach

        </select>

    </form>


    {{-- ========================================================= --}}
    {{-- TEMPLATE PANGGUNG --}}
    {{-- ========================================================= --}}

    <div class="mb-12">

        <div class="flex items-center justify-between mb-5">

            <div>

                <h2 class="text-2xl font-bold">
                    Template Panggung
                </h2>

                <p class="text-sm text-gray-400 mt-1">
                    Template dasar panggung yang dapat digunakan
                    untuk membuat simulasi baru.
                </p>

            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @forelse($templates as $template)

                <div
                    class="bg-slate-800
                           border border-white/10
                           rounded-xl
                           overflow-hidden
                           shadow-lg
                           hover:border-cyan-400/30
                           transition"
                >

                    {{-- PREVIEW --}}
                    <div class="h-48 bg-slate-700 overflow-hidden">

                        @if($template->background_path)

                            @if($template->background_type === 'image')

                                <img
                                    src="{{ asset('storage/' . $template->background_path) }}"
                                    class="w-full h-full object-cover"
                                    alt="{{ $template->name }}"
                                >

                            @elseif($template->background_type === 'video')

                                <video
                                    class="w-full h-full object-cover"
                                    muted
                                    autoplay
                                    loop
                                    playsinline
                                >
                                    <source
                                        src="{{ asset('storage/' . $template->background_path) }}"
                                        type="video/mp4"
                                    >
                                </video>

                            @endif

                        @else

                            <div
                                class="w-full h-full
                                       flex items-center justify-center
                                       text-gray-500"
                            >
                                Tidak ada preview
                            </div>

                        @endif

                    </div>


                    {{-- INFO --}}
                    <div class="p-5">

                        <div class="flex items-start justify-between gap-3">

                            <h3 class="text-xl font-semibold">
                                {{ $template->name }}
                            </h3>

                            <span
                                class="text-xs
                                       px-2 py-1
                                       rounded-full
                                       bg-blue-400/10
                                       text-blue-300
                                       border border-blue-400/20
                                       whitespace-nowrap"
                            >
                                Panggung
                            </span>

                        </div>


                        <p class="text-sm text-gray-400 mt-2">
                            {{ $template->description ?? 'Tidak ada deskripsi.' }}
                        </p>


                        {{-- CANVAS --}}
                        <div class="text-sm text-gray-400 mt-4">

                            Canvas:

                            <span class="text-white">
                                {{ $template->canvas_width }}
                                ×
                                {{ $template->canvas_height }}
                            </span>

                        </div>


                        {{-- TEMA --}}
                        <div class="mt-4">

                            <p class="text-sm text-gray-400 mb-2">
                                Tema:
                            </p>

                            <div class="flex flex-wrap gap-2">

                                @forelse($template->themes as $theme)

                                    <span
                                        class="px-2 py-1
                                               text-xs
                                               rounded-full
                                               bg-cyan-400/10
                                               text-cyan-300
                                               border border-cyan-400/20"
                                    >
                                        {{ $theme->name }}
                                    </span>

                                @empty

                                    <span class="text-xs text-gray-500">
                                        Belum ada tema
                                    </span>

                                @endforelse

                            </div>

                        </div>


                        {{-- ACTION --}}
                        <div class="mt-5">

                            <a
                                href="{{ route('simulations.create', $template) }}"
                                class="block text-center
                                       bg-cyan-400
                                       hover:bg-cyan-300
                                       text-[#0A192F]
                                       font-semibold
                                       px-4 py-2
                                       rounded-lg
                                       transition"
                            >
                                Gunakan Template
                            </a>

                        </div>

                    </div>

                </div>

            @empty

                <div
                    class="col-span-full
                           text-center
                           py-12
                           text-gray-400
                           border border-white/10
                           rounded-xl
                           bg-slate-800/50"
                >

                    <p class="text-lg">
                        Template panggung tidak ditemukan.
                    </p>

                    <p class="text-sm mt-2">
                        Coba gunakan kata pencarian atau tema yang berbeda.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TEMPLATE SIMULASI --}}
    {{-- ========================================================= --}}

    <div>

        <div class="flex items-center justify-between mb-5">

            <div>

                <h2 class="text-2xl font-bold">
                    Template Simulasi
                </h2>

                <p class="text-sm text-gray-400 mt-1">
                    Rancangan simulasi yang telah dibuat dan dipublikasikan
                    oleh admin sebagai referensi untuk VJ.
                </p>

            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @forelse($simulationTemplates as $simulationTemplate)

                <div
                    class="bg-slate-800
                           border border-white/10
                           rounded-xl
                           overflow-hidden
                           shadow-lg
                           hover:border-purple-400/30
                           transition"
                >

                    {{-- ================================================= --}}
                    {{-- PREVIEW --}}
                    {{-- ================================================= --}}

                    <div class="h-48 bg-slate-700 overflow-hidden">

                        @if(
                            $simulationTemplate->template &&
                            $simulationTemplate->template->background_path
                        )

                            @if(
                                $simulationTemplate->template->background_type === 'image'
                            )

                                <img
                                    src="{{ asset(
                                        'storage/' .
                                        $simulationTemplate->template->background_path
                                    ) }}"
                                    class="w-full h-full object-cover"
                                    alt="{{ $simulationTemplate->title }}"
                                >

                            @elseif(
                                $simulationTemplate->template->background_type === 'video'
                            )

                                <video
                                    class="w-full h-full object-cover"
                                    muted
                                    autoplay
                                    loop
                                    playsinline
                                >
                                    <source
                                        src="{{ asset(
                                            'storage/' .
                                            $simulationTemplate->template->background_path
                                        ) }}"
                                        type="video/mp4"
                                    >
                                </video>

                            @endif

                        @else

                            <div
                                class="w-full h-full
                                       flex items-center justify-center
                                       text-gray-500"
                            >
                                Tidak ada preview
                            </div>

                        @endif

                    </div>


                    {{-- ================================================= --}}
                    {{-- INFO --}}
                    {{-- ================================================= --}}

                    <div class="p-5">

                        <div class="flex items-start justify-between gap-3">

                            <h3 class="text-xl font-semibold">
                                {{ $simulationTemplate->title }}
                            </h3>

                            <span
                                class="text-xs
                                       px-2 py-1
                                       rounded-full
                                       bg-purple-400/10
                                       text-purple-300
                                       border border-purple-400/20
                                       whitespace-nowrap"
                            >
                                Simulasi
                            </span>

                        </div>


                        {{-- DESCRIPTION --}}
                        <p class="text-sm text-gray-400 mt-2">
                            {{
                                $simulationTemplate->description
                                ?? 'Tidak ada deskripsi.'
                            }}
                        </p>


                        {{-- CANVAS --}}
                        <div class="text-sm text-gray-400 mt-4">

                            Canvas:

                            <span class="text-white">
                                {{ $simulationTemplate->canvas_width }}
                                ×
                                {{ $simulationTemplate->canvas_height }}
                            </span>

                        </div>


                        {{-- JUMLAH CONTENT --}}
                        <div class="text-sm text-gray-400 mt-2">

                            Konten:

                            <span class="text-white">
                                {{ $simulationTemplate->simulationContents->count() }}
                                item
                            </span>

                        </div>


                        {{-- TEMA --}}
                        <div class="mt-4">

                            <p class="text-sm text-gray-400 mb-2">
                                Tema:
                            </p>

                            <div class="flex flex-wrap gap-2">

                                @if(
                                    $simulationTemplate->template &&
                                    $simulationTemplate->template->themes
                                )

                                    @forelse(
                                        $simulationTemplate->template->themes
                                        as $theme
                                    )

                                        <span
                                            class="px-2 py-1
                                                   text-xs
                                                   rounded-full
                                                   bg-purple-400/10
                                                   text-purple-300
                                                   border border-purple-400/20"
                                        >
                                            {{ $theme->name }}
                                        </span>

                                    @empty

                                        <span class="text-xs text-gray-500">
                                            Belum ada tema
                                        </span>

                                    @endforelse

                                @else

                                    <span class="text-xs text-gray-500">
                                        Belum ada tema
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- ACTION --}}
                        {{-- ================================================= --}}

                        <div class="grid grid-cols-2 gap-3 mt-5">

                            {{-- REFERENSI --}}
                            <a
                                href="{{ route(
                                    'simulations.reference',
                                    $simulationTemplate->simulation_id
                                ) }}"
                                class="block text-center
                                    bg-gray-700
                                    hover:bg-gray-600
                                    text-white
                                    px-3 py-2
                                    rounded-lg
                                    text-sm
                                    font-semibold
                                    transition"
                            >
                                Lihat Referensi
                            </a>


                            {{-- GUNAKAN TEMPLATE --}}
                            <form
                                action="{{ route(
                                    'simulations.useTemplate',
                                    $simulationTemplate->simulation_id
                                ) }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full
                                        bg-purple-600
                                        hover:bg-purple-700
                                        text-white
                                        px-3 py-2
                                        rounded-lg
                                        text-sm
                                        font-semibold
                                        transition"
                                    onclick="return confirm(
                                        'Gunakan template simulasi ini sebagai dasar simulasi baru?'
                                    )"
                                >
                                    Gunakan Template
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <div
                    class="col-span-full
                           text-center
                           py-12
                           text-gray-400
                           border border-white/10
                           rounded-xl
                           bg-slate-800/50"
                >

                    <p class="text-lg">
                        Belum ada Template Simulasi.
                    </p>

                    <p class="text-sm mt-2">
                        Template Simulasi yang dibuat dan dipublikasikan
                        oleh admin akan muncul di sini.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- AUTO FILTER --}}
{{-- ============================================================= --}}

<script>

    const themeFilter =
        document.getElementById('themeFilter');

    const searchInput =
        document.getElementById('searchInput');

    const form =
        document.getElementById('filterForm');


    /*
    |--------------------------------------------------------------------------
    | Filter Tema
    |--------------------------------------------------------------------------
    */

    if (themeFilter) {

        themeFilter.addEventListener('change', function () {

            form.submit();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if (searchInput) {

        let typingTimer;

        searchInput.addEventListener('keyup', function () {

            clearTimeout(typingTimer);

            typingTimer = setTimeout(() => {

                form.submit();

            }, 500);

        });

    }

</script>

@endsection