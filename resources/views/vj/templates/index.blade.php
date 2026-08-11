@extends('layouts.app')

@section('content')

<div class="p-8 text-white">

    {{-- HEADER --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            Template Simulasi Panggung
        </h1>

        <p class="text-gray-400 mt-2">
            Pilih template panggung yang sesuai dengan kebutuhan visual Anda.
        </p>

    </div>


    {{-- SEARCH & FILTER --}}
    <form
        method="GET"
        action="{{ route('vj.templates.index') }}"
        id="filterForm"
        class="flex flex-col md:flex-row md:justify-end gap-3 mb-8"
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


    {{-- TEMPLATE LIST --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        @forelse($templates as $template)

            <div
                class="bg-slate-800
                       border border-white/10
                       rounded-xl
                       overflow-hidden
                       shadow-lg"
            >

                {{-- PREVIEW --}}
                <div class="h-48 bg-slate-700 overflow-hidden">

                    @if($template->background_path)

                        @if($template->background_type === 'image')

                            <img
                                src="{{ asset('storage/' . $template->background_path) }}"
                                class="w-full h-full object-cover"
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

                        <div class="w-full h-full flex items-center justify-center text-gray-500">
                            Tidak ada preview
                        </div>

                    @endif

                </div>


                {{-- INFO --}}
                <div class="p-5">

                    <h2 class="text-xl font-semibold">
                        {{ $template->name }}
                    </h2>


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
                       py-16
                       text-gray-400"
            >
                <p class="text-lg">
                    Template tidak ditemukan.
                </p>

                <p class="text-sm mt-2">
                    Coba gunakan kata pencarian atau tema yang berbeda.
                </p>
            </div>

        @endforelse

    </div>

</div>


{{-- AUTO FILTER --}}
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

    themeFilter.addEventListener('change', function () {

        form.submit();

    });


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    let typingTimer;

    searchInput.addEventListener('keyup', function () {

        clearTimeout(typingTimer);

        typingTimer = setTimeout(() => {

            form.submit();

        }, 500);

    });

</script>

@endsection