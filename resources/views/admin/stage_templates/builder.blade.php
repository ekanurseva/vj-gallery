@extends('layouts.app')

@section('content')

<div class="p-8 bg-slate-900 min-h-screen text-white">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <h1 class="text-2xl font-bold mb-6">
        Stage Builder - {{ $stage_template->name }}
    </h1>


    {{-- =========================================================
         BUTTON
    ========================================================== --}}
    <div class="mb-4 flex gap-3">

        <button
            id="addSlot"
            type="button"
            class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded"
        >
            + Tambah Slot
        </button>

        <button
            id="saveLayout"
            type="button"
            class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded"
        >
            Simpan Layout
        </button>

    </div>


    {{-- =========================================================
         CANVAS
    ========================================================== --}}
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

        {{-- =====================================================
             BACKGROUND TEMPLATE
        ====================================================== --}}
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

    </div>

</div>


{{-- =============================================================
     INTERACT.JS
============================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | DATA LAYOUT DARI DATABASE
    |--------------------------------------------------------------------------
    */

    const savedLayout = @json(
        $stage_template->layout_json
            ? json_decode($stage_template->layout_json)
            : []
    );


    /*
    |--------------------------------------------------------------------------
    | URL VIDEO PREVIEW
    |--------------------------------------------------------------------------
    |
    | File berada di:
    |
    | public/videos/Vj-gallery.mp4
    |
    */

    const previewVideoUrl =
        "{{ asset('videos/Vj-gallery.mp4') }}";


    /*
    |--------------------------------------------------------------------------
    | CANVAS UTAMA
    |--------------------------------------------------------------------------
    */

    const canvas =
        document.getElementById('canvas');


    /*
    |--------------------------------------------------------------------------
    | COUNTER SLOT
    |--------------------------------------------------------------------------
    */

    let slotCounter = 0;


    /*
    |--------------------------------------------------------------------------
    | Tentukan nomor slot berikutnya
    |--------------------------------------------------------------------------
    */

    if (savedLayout.length > 0) {

        savedLayout.forEach(function (slot) {

            const slotId =
                String(slot.slot_id || '');

            const match =
                slotId.match(/^slot_(\d+)$/);

            if (match) {

                const number =
                    parseInt(match[1]);

                if (number >= slotCounter) {

                    slotCounter =
                        number + 1;

                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Membuat Slot
    |--------------------------------------------------------------------------
    */

    function createSlot(slotData) {

        /*
        |--------------------------------------------------------------
        | Container Slot
        |--------------------------------------------------------------
        */

        const slot =
            document.createElement('div');


        slot.classList.add(
            'slot'
        );


        /*
        |--------------------------------------------------------------
        | ID Slot
        |--------------------------------------------------------------
        */

        slot.dataset.slot =
            slotData.slot_id;


        /*
        |--------------------------------------------------------------
        | Posisi
        |--------------------------------------------------------------
        */

        slot.style.position =
            'absolute';

        slot.style.left =
            (parseFloat(slotData.x) || 0) + 'px';

        slot.style.top =
            (parseFloat(slotData.y) || 0) + 'px';


        /*
        |--------------------------------------------------------------
        | Ukuran
        |--------------------------------------------------------------
        */

        slot.style.width =
            (parseFloat(slotData.width) || 200) + 'px';

        slot.style.height =
            (parseFloat(slotData.height) || 150) + 'px';


        /*
        |--------------------------------------------------------------
        | Tampilan Slot
        |--------------------------------------------------------------
        */

        slot.style.border =
            '2px dashed white';

        slot.style.background =
            '#111';

        slot.style.cursor =
            'move';

        slot.style.overflow =
            'hidden';

        slot.style.boxSizing =
            'border-box';

        slot.style.zIndex =
            '5';


        /*
        |--------------------------------------------------------------------------
        | VIDEO PREVIEW
        |--------------------------------------------------------------------------
        |
        | SETIAP SLOT mempunyai video sendiri.
        |
        */

        const video =
            document.createElement('video');


        video.classList.add(
            'slot-preview-video'
        );


        /*
        |--------------------------------------------------------------
        | Sumber Video
        |--------------------------------------------------------------
        */

        video.src =
            previewVideoUrl;


        /*
        |--------------------------------------------------------------
        | Video Settings
        |--------------------------------------------------------------
        */

        video.muted =
            true;

        video.autoplay =
            true;

        video.loop =
            true;

        video.playsInline =
            true;

        video.preload =
            'auto';


        /*
        |--------------------------------------------------------------
        | Tampilan Video
        |--------------------------------------------------------------
        |
        | width 100%
        | height 100%
        |
        | Jadi video 1920x1080 akan mengikuti ukuran slot.
        |
        */

        video.style.position =
            'absolute';

        video.style.left =
            '0';

        video.style.top =
            '0';

        video.style.width =
            '100%';

        video.style.height =
            '100%';

        video.style.objectFit =
            'fill';

        video.style.display =
            'block';

        video.style.margin =
            '0';

        video.style.padding =
            '0';

        video.style.pointerEvents =
            'none';


        /*
        |--------------------------------------------------------------------------
        | Masukkan Video ke Slot
        |--------------------------------------------------------------------------
        */

        slot.appendChild(
            video
        );


        /*
        |--------------------------------------------------------------------------
        | LABEL SLOT
        |--------------------------------------------------------------------------
        */

        const label =
            document.createElement('span');


        label.textContent =
            'Slot ' + slotData.slot_id;


        label.style.position =
            'absolute';

        label.style.left =
            '5px';

        label.style.top =
            '5px';

        label.style.zIndex =
            '10';

        label.style.color =
            'white';

        label.style.background =
            'rgba(0,0,0,0.55)';

        label.style.padding =
            '2px 5px';

        label.style.borderRadius =
            '3px';

        label.style.fontSize =
            '11px';

        label.style.pointerEvents =
            'none';


        /*
        |--------------------------------------------------------------------------
        | Masukkan Label
        |--------------------------------------------------------------------------
        */

        slot.appendChild(
            label
        );


        /*
        |--------------------------------------------------------------------------
        | Masukkan Slot ke Canvas
        |--------------------------------------------------------------------------
        */

        canvas.appendChild(
            slot
        );


        /*
        |--------------------------------------------------------------------------
        | Jalankan Video
        |--------------------------------------------------------------------------
        */

        video.play()
            .catch(function () {

                /*
                | Browser biasanya mengizinkan autoplay
                | karena video sudah muted.
                */

                console.warn(
                    'Autoplay video ditolak oleh browser.'
                );

            });


        /*
        |--------------------------------------------------------------------------
        | Aktifkan Drag & Resize
        |--------------------------------------------------------------------------
        */

        makeInteractable(
            slot
        );


        return slot;

    }


    /*
    |--------------------------------------------------------------------------
    | RENDER SLOT YANG SUDAH TERSIMPAN
    |--------------------------------------------------------------------------
    */

    if (savedLayout.length > 0) {

        savedLayout.forEach(function (slot) {

            createSlot({

                slot_id:
                    slot.slot_id,

                x:
                    slot.x,

                y:
                    slot.y,

                width:
                    slot.width,

                height:
                    slot.height

            });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH SLOT BARU
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('addSlot')
        .addEventListener(
            'click',
            function () {

                createSlot({

                    slot_id:
                        'slot_' + slotCounter,

                    x:
                        50,

                    y:
                        50,

                    width:
                        200,

                    height:
                        150

                });


                slotCounter++;

            }
        );


    /*
    |--------------------------------------------------------------------------
    | INTERACT.JS
    |--------------------------------------------------------------------------
    */

    function makeInteractable(target) {

        interact(target)


            /*
            |--------------------------------------------------------------
            | DRAG
            |--------------------------------------------------------------
            */

            .draggable({

                listeners: {

                    move(event) {

                        const element =
                            event.target;


                        let x =
                            parseFloat(
                                element.style.left
                            ) || 0;


                        let y =
                            parseFloat(
                                element.style.top
                            ) || 0;


                        /*
                        |--------------------------------------------------
                        | Perubahan posisi
                        |--------------------------------------------------
                        */

                        x += event.dx;

                        y += event.dy;


                        /*
                        |--------------------------------------------------
                        | Batas kiri
                        |--------------------------------------------------
                        */

                        x =
                            Math.max(
                                0,
                                x
                            );


                        /*
                        |--------------------------------------------------
                        | Batas atas
                        |--------------------------------------------------
                        */

                        y =
                            Math.max(
                                0,
                                y
                            );


                        /*
                        |--------------------------------------------------
                        | Batas kanan
                        |--------------------------------------------------
                        */

                        const maxX =
                            canvas.clientWidth -
                            element.offsetWidth;


                        if (x > maxX) {

                            x =
                                Math.max(
                                    0,
                                    maxX
                                );

                        }


                        /*
                        |--------------------------------------------------
                        | Batas bawah
                        |--------------------------------------------------
                        */

                        const maxY =
                            canvas.clientHeight -
                            element.offsetHeight;


                        if (y > maxY) {

                            y =
                                Math.max(
                                    0,
                                    maxY
                                );

                        }


                        /*
                        |--------------------------------------------------
                        | Simpan posisi
                        |--------------------------------------------------
                        */

                        element.style.left =
                            x + 'px';

                        element.style.top =
                            y + 'px';

                    }

                }

            })


            /*
            |--------------------------------------------------------------
            | RESIZE
            |--------------------------------------------------------------
            */

            .resizable({

                edges: {

                    left:
                        true,

                    right:
                        true,

                    top:
                        true,

                    bottom:
                        true

                },


                listeners: {

                    move(event) {

                        const element =
                            event.target;


                        let x =
                            parseFloat(
                                element.style.left
                            ) || 0;


                        let y =
                            parseFloat(
                                element.style.top
                            ) || 0;


                        /*
                        |--------------------------------------------------
                        | Ukuran minimum
                        |--------------------------------------------------
                        */

                        const minWidth =
                            50;

                        const minHeight =
                            50;


                        let width =
                            Math.max(
                                event.rect.width,
                                minWidth
                            );


                        let height =
                            Math.max(
                                event.rect.height,
                                minHeight
                            );


                        /*
                        |--------------------------------------------------
                        | Posisi ketika resize
                        |--------------------------------------------------
                        */

                        x +=
                            event.deltaRect.left;

                        y +=
                            event.deltaRect.top;


                        /*
                        |--------------------------------------------------
                        | Jangan keluar canvas
                        |--------------------------------------------------
                        */

                        x =
                            Math.max(
                                0,
                                x
                            );

                        y =
                            Math.max(
                                0,
                                y
                            );


                        /*
                        |--------------------------------------------------
                        | Batasi width
                        |--------------------------------------------------
                        */

                        const maxWidth =
                            canvas.clientWidth -
                            x;


                        if (
                            width >
                            maxWidth
                        ) {

                            width =
                                maxWidth;

                        }


                        /*
                        |--------------------------------------------------
                        | Batasi height
                        |--------------------------------------------------
                        */

                        const maxHeight =
                            canvas.clientHeight -
                            y;


                        if (
                            height >
                            maxHeight
                        ) {

                            height =
                                maxHeight;

                        }


                        /*
                        |--------------------------------------------------
                        | Terapkan ukuran
                        |--------------------------------------------------
                        */

                        element.style.width =
                            width + 'px';

                        element.style.height =
                            height + 'px';


                        /*
                        |--------------------------------------------------
                        | Terapkan posisi
                        |--------------------------------------------------
                        */

                        element.style.left =
                            x + 'px';

                        element.style.top =
                            y + 'px';

                    }

                }

            });

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN LAYOUT
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('saveLayout')
        .addEventListener(
            'click',
            function () {

                const slots = [];


                /*
                |--------------------------------------------------------------
                | Ambil semua slot
                |--------------------------------------------------------------
                */

                document
                    .querySelectorAll('.slot')
                    .forEach(
                        function (element) {

                            slots.push({

                                slot_id:
                                    element.dataset.slot,

                                x:
                                    parseFloat(
                                        element.style.left
                                    ) || 0,

                                y:
                                    parseFloat(
                                        element.style.top
                                    ) || 0,

                                width:
                                    element.offsetWidth,

                                height:
                                    element.offsetHeight

                            });

                        }
                    );


                /*
                |--------------------------------------------------------------
                | Kirim ke Laravel
                |--------------------------------------------------------------
                */

                fetch(
                    "{{ route('admin.stage_templates.saveLayout', $stage_template) }}",
                    {

                        method:
                            "POST",

                        headers: {

                            "Content-Type":
                                "application/json",

                            "X-CSRF-TOKEN":
                                "{{ csrf_token() }}"

                        },

                        body:
                            JSON.stringify({

                                layout:
                                    slots

                            })

                    }
                )


                /*
                |--------------------------------------------------------------
                | Response
                |--------------------------------------------------------------
                */

                .then(function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'Gagal menyimpan layout.'
                        );

                    }

                    return response.json();

                })


                .then(function () {

                    alert(
                        "Layout berhasil disimpan!"
                    );

                })


                .catch(function (error) {

                    console.error(
                        error
                    );

                    alert(
                        "Terjadi kesalahan saat menyimpan layout."
                    );

                });

            }
        );

</script>

@endsection