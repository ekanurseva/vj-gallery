@extends('layouts.app')

@section('content')

<div style="padding:40px;">

    <h1 class="text-3xl font-bold mb-6 text-center">
        Gallery Karya
    </h1>

    <form method="GET" action="{{ route('gallery.index') }}" id="filterForm" class="flex gap-3 flex-wrap mb-6">

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..." class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">

        <select name="category_id" id="kategoriFilter" class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}"
                    {{ request('category_id') == $cat->category_id ? 'selected':'' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>          

        <select name="media_type" class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">
            <option value="">Semua Media</option>
            <option value="jpg">Image</option>
            <option value="mp4">Video</option>
            <option value="mp3">Audio</option>
        </select>

        <input type="number" name="min_width" placeholder="Min Width" class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">

        <input type="number" name="min_duration" placeholder="Min Durasi (detik)" class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">

    </form>

    <script>
        const form = document.getElementById('filterForm');
        const inputs = form.querySelectorAll('input, select');

        let typingTimer;

        inputs.forEach(input => {

            // Untuk input text & number → debounce
            if (input.type === 'text' || input.type === 'number') {
                input.addEventListener('keyup', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }

            // Untuk select → langsung submit
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', function() {
                    form.submit();
                });
            }
        });
    </script>

    <div class="grid-karya">

        @foreach($contents as $content)

        @php
        $extension = strtolower(pathinfo($content->file_path, PATHINFO_EXTENSION));
        $fileUrl = asset('storage/'.$content->file_path);
        @endphp

        <div class="card-karya">
            
            <div class="flex justify-between mb-4 items-center">
                <div>
                    <strong>{{ $content->category->name ?? '-' }}</strong> - {{ $content->title }}
                </div>

                <a href="{{ route('gallery.download',$content->content_id) }}" class="inline-block mt-2 px-3 py-1 bg-green-600 rounded">
                    Download
                </a>
            </div>

            <div class="preview-box" onclick="openModal('{{ $fileUrl }}', '{{ $extension }}')">

                {{-- IMAGE --}}
                @if(in_array($extension, ['jpg','jpeg','png','gif','webp']))
                    <img src="{{ $fileUrl }}">

                {{-- VIDEO --}}
                @elseif(in_array($extension, ['mp4','mov','webm']))
                    <video muted>
                        <source src="{{ $fileUrl }}">
                    </video>

                {{-- AUDIO --}}
                @elseif(in_array($extension, ['mp3','wav','ogg']))
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#334155;">
                        <audio controls style="width:100%;">
                            <source src="{{ $fileUrl }}">
                            Browser tidak mendukung audio.
                        </audio>                
                    </div>
                @endif

            </div>
        </div>

        @endforeach

    </div>

    <div class="mt-6">
        {{ $contents->appends(request()->query())->links() }}
    </div>

    <!-- MODAL -->
    <div id="previewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); justify-content:center; align-items:center; z-index:999;">
        <div id="modalContent" style="max-width:90%; max-height:90%;"></div>
    </div>

    <script>
        function openModal(url, ext) {
            let modal = document.getElementById('previewModal');
            let content = document.getElementById('modalContent');

            if(['jpg','jpeg','png','gif','webp'].includes(ext)){
                content.innerHTML = `<img src="${url}" style="max-width:100%; max-height:90vh;">`;
            }

            if(['mp4','mov','webm'].includes(ext)){
                content.innerHTML = `<video controls autoplay style="max-width:100%; max-height:90vh;">
                                        <source src="${url}">
                                    </video>`;
            }

            modal.style.display = "flex";
        }

        document.getElementById('previewModal').onclick = function(){
            this.style.display = "none";
        }
    </script>
</div>

@endsection