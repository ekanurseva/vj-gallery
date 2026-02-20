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

            <table style="width:100%;color:white;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.2);">
                        <th style="padding:10px;text-align:left;">Judul</th>
                        <th style="padding:10px;text-align:left;">User</th>
                        <th style="padding:10px;text-align:left;">Kategori</th>
                        <th style="padding:10px;text-align:left;">Tipe</th>
                        <th style="padding:10px;text-align:left;">Status</th>
                        <th style="padding:10px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $content)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                            <td style="padding:10px;">{{ $content->title }}</td>
                            <td style="padding:10px;">{{ $content->user->name }}</td>
                            <td style="padding:10px;">{{ $content->category->name }}</td>
                            <td style="padding:10px;">{{ strtoupper($content->type) }}</td>
                            <td style="padding:10px;">
                                {{ strtoupper($content->status) }}
                            </td>
                            <td style="padding:10px;text-align:center;">

                                @if($content->status == 'pending')
                                    <form action="{{ route('admin.contents.approve',$content->content_id) }}"
                                          method="POST"
                                          style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button style="background:#16a34a;color:white;border:none;padding:5px 10px;border-radius:5px;">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.contents.reject',$content->content_id) }}"
                                          method="POST"
                                          style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button style="background:#dc2626;color:white;border:none;padding:5px 10px;border-radius:5px;">
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.contents.destroy',$content->content_id) }}"
                                          method="POST"
                                          style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus konten ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button style="background:#ef4444;color:white;border:none;padding:5px 10px;border-radius:5px;">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:20px;text-align:center;">
                                Belum ada konten
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
        
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
