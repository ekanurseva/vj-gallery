@extends('layouts.app')

@section('content')
<div class="p-8 bg-slate-900 min-h-screen text-white">

    <h1 class="text-2xl font-bold mb-6">
        Stage Builder - {{ $stage_template->name }}
    </h1>

    <div class="mb-4 flex gap-3">
        <button id="addSlot"
                class="bg-blue-600 px-4 py-2 rounded">
            + Tambah Slot
        </button>

        <button id="saveLayout"
                class="bg-green-600 px-4 py-2 rounded">
            Simpan Layout
        </button>
    </div>

    <div id="canvas"
         style="
         position:relative;
         width:{{ $stage_template->canvas_width }}px;
         height:{{ $stage_template->canvas_height }}px;
         background:#111;
         border:2px solid #555;
         overflow:hidden;
         ">

        {{-- Background image --}}
        @if($stage_template->background_path)
            <img src="{{ asset('storage/'.$stage_template->background_path) }}"
                 style="position:absolute; width:100%; height:100%; object-fit:cover;">
        @endif

    </div>

</div>

<script>
    let savedLayout = @json($stage_template->layout_json ? json_decode($stage_template->layout_json) : []);
</script>

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

<script>
let slotCounter = 0;

// Render slot yang sudah ada
if (savedLayout.length > 0) {

    savedLayout.forEach(slot => {

        let el = document.createElement('div');
        el.classList.add('slot');

        el.dataset.slot = slot.slot_id;

        el.style.position = 'absolute';
        el.style.left = slot.x + 'px';
        el.style.top = slot.y + 'px';
        el.style.width = slot.width + 'px';
        el.style.height = slot.height + 'px';
        el.style.border = '2px dashed white';
        el.style.background = 'rgba(255,255,255,0.1)';
        el.style.cursor = 'move';

        document.getElementById('canvas').appendChild(el);

        makeInteractable(el);
    });

}

// Tambah slot baru
document.getElementById('addSlot').addEventListener('click', function () {

    let slot = document.createElement('div');
    slot.classList.add('slot');

    slot.dataset.slot = 'slot_' + slotCounter;

    slot.style.position = 'absolute';
    slot.style.left = '50px';
    slot.style.top = '50px';
    slot.style.width = '200px';
    slot.style.height = '150px';
    slot.style.border = '2px dashed white';
    slot.style.background = 'rgba(255,255,255,0.1)';
    slot.style.cursor = 'move';

    document.getElementById('canvas').appendChild(slot);

    makeInteractable(slot);

    slotCounter++;
});

// Fungsi interact
function makeInteractable(target) {

    interact(target)
        .draggable({
            listeners: {
                move (event) {
                    let target = event.target;

                    let x = parseFloat(target.style.left) || 0;
                    let y = parseFloat(target.style.top) || 0;

                    x += event.dx;
                    y += event.dy;

                    target.style.left = x + 'px';
                    target.style.top = y + 'px';
                }
            }
        })
        .resizable({
            edges: { left: true, right: true, bottom: true, top: true },
            listeners: {
                move (event) {
                    let target = event.target;

                    let x = parseFloat(target.style.left) || 0;
                    let y = parseFloat(target.style.top) || 0;

                    target.style.width = event.rect.width + 'px';
                    target.style.height = event.rect.height + 'px';

                    x += event.deltaRect.left;
                    y += event.deltaRect.top;

                    target.style.left = x + 'px';
                    target.style.top = y + 'px';
                }
            }
        });
}

// Simpan layout
document.getElementById('saveLayout').addEventListener('click', function () {

    let slots = [];

    document.querySelectorAll('.slot').forEach(el => {

        slots.push({
            slot_id: el.dataset.slot,
            x: parseFloat(el.style.left) || 0,
            y: parseFloat(el.style.top) || 0,
            width: el.offsetWidth,
            height: el.offsetHeight
        });

    });

    fetch("{{ route('admin.stage_templates.saveLayout', $stage_template) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            layout: slots
        })
    })
    .then(res => res.json())
    .then(data => {
        alert("Layout berhasil disimpan!");
    });

});
</script>

@endsection