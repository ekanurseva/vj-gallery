@extends('layouts.app')

@section('content')

<div class="p-8 bg-slate-900 min-h-screen text-white">

    {{--HEADER--}}

    <h1 class="text-2xl font-bold mb-6 text-center">
        Detail Template
    </h1>


    {{--INFORMASI TEMPLATE--}}

    <div class="bg-slate-800 p-6 rounded-xl mb-6 space-y-3">

        <p>
            <strong>Nama:</strong>
            {{ $stage_template->name }}
        </p>

        <p>
            <strong>Deskripsi:</strong>
            {{ $stage_template->description }}
        </p>

        <p>
            <strong>Canvas:</strong>
            {{ $stage_template->canvas_width }}
            ×
            {{ $stage_template->canvas_height }}
        </p>

        <p>
            <strong>Background Type:</strong>
            {{ $stage_template->background_type }}
        </p>


        {{-- Background --}}
        @if($stage_template->background_path)

            <div>

                <strong>Background:</strong>

                <br>

                <img
                    src="{{ asset('storage/' . $stage_template->background_path) }}"
                    alt="Background Template"
                    class="mt-2 max-w-md rounded"
                >

            </div>

        @endif


        {{-- Audio --}}
        @if($stage_template->audio_path)

            <audio controls loop>

                <source
                    src="{{ asset('storage/' . $stage_template->audio_path) }}"
                >

                Browser tidak mendukung audio.

            </audio>

        @endif

    </div>



    {{--CANVAS--}}

    <div class="flex justify-center">

        <div
            id="canvas"
            style="
                position: relative;
                width: {{ $stage_template->canvas_width }}px;
                height: {{ $stage_template->canvas_height }}px;
                background: #111;
                border: 2px solid #555;
                overflow: hidden;
            "
        >


            {{--BACKGROUND TEMPLATE--}}

            @if($stage_template->background_path)

                <img
                    src="{{ asset('storage/' . $stage_template->background_path) }}"
                    alt="Background Template"
                    style="
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        z-index: 0;
                        pointer-events: none;
                    "
                >

            @endif



            {{--RENDER SEMUA SLOT--}}

            @if($stage_template->layout_json)

                @foreach(json_decode($stage_template->layout_json) as $slot)

                    <div
                        class="stage-slot"
                        data-slot-id="{{ $slot->slot_id }}"
                        style="
                            position: absolute;
                            left: {{ $slot->x }}px;
                            top: {{ $slot->y }}px;
                            width: {{ $slot->width }}px;
                            height: {{ $slot->height }}px;
                            overflow: hidden;
                            border: 2px dashed rgba(255,255,255,0.7);
                            box-sizing: border-box;
                            background: #111;
                            z-index: 5;
                        "
                    >


                        {{--VIDEO PREVIEW--}}

                        <video
                            class="slot-preview-video"
                            data-slot="{{ $slot->slot_id }}"
                            muted
                            autoplay
                            loop
                            playsinline
                            preload="auto"
                            style="
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                                height: 100%;
                                object-fit: fill;
                                display: block;
                                visibility: visible;
                                opacity: 1;
                                z-index: 6;
                                pointer-events: none;
                            "
                        >

                            <source
                                src="{{ asset('videos/Vj-gallery.mp4') }}"
                                type="video/mp4"
                            >

                            Browser tidak mendukung video.

                        </video>



                        {{--LABEL SLOT--}}

                        <span
                            style="
                                position: absolute;
                                left: 5px;
                                top: 5px;
                                z-index: 10;
                                color: white;
                                background: rgba(0,0,0,0.55);
                                padding: 2px 5px;
                                border-radius: 3px;
                                font-size: 11px;
                                pointer-events: none;
                            "
                        >
                            {{ $slot->slot_id }}
                        </span>


                    </div>

                @endforeach

            @endif

        </div>

    </div>

</div>



{{--VIDEO SCRIPT--}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const videos =
            document.querySelectorAll(
                '.slot-preview-video'
            );


        console.log(
            'Jumlah slot video:',
            videos.length
        );


        /* | Load setiap video */

        videos.forEach(
            function (video, index) {

                console.log(
                    'Memuat video slot:',
                    video.dataset.slot
                );


                /*| Pastikan muted*/

                video.muted = true;

                video.volume = 0;


                /*| Load ulang video*/

                video.load();


                /*| Jalankan ketika video siap*/

                video.addEventListener(
                    'loadeddata',
                    function () {

                        video.play()
                            .then(function () {

                                console.log(
                                    'Video berhasil diputar:',
                                    video.dataset.slot
                                );

                            })
                            .catch(function (error) {

                                console.warn(
                                    'Video gagal diputar:',
                                    video.dataset.slot,
                                    error
                                );

                            });

                    },
                    {
                        once: true
                    }
                );


                /*| Coba play langsung*/

                video.play()
                    .catch(function () {

                        /* Browser mungkin belum siap. | loadeddata akan mencoba lagi.*/

                    });

            }
        );

    }
);

</script>

@endsection