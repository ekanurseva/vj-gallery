@extends('layouts.app')

@section('content')

<div style="padding: 40px;">

    @if(session('success'))
        <div style="background:#16a34a; color:white; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center;
        ">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#dc2626;color:white;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-3xl font-bold mb-6 text-center">
        Kelola Konten Karya
    </h1>

    <div class="flex justify-between gap-2 items-center mb-6">
        <a href="{{ route('vj.contents.create') }}" class="bg-cyan-400 text-[#0A192F] px-6 py-2 rounded-lg font-semibold hover:bg-cyan-300 transition">
            + Tambah Karya
        </a>

        {{-- FILTER --}}
        <form method="GET" action="{{ route('vj.contents.index') }}" id="filterForm">
            <input type="text" name="search" value="{{ request('search') }}" id="searchInput" placeholder="Cari judul..." class="flex-2 bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">

            <select name="category_id" id="kategoriFilter" class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}"
                        {{ request('category_id') == $cat->category_id ? 'selected':'' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>            
        </form>

        <script>
            const kategoriFilter = document.getElementById('kategoriFilter');
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('filterForm');

            // Auto submit saat kategori berubah
            kategoriFilter.addEventListener('change', function() {
                form.submit();
            });

            // Auto submit saat mengetik (dengan delay supaya tidak spam)
            let typingTimer;
            searchInput.addEventListener('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    form.submit();
                }, 500); // delay 500ms
            });
        </script>
    </div>

    <div class="grid-karya">

        @foreach($contents as $content)

        @php
        $extension = strtolower(pathinfo($content->file_path, PATHINFO_EXTENSION));
        $fileUrl = asset('storage/'.$content->file_path);
        @endphp

        <div class="card-karya">
            
            <div class="flex justify-between items-center">
                <div>
                    <strong>{{ $content->category->name ?? '-' }}</strong> - {{ $content->title }}
                </div>

                {{-- Tombol Hapus --}}
                <form action="{{ route('vj.contents.destroy',$content->content_id) }}" method="POST"
                      onsubmit="return confirm('Yakin hapus karya?')">
                    @csrf
                    @method('DELETE')

                    <button class="px-3 py-1 bg-red-500 text-white rounded-md text-sm">
                        Hapus
                    </button>
                </form>
            </div>
            
            <div class="mb-4">
                @if($content->status == 'pending')
                    <span style="color:yellow">Pending</span>
                @elseif($content->status == 'approved')
                    <span style="color:cyan">Approved</span>
                @else
                    <span style="color:red">Rejected</span>
                @endif
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