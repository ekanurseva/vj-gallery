@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-[#0A192F] text-white">

    
    {{-- HEADER --}}
    

    <div class="flex justify-between items-center px-6 py-4 bg-gray-800 shadow">

        <div>
            <h1 class="text-xl font-semibold">
                Simulation Reference
            </h1>

            <p class="text-sm text-gray-400">
                {{ $simulation->title }}
            </p>
        </div>

        <div class="flex items-center gap-3">

            <span class="bg-purple-500/10 border border-purple-400/20 text-purple-300 text-xs px-3 py-1 rounded-full">
                Template Simulation
            </span>

            <a
                href="{{ route('vj.templates.index') }}"
                class="bg-gray-600 hover:bg-gray-500 px-4 py-2 rounded-lg text-sm"
            >
                Kembali
            </a>

        </div>

    </div>


    
    {{-- INFORMATION --}}
    

    <div class="px-6 py-4 bg-gray-900 border-b border-gray-700">

        <div class="flex flex-wrap gap-6 text-sm">

            <div>
                <span class="text-gray-500">
                    Canvas
                </span>

                <span class="ml-2 text-white">
                    {{ $simulation->canvas_width }}
                    ×
                    {{ $simulation->canvas_height }}
                </span>
            </div>


            <div>
                <span class="text-gray-500">
                    Konten
                </span>

                <span class="ml-2 text-white">
                    {{ $simulation->simulationContents->count() }}
                    item
                </span>
            </div>


            @if($simulation->template)

                <div>

                    <span class="text-gray-500">
                        Tema
                    </span>

                    <span class="ml-2 text-white">

                        @forelse($simulation->template->themes as $theme)

                            {{ $theme->name }}

                            @if(!$loop->last)
                                ,
                            @endif

                        @empty

                            Tidak ada

                        @endforelse

                    </span>

                </div>

            @endif

        </div>

    </div>


    
    {{-- MAIN AREA --}}
    

    <div class="flex flex-1">

        
        {{-- CONTENT INFO --}}
        

        <div class="w-64 bg-gray-800 p-4 max-h-[85vh] overflow-y-auto">

            <h2 class="text-lg font-semibold mb-4">
                Contents
            </h2>


            @forelse($simulation->simulationContents as $item)

                @if($item->content)

                    <div class="bg-gray-700 p-3 mb-3 rounded-lg">

                        <p class="text-sm font-medium">
                            {{ $item->content->title }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            {{ ucfirst($item->content->type) }}
                        </p>

                    </div>

                @endif

            @empty

                <p class="text-sm text-gray-500">
                    Tidak ada konten.
                </p>

            @endforelse

        </div>


        
        {{-- STAGE --}}
        

        <div class="flex-1 flex justify-center items-center p-6">

            <div
                id="reference-stage"

                class="relative bg-black border border-gray-700 shadow-lg"

                data-width="{{ $simulation->canvas_width }}"
                data-height="{{ $simulation->canvas_height }}"
            >

                
                {{-- BACKGROUND --}}
                

                @if(
                    $simulation->template &&
                    $simulation->template->background_path
                )

                    @if($simulation->template->background_type === 'image')

                        <img
                            src="{{ asset('storage/' . $simulation->template->background_path) }}"
                            class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                        >

                    @elseif($simulation->template->background_type === 'video')

                        <video
                            class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                            autoplay
                            muted
                            loop
                            playsinline
                        >

                            <source
                                src="{{ asset('storage/' . $simulation->template->background_path) }}"
                                type="video/mp4"
                            >

                        </video>

                    @endif

                @endif


                
                {{-- SLOTS --}}
                

                @foreach($layout as $slot)

                    <div
                        class="reference-slot absolute border-2 border-dashed border-white/40 bg-blue-500/10 pointer-events-none"

                        data-x="{{ $slot['x'] }}"
                        data-y="{{ $slot['y'] }}"
                        data-width="{{ $slot['width'] }}"
                        data-height="{{ $slot['height'] }}"
                        data-z-index="10"
                    >

                        <span class="text-xs text-gray-300 bg-black/50 px-1">
                            {{ $slot['slot_id'] }}
                        </span>

                    </div>

                @endforeach


                
                {{-- VISUAL CONTENT --}}
                

                @foreach($visualContents as $item)

                    @if($item->content)

                        <div
                            class="reference-content absolute pointer-events-none"

                            data-x="{{ $item->pos_x }}"
                            data-y="{{ $item->pos_y }}"
                            data-width="{{ $item->width }}"
                            data-height="{{ $item->height }}"
                            data-opacity="{{ $item->opacity ?? 1 }}"
                            data-rotation="{{ $item->rotation ?? 0 }}"
                            data-z-index="{{ $item->layer_order }}"
                        >

                            @if($item->content->type === 'image')

                                <img
                                    src="{{ asset('storage/' . $item->content->file_path) }}"
                                    class="w-full h-full object-cover"
                                >

                            @elseif($item->content->type === 'video')

                                <video
                                    src="{{ asset('storage/' . $item->content->file_path) }}"
                                    class="w-full h-full"
                                    muted
                                    loop
                                    autoplay
                                    playsinline
                                ></video>

                            @endif

                        </div>

                    @endif

                @endforeach

            </div>

        </div>


        
        {{-- LAYERS --}}
        

        <div class="w-64 bg-gray-800 p-4 border-l border-gray-700 max-h-[85vh] overflow-y-auto">

            <h2 class="text-lg font-semibold mb-4">
                Layers
            </h2>


            @forelse(
                $visualContents->sortByDesc('layer_order')
                as $item
            )

                @if($item->content)

                    <div class="bg-gray-700 p-3 mb-2 rounded-lg">

                        <div class="text-sm">
                            {{ $item->content->title }}
                        </div>

                        <div class="text-xs text-gray-400 mt-1">
                            Layer {{ $item->layer_order }}
                        </div>

                    </div>

                @endif

            @empty

                <p class="text-sm text-gray-500">
                    Tidak ada layer.
                </p>

            @endforelse

        </div>

    </div>


    
    {{-- AUDIO / TIMELINE --}}
    

    <div class="flex w-full border-t border-gray-700">

        
        {{-- AUDIO --}}
        

        <div class="w-64 flex-shrink-0 bg-gray-900 p-4">

            <h2 class="text-lg font-semibold mb-4">
                Audio Tracks
            </h2>


            @forelse($audioContents as $item)

                @if($item->content)

                    <div class="bg-gray-700 p-3 rounded-lg mb-2">

                        <div class="text-sm">
                            🎵 {{ $item->content->title }}
                        </div>

                        <div class="text-xs text-gray-400 mt-1">

                            {{ $item->start_time ?? 0 }}s
                            -
                            {{ ($item->start_time ?? 0) + ($item->duration ?? 0) }}s

                        </div>

                    </div>

                @endif

            @empty

                <p class="text-sm text-gray-500">
                    Tidak ada audio.
                </p>

            @endforelse

        </div>


        
        {{-- TIMELINE --}}
        

        <div class="flex-1 bg-gray-800 p-4">

            <h2 class="text-lg font-semibold mb-2">
                Timeline
            </h2>


            <div
                id="reference-timeline"
                class="relative bg-gray-900 border border-gray-700 rounded overflow-x-auto"
                style="height: 160px;"
            >

                @foreach($simulation->simulationContents as $item)

                    @if($item->content)

                        <div
                            class="reference-timeline-item absolute bg-purple-600/60 border border-purple-400 rounded px-2 py-1 text-xs text-white whitespace-nowrap overflow-hidden"

                            data-start-time="{{ $item->start_time ?? 0 }}"
                            data-duration="{{ $item->duration ?? 1 }}"
                            data-index="{{ $loop->index }}"
                        >

                            {{ $item->content->title }}

                        </div>

                    @endif

                @endforeach

            </div>

        </div>

    </div>

</div>



{{-- REFERENCE PAGE JAVASCRIPT --}}


<script>

document.addEventListener('DOMContentLoaded', function () {

    /* REFERENCE STAGE */

    const stage = document.getElementById('reference-stage');

    if (stage) {

        const stageWidth = Number(stage.dataset.width);
        const stageHeight = Number(stage.dataset.height);

        stage.style.width = stageWidth + 'px';
        stage.style.height = stageHeight + 'px';

    }


    /* SLOTS */

    const slots = document.querySelectorAll('.reference-slot');

    slots.forEach(function (slot) {

        const x = Number(slot.dataset.x);
        const y = Number(slot.dataset.y);

        const width = Number(slot.dataset.width);
        const height = Number(slot.dataset.height);

        const zIndex = Number(slot.dataset.zIndex);

        slot.style.left = x + 'px';
        slot.style.top = y + 'px';

        slot.style.width = width + 'px';
        slot.style.height = height + 'px';

        slot.style.zIndex = zIndex;

    });


    /* VISUAL CONTENT */

    const contents = document.querySelectorAll('.reference-content');

    contents.forEach(function (content) {

        const x = Number(content.dataset.x);
        const y = Number(content.dataset.y);

        const width = Number(content.dataset.width);
        const height = Number(content.dataset.height);

        const opacity = Number(content.dataset.opacity);
        const rotation = Number(content.dataset.rotation);

        const zIndex = Number(content.dataset.zIndex);

        content.style.left = x + 'px';
        content.style.top = y + 'px';

        content.style.width = width + 'px';
        content.style.height = height + 'px';

        content.style.opacity = opacity;

        content.style.transform =
            'rotate(' + rotation + 'deg)';

        content.style.zIndex = zIndex;

    });


    /* TIMELINE */

    const timeline =
        document.getElementById('reference-timeline');

    const timelineItems =
        document.querySelectorAll('.reference-timeline-item');


    if (timeline && timelineItems.length > 0) {

        timelineItems.forEach(function (item) {

            const startTime =
                parseFloat(item.dataset.startTime) || 0;

            const duration =
                parseFloat(item.dataset.duration) || 1;

            const index =
                parseInt(item.dataset.index) || 0;


            const left =
                startTime * 10;

            const width =
                Math.max(duration * 10, 50);

            const top =
                index * 32;


            item.style.left =
                left + 'px';

            item.style.top =
                top + 'px';

            item.style.width =
                width + 'px';

            item.style.height =
                '26px';

            item.style.display =
                'block';

            item.style.zIndex =
                '20';

        });

    }

});

</script>

@endsection