@extends('layouts.app')

@section('content')
<div class="p-8 bg-slate-900 min-h-screen text-white">

    <h1 class="text-2xl font-bold mb-6 text-center">Detail Template</h1>

    <div class="bg-slate-800 p-6 rounded-xl mb-4 space-y-3">

        <p><strong>Nama:</strong> {{ $stage_template->name }}</p>
        <p><strong>Deskripsi:</strong> {{ $stage_template->description }}</p>
        <p><strong>Canvas:</strong>
            {{ $stage_template->canvas_width }} x
            {{ $stage_template->canvas_height }}
        </p>
        <p><strong>Background Type:</strong>
            {{ $stage_template->background_type }}
        </p>

        <!-- @if($stage_template->background_path)
            <div>
                <strong>Background:</strong><br>
                <img src="{{ asset('storage/'.$stage_template->background_path) }}"
                     class="mt-2 max-w-md rounded">
            </div>
        @endif -->

        @if($stage_template->audio_path)
            <audio controls loop>
                <source src="{{ asset('storage/'.$stage_template->audio_path) }}">
                Browser tidak mendukung audio.
            </audio>
        @endif

    </div>

    <div class="flex justify-center">
        <div id="canvas" style="position:relative; width:{{ $stage_template->canvas_width }}px; height:{{ $stage_template->canvas_height }}px; background:#111; border:2px solid #555; overflow:hidden;">
    
            @if($stage_template->background_path)
                <img src="{{ asset('storage/'.$stage_template->background_path) }}" style="position:absolute; width:100%; height:100%; object-fit:cover;">
            @endif
    
            @if($stage_template->layout_json)
                @foreach(json_decode($stage_template->layout_json) as $slot)
                    <!-- <div style="position:absolute;left:{{ $slot->x }}px;top:{{ $slot->y }}px;width:{{ $slot->width }}px;height:{{ $slot->height }}px;border:2px solid red;"> -->
                        <div style="position:absolute;left:{{ $slot->x }}px;top:{{ $slot->y }}px;width:{{ $slot->width }}px;height:{{ $slot->height }}px;overflow:hidden;">
                            <video width="100%" height="100%" autoplay loop muted>
                                <source src="{{ asset('storage/contents/W1eqXLUmdlGUl5zo97WyyxTdbnoFLOGXBNDvNwpy.mp4') }}" type="video/mp4">
                            </video>
                        </div>
                    <!-- </div> -->
                @endforeach
            @endif
    
        </div>
    </div>
</div>
@endsection