@extends('layouts.app')

@section('content')

<div class="h-screen bg-gray-900 text-white flex flex-col">

    {{-- Top Bar --}}
    <div class="flex justify-between items-center px-6 py-4 bg-gray-800 shadow">
        <div>
            <h1 class="text-xl font-semibold">
                Simulation Builder
            </h1>
            <p class="text-sm text-gray-400">
                {{ $simulation->title }}
            </p>
        </div>

        <div class="flex gap-3">
            <button id="saveBtn"
                class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">
                Save Simulation
            </button>
        </div>
    </div>

    <div class="flex flex-1">
        {{--Sidebar kanan--}}
        <div class="w-64 bg-gray-800 p-4 overflow-y-auto">
            <h2 class="text-lg font-semibold mb-4">Available Contents</h2>
            @foreach(\App\Models\Content::all() as $content)
                <div class="bg-gray-700 p-2 mb-3 rounded cursor-pointer content-item"
                    data-id="{{ $content->content_id }}"
                    data-type="{{ $content->type }}"
                    data-path="{{ $content->file_path }}">

                    {{ $content->title }}
                </div>
            @endforeach
        </div>

        {{--Stage Area--}}
        <div class="flex-1 flex justify-center items-center">
            <div id="stage" class="relative bg-black border border-gray-700 shadow-lg" style="width: {{ $simulation->canvas_width }}px; height: {{ $simulation->canvas_height }}px;">
                {{-- Render Slots --}}
                @if(!empty($layout))
                    @foreach($layout as $slot)
                        <div class="slot absolute border-2 border-dashed border-white text-xs bg-blue-500/20 pointer-events-auto"
                            data-id="{{ $slot['slot_id'] }}"
                            style="
                                left: {{ $slot['x'] }}px;
                                top: {{ $slot['y'] }}px;
                                width: {{ $slot['width'] }}px;
                                height: {{ $slot['height'] }}px; 
                                z-index: 999;
                            ">

                            Slot {{ $slot['slot_id'] }}

                        </div>
                    @endforeach
                @endif

                {{-- Render Saved Contents --}}
                @foreach($visualContents as $item)

                    @if($item->content)
                        <div class="stage-content absolute group"
                            data-content-id="{{ $item->content_id }}"
                            data-title="{{ $item->content->title }}"
                            data-type="{{ $item->content->type }}"
                            data-slot-id="{{ $item->slot_id }}"
                            data-layer-order="{{ $item->layer_order }}"
                            data-start-time="{{ $item->start_time }}"
                            data-duration="{{ $item->duration }}"
                            data-id="{{ $item->sim_content_id }}"
                            style="
                                left: {{ $item->pos_x }}px;
                                top: {{ $item->pos_y }}px;
                                width: {{ $item->width }}px;
                                height: {{ $item->height }}px;
                                border: 2px solid red;
                                z-index: {{ $item->layer_order }}
                            ">

                            @if($item->content->type === 'image')
                                <img src="/storage/{{ $item->content->file_path }}"
                                    style="width:100%;height:100%;object-fit:cover;">
                            @endif

                            @if($item->content->type === 'video')
                                <video src="/storage/{{ $item->content->file_path }}"
                                    style="width:100%;height:100%;" preload="auto"></video>
                            @endif

                        </div>
                    @endif

                @endforeach
            </div>
        </div>

        {{--Sidebare kiri--}}
        <div class="w-64 bg-gray-800 p-4 border-l border-gray-700">
            <h2 class="text-lg font-semibold mb-4">Layers</h2>

            <div id="layerPanel" class="space-y-2">
                {{-- Layer items akan di-generate via JS --}}
            </div>
        </div>
    </div>

    {{--Audio & Timeline Panel--}}
    <div class="flex w-full">
        <div class="w-64 flex-shrink-0 bg-gray-900 p-4 border-l border-gray-700">
            <h2 class="text-lg font-semibold mb-4">Audio Tracks</h2>
    
            <div id="audioPanel" class="space-y-2">
                @foreach($audioContents as $item)
                    <div class="audio-item bg-gray-700 p-2 rounded cursor-move flex justify-between items-center"
                        data-content-id="{{ $item->content_id }}"
                        data-title="{{ $item->content->title }}"
                        data-start-time="{{ $item->start_time ?? 0 }}"
                        data-duration="{{ $item->duration ?? 10 }}">

                        <span>🎵 {{ $item->content->title }}</span>
                        <button class="delete-audio text-red-500 text-xs">✕</button>

                        <audio src="/storage/{{ $item->content->file_path }}"
                            preload="auto"
                            style="display:none;"></audio>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 mb-2">
            <button id="playBtn" class="bg-green-600 px-3 py-1 rounded">▶ Play</button>
            <button id="pauseBtn" class="bg-yellow-600 px-3 py-1 rounded">⏸ Pause</button>
            <span id="currentTimeLabel" class="text-sm text-gray-400">0.00s</span>
        </div>

        <div class="flex-1 min-w-0 bg-gray-800 p-4 border-t border-gray-700">
            <h2 class="text-lg font-semibold mb-2">Timeline</h2>

            <div id="timeline"
                class="relative bg-gray-900 h-32 overflow-x-auto border border-gray-700 w-full">
                
                <div id="timelineTracks" class="relative h-full">
                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
    window.editorConfig = {
        saveUrl: "{{ route('simulations.saveContents', $simulation->simulation_id) }}",
        csrf: "{{ csrf_token() }}"
    };
</script>
<script src="{{ asset('js/editor.js') }}"></script>
@endsection