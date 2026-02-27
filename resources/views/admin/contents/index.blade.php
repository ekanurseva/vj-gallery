@extends('layouts.app')

@section('content')

<div style="padding:40px;">

    {{-- Judul --}}
    <h1 class="text-3xl font-bold mb-6 text-center">
        Manajemen Konten Multimedia
    </h1>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div style="background:#16a34a;color:white;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#dc2626;color:white;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Card Container --}}
    <div style="background:#0B1E3D;padding:30px;border-radius:14px;box-shadow:0 0 25px rgba(0,0,0,0.3);">

        {{-- TAB BUTTON --}}
        <div style="margin-bottom:25px; display:flex; gap:15px;">
            <button onclick="showTab('konten')" id="btnKonten" style="background:white;color:#0B1E3D;padding:8px 18px;border-radius:8px;border:none;cursor:pointer;">
                Perizinan Konten
            </button>

            <button onclick="showTab('kategori')" id="btnKategori" style="background:transparent;color:white;padding:8px 18px;border-radius:8px;border:1px solid white;cursor:pointer;">
                Kategori
        </button>
        </div>

        {{-- TAB KONTEN --}}
        <div id="tabKonten">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @forelse($contents as $content)

                @php
                    $extension = strtolower(pathinfo($content->file_path, PATHINFO_EXTENSION));
                    $fileUrl = asset('storage/'.$content->file_path);
                @endphp

                <div class="bg-slate-800 rounded-xl shadow-lg overflow-hidden text-white">

                    {{-- PREVIEW --}}
                    <div class="h-56 bg-slate-700 cursor-pointer"
                        onclick="openModal('{{ $fileUrl }}','{{ $extension }}')">

                        {{-- IMAGE --}}
                        @if(in_array($extension, ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ $fileUrl }}" 
                                class="w-full h-full object-cover">

                        {{-- VIDEO --}}
                        @elseif(in_array($extension, ['mp4','mov','webm']))
                            <video class="w-full h-full object-cover" muted>
                                <source src="{{ $fileUrl }}">
                            </video>

                        {{-- AUDIO --}}
                        @elseif(in_array($extension, ['mp3','wav','ogg']))
                            <div class="flex items-center justify-center h-full bg-slate-600">
                                <audio controls class="w-full px-4">
                                    <source src="{{ $fileUrl }}">
                                </audio>
                            </div>
                        @endif

                    </div>

                    {{-- INFO --}}
                    <div class="p-4 space-y-2">

                        <h2 class="font-semibold truncate">
                            {{ $content->title }}
                        </h2>

                        <p class="text-sm text-gray-400">
                            User: {{ $content->user->name }}
                        </p>

                        <p class="text-sm text-gray-400">
                            Kategori: {{ $content->category->name ?? '-' }}
                        </p>

                        <p class="text-sm text-gray-400">
                            Deskripsi: {{ $content->description ?? '-' }}
                        </p>

                        <div class="flex justify-between items-center text-sm">

                            <span class="px-2 py-1 text-slate-300">
                                {{ strtoupper($content->type) }}
                            </span>

                            <span class="
                                px-2 py-1
                                @if($content->status === 'approved') text-green-600
                                @elseif($content->status === 'pending') text-yellow-500
                                @else text-red-600
                                @endif
                            ">
                                {{ strtoupper($content->status) }}
                            </span>

                        </div>

                        {{-- ACTION --}}
                        <div class="pt-3 border-t border-slate-700 flex gap-2 flex-wrap">

                            @if($content->status == 'pending')

                                <form action="{{ route('admin.contents.approve',$content->content_id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm">
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('admin.contents.reject',$content->content_id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                                        Reject
                                    </button>
                                </form>

                            @else

                                <form action="{{ route('admin.contents.destroy',$content->content_id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus konten ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                                        Hapus
                                    </button>
                                </form>

                            @endif

                        </div>

                    </div>

                </div>

                @empty
                    <div class="col-span-full text-center text-gray-400 py-10">
                        Belum ada konten
                    </div>
                @endforelse

            </div>

        </div>

        <!-- MODAL -->
        <div id="previewModal"
            class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50">

            <div id="modalContent" class="max-w-[90%] max-h-[90%]"></div>

        </div>

        <script>
        function openModal(url, ext) {

            let modal = document.getElementById('previewModal');
            let content = document.getElementById('modalContent');

            if(['jpg','jpeg','png','gif','webp'].includes(ext)){
                content.innerHTML = `<img src="${url}" style="max-width:100%; max-height:90vh;">`;
            }

            if(['mp4','mov','webm'].includes(ext)){
                content.innerHTML = `
                    <video controls autoplay style="max-width:100%; max-height:90vh;">
                        <source src="${url}">
                    </video>`;
            }

            modal.classList.remove('hidden');
        }

        document.getElementById('previewModal').onclick = function(){
            this.classList.add('hidden');
        }
        </script>
        
        {{-- TAB KATEGORI --}}
        <div id="tabKategori" style="display:none;">

            {{-- Tombol Tambah --}}
            <div style="margin-bottom:20px;">
                <form action="{{ route('admin.categories.store') }}" method="POST" style="display:flex; gap:10px;">
                    @csrf
                    <input type="text" name="name" placeholder="Nama kategori"
                        class="flex-2 bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">

                    <input type="text" name="description" placeholder="Deskripsi kategori"
                        class="flex-2 bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">

                    <select name="target_type"
                         class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">
                        <option value="all">Semua Type</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="audio">Audio</option>
                    </select>

                    <button type="submit"
                        class="bg-cyan-400 text-[#0A192F] px-6 py-2 rounded-lg font-semibold hover:bg-cyan-300 transition">
                        + Tambah
                    </button>
                </form>
            </div>

            {{-- Tabel Kategori --}}
            <table style="width:100%;color:white;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.2);">
                        <th style="padding:10px;text-align:left;">Nama</th>
                        <th style="padding:10px;text-align:left;">Deskripsi</th>
                        <th style="padding:10px;text-align:left;">Target</th>
                        <th style="padding:10px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="border-b border-white/10 hover:bg-white/5">

                            <form action="{{ route('admin.categories.update',$category->category_id) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')

                                <td style="padding:10px;">
                                    <input type="text"
                                        name="name"
                                        value="{{ $category->name }}"
                                        class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">
                                </td>

                                <td style="padding:10px;">
                                    <input type="text"
                                        name="description"
                                        value="{{ $category->description }}"
                                        class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">
                                </td>

                                <td style="padding:10px;">
                                    <select name="target_type"
                                        class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">
                                        <option value="all" {{ $category->target_type=='all'?'selected':'' }}>ALL</option>
                                        <option value="image" {{ $category->target_type=='image'?'selected':'' }}>IMAGE</option>
                                        <option value="video" {{ $category->target_type=='video'?'selected':'' }}>VIDEO</option>
                                        <option value="audio" {{ $category->target_type=='audio'?'selected':'' }}>AUDIO</option>
                                    </select>
                                </td>

                                <td style="padding:10px;text-align:center;">

                                    <button type="submit"
                                        class="px-3 py-2 bg-yellow-400 text-black rounded-md text-sm">
                                        Edit
                                    </button>
                            </form>

                                    <form action="{{ route('admin.categories.destroy',$category->category_id) }}"
                                        method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Yakin hapus kategori?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 bg-red-500 text-white rounded-md text-sm">
                                            Hapus
                                        </button>
                                    </form>

                                </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding:20px;text-align:center;">
                                Belum ada kategori
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>


{{-- SCRIPT TAB --}}
<script>
function showTab(tab) {
    const konten = document.getElementById('tabKonten');
    const kategori = document.getElementById('tabKategori');
    const btnKonten = document.getElementById('btnKonten');
    const btnKategori = document.getElementById('btnKategori');

    if(tab === 'konten') {
        konten.style.display = 'block';
        kategori.style.display = 'none';

        btnKonten.style.background = 'white';
        btnKonten.style.color = '#0B1E3D';

        btnKategori.style.background = 'transparent';
        btnKategori.style.color = 'white';
    } else {
        konten.style.display = 'none';
        kategori.style.display = 'block';

        btnKategori.style.background = 'white';
        btnKategori.style.color = '#0B1E3D';

        btnKonten.style.background = 'transparent';
        btnKonten.style.color = 'white';
    }
}
</script>

@endsection
