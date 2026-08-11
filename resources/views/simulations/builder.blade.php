@extends('layouts.app')

@section('content')

<div class="h-screen bg-gray-900 text-white flex flex-col overflow-hidden">

    {{-- ========================================================= --}}
    {{-- TOP BAR --}}
    {{-- ========================================================= --}}

    <div class="flex-shrink-0 flex justify-between items-center px-6 py-4 bg-gray-800 shadow">

        <div>
            <h1 class="text-xl font-semibold">
                Simulation Builder
            </h1>

            <p class="text-sm text-gray-400">
                {{ $simulation->title }}
            </p>
        </div>

        <div>
            <button
                id="saveBtn"
                class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded transition"
            >
                Save Simulation
            </button>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MAIN WORKSPACE --}}
    {{-- ========================================================= --}}

    <div class="flex flex-1 min-h-0 overflow-hidden">


        {{-- ===================================================== --}}
        {{-- SIDEBAR KIRI --}}
        {{-- ===================================================== --}}

        <aside
            class="w-64 flex-shrink-0 bg-gray-800 border-r border-gray-700 overflow-y-auto"
        >

            <div class="p-4">

                {{-- AVAILABLE CONTENTS --}}
                <section>

                    <h2 class="text-lg font-semibold mb-4">
                        Available Contents
                    </h2>


                    @forelse($availableContents as $content)

                        <div
                            class="bg-gray-700 hover:bg-gray-600 p-2 mb-3 rounded cursor-pointer content-item transition"
                            data-id="{{ $content->content_id }}"
                            data-type="{{ $content->type }}"
                            data-path="{{ $content->file_path }}"
                        >

                            <div class="text-sm truncate">
                                {{ $content->title }}
                            </div>

                        </div>

                    @empty

                        <p class="text-sm text-gray-500">
                            Belum ada konten.
                        </p>

                    @endforelse

                </section>


                {{-- DIVIDER --}}
                <div class="border-t border-gray-700 my-5"></div>


                {{-- REKOMENDASI --}}
                <section>

                    <h2 class="text-lg font-semibold mb-1">
                        Rekomendasi
                    </h2>

                    <p class="text-xs text-gray-400 mb-3">
                        Berdasarkan tema template
                    </p>


                    {{-- TEMA --}}
                    <div class="flex flex-wrap gap-2 mb-5">

                        @forelse($simulation->template->themes ?? [] as $theme)

                            <span
                                class="px-2 py-1 rounded-full bg-cyan-400/10 text-cyan-300 text-xs border border-cyan-400/20"
                            >
                                {{ $theme->name }}
                            </span>

                        @empty

                            <span class="text-xs text-gray-500">
                                Belum ada tema.
                            </span>

                        @endforelse

                    </div>


                    {{-- VISUAL --}}
                    <div class="mb-5">

                        <h3 class="text-sm font-semibold text-gray-300 mb-2">
                            Visual
                        </h3>


                        @forelse($recommendedVisuals as $content)

                            <div
                                class="bg-gray-700 hover:bg-gray-600 p-2 mb-2 rounded cursor-pointer content-item transition"
                                data-id="{{ $content->content_id }}"
                                data-type="{{ $content->type }}"
                                data-path="{{ $content->file_path }}"
                            >

                                <div class="flex items-center gap-2">

                                    @if($content->type === 'image')

                                        <span class="text-sm">
                                            🖼️
                                        </span>

                                    @elseif($content->type === 'video')

                                        <span class="text-sm">
                                            🎬
                                        </span>

                                    @else

                                        <span class="text-sm">
                                            📁
                                        </span>

                                    @endif


                                    <span class="text-sm truncate">
                                        {{ $content->title }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <p class="text-xs text-gray-500">
                                Belum ada rekomendasi visual.
                            </p>

                        @endforelse

                    </div>


                    {{-- AUDIO --}}
                    <div>

                        <h3 class="text-sm font-semibold text-gray-300 mb-2">
                            Audio
                        </h3>


                        @forelse($recommendedAudios as $content)

                            <div
                                class="bg-gray-700 hover:bg-gray-600 p-2 mb-2 rounded cursor-pointer content-item transition"
                                data-id="{{ $content->content_id }}"
                                data-type="{{ $content->type }}"
                                data-path="{{ $content->file_path }}"
                            >

                                <div class="flex items-center gap-2">

                                    <span>
                                        🎵
                                    </span>

                                    <span class="text-sm truncate">
                                        {{ $content->title }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <p class="text-xs text-gray-500">
                                Belum ada rekomendasi audio.
                            </p>

                        @endforelse

                    </div>

                </section>

            </div>

        </aside>



        {{-- ===================================================== --}}
        {{-- STAGE AREA --}}
        {{-- ===================================================== --}}

        <main
            class="flex-1 min-w-0 min-h-0 overflow-auto flex justify-center items-center p-6"
        >

            <div
                id="stage"
                class="relative bg-black border border-gray-700 shadow-lg flex-shrink-0"
                data-width="{{ $simulation->canvas_width }}"
                data-height="{{ $simulation->canvas_height }}"
            >

                {{-- ============================================= --}}
                {{-- RENDER SLOTS --}}
                {{-- ============================================= --}}

                @if(!empty($layout))

                    @foreach($layout as $slot)

                        <div
                            class="slot absolute border-2 border-dashed border-white text-xs bg-blue-500/20 pointer-events-auto"
                            data-id="{{ $slot['slot_id'] }}"
                            data-x="{{ $slot['x'] }}"
                            data-y="{{ $slot['y'] }}"
                            data-width="{{ $slot['width'] }}"
                            data-height="{{ $slot['height'] }}"
                        >

                            Slot {{ $slot['slot_id'] }}

                        </div>

                    @endforeach

                @endif


                {{-- ============================================= --}}
                {{-- RENDER SAVED CONTENTS --}}
                {{-- ============================================= --}}

                @foreach($visualContents as $item)

                    @if($item->content)

                        <div
                            class="stage-content absolute group"
                            data-content-id="{{ $item->content_id }}"
                            data-title="{{ $item->content->title }}"
                            data-type="{{ $item->content->type }}"
                            data-slot-id="{{ $item->slot_id }}"
                            data-layer-order="{{ $item->layer_order }}"
                            data-start-time="{{ $item->start_time }}"
                            data-duration="{{ $item->duration }}"
                            data-id="{{ $item->sim_content_id }}"
                            data-x="{{ $item->pos_x }}"
                            data-y="{{ $item->pos_y }}"
                            data-width="{{ $item->width }}"
                            data-height="{{ $item->height }}"
                        >

                            @if($item->content->type === 'image')

                                <img
                                    src="{{ asset('storage/' . $item->content->file_path) }}"
                                    class="w-full h-full object-cover"
                                    alt="{{ $item->content->title }}"
                                >

                            @elseif($item->content->type === 'video')

                                <video
                                    src="{{ asset('storage/' . $item->content->file_path) }}"
                                    class="w-full h-full"
                                    preload="auto"
                                ></video>

                            @endif

                        </div>

                    @endif

                @endforeach

            </div>

        </main>



        {{-- ===================================================== --}}
        {{-- SIDEBAR KANAN - LAYERS --}}
        {{-- ===================================================== --}}

        <aside
            class="w-64 flex-shrink-0 bg-gray-800 border-l border-gray-700 overflow-y-auto"
        >

            <div class="p-4">

                <h2 class="text-lg font-semibold mb-4">
                    Layers
                </h2>

                <div
                    id="layerPanel"
                    class="space-y-2"
                >

                    {{-- Layer items akan di-generate via JS --}}

                </div>

            </div>

        </aside>

    </div>



    {{-- ========================================================= --}}
    {{-- AUDIO + TIMELINE --}}
    {{-- ========================================================= --}}

    <div
        class="flex-shrink-0 h-56 flex border-t border-gray-700 bg-gray-800"
    >


        {{-- ===================================================== --}}
        {{-- AUDIO TRACKS --}}
        {{-- ===================================================== --}}

        <aside
            class="w-64 flex-shrink-0 bg-gray-900 p-4 border-r border-gray-700"
        >

            <h2 class="text-lg font-semibold mb-4">
                Audio Tracks
            </h2>


            <div
                id="audioPanel"
                class="space-y-2 h-24 overflow-y-auto"
            >

                @foreach($audioContents as $item)

                    <div
                        class="audio-item bg-gray-700 p-2 rounded cursor-move flex justify-between items-center"
                        data-content-id="{{ $item->content_id }}"
                        data-title="{{ $item->content->title }}"
                        data-start-time="{{ $item->start_time ?? 0 }}"
                        data-duration="{{ $item->duration ?? 10 }}"
                    >

                        <span class="text-sm truncate">
                            🎵 {{ $item->content->title }}
                        </span>

                        <button
                            class="delete-audio text-red-500 text-xs ml-2"
                        >
                            ✕
                        </button>


                        <audio
                            src="{{ asset('storage/' . $item->content->file_path) }}"
                            preload="auto"
                            style="display:none;"
                        ></audio>

                    </div>

                @endforeach

            </div>


            {{-- PLAYBACK CONTROL --}}
            <div class="flex items-center gap-2 mt-4">

                <button
                    id="playBtn"
                    class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm"
                >
                    ▶ Play
                </button>

                <button
                    id="pauseBtn"
                    class="bg-yellow-600 hover:bg-yellow-700 px-3 py-1 rounded text-sm"
                >
                    ⏸ Pause
                </button>

                <span
                    id="currentTimeLabel"
                    class="text-xs text-gray-400"
                >
                    0.00s
                </span>

            </div>

        </aside>



        {{-- ===================================================== --}}
        {{-- TIMELINE --}}
        {{-- ===================================================== --}}

        <section
            class="flex-1 min-w-0 bg-gray-800 p-4"
        >

            <h2 class="text-lg font-semibold mb-2">
                Timeline
            </h2>


            <div
                id="timeline"
                class="relative bg-gray-900 h-36 overflow-auto border border-gray-700 w-full"
            >

                <div
                    id="timelineTracks"
                    class="relative h-full"
                >
                </div>

            </div>

        </section>

    </div>

</div>



{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>


<script>

    window.editorConfig = {

        saveUrl: "{{ route('simulations.saveContents', $simulation->simulation_id) }}",

        uploadUrl: "{{ route('simulations.uploadContent') }}",

        csrf: "{{ csrf_token() }}"

    };

</script>


<script src="{{ asset('js/editor.js') }}"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {


    // =========================================================
    // SET UKURAN STAGE
    // =========================================================

    const stage = document.getElementById('stage');

    if (stage) {

        const stageWidth =
            parseFloat(stage.dataset.width) || 500;

        const stageHeight =
            parseFloat(stage.dataset.height) || 500;

        stage.style.width = stageWidth + 'px';

        stage.style.height = stageHeight + 'px';

    }


    // =========================================================
    // RENDER SLOT
    // =========================================================

    document.querySelectorAll('.slot').forEach(function (slot) {

        const x =
            parseFloat(slot.dataset.x) || 0;

        const y =
            parseFloat(slot.dataset.y) || 0;

        const width =
            parseFloat(slot.dataset.width) || 0;

        const height =
            parseFloat(slot.dataset.height) || 0;


        slot.style.left = x + 'px';

        slot.style.top = y + 'px';

        slot.style.width = width + 'px';

        slot.style.height = height + 'px';

        slot.style.zIndex = 999;

    });


    // =========================================================
    // RENDER SAVED CONTENT
    // =========================================================

    document.querySelectorAll('.stage-content').forEach(function (content) {

        const x =
            parseFloat(content.dataset.x) || 0;

        const y =
            parseFloat(content.dataset.y) || 0;

        const width =
            parseFloat(content.dataset.width) || 0;

        const height =
            parseFloat(content.dataset.height) || 0;

        const layerOrder =
            parseInt(content.dataset.layerOrder) || 0;


        content.style.left = x + 'px';

        content.style.top = y + 'px';

        content.style.width = width + 'px';

        content.style.height = height + 'px';

        content.style.zIndex = layerOrder;

    });

});

</script>

@endsection