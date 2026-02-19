@extends('layouts.app')

@section('content')

<div class="px-20 py-12">

    @if(session('success'))
        <div style="
            background:#16a34a;
            color:white;
            padding:12px;
            border-radius:8px;
            margin-bottom:20px;
            text-align:center;
        ">
            {{ session('success') }}
        </div>
    @endif

    <!-- Judul -->
    <h1 class="text-3xl font-bold mb-6 text-center">
        Kelola Data Pengguna
    </h1>

    <!-- Tombol Tambah -->
    <div class="mb-6">
        <a href="{{ route('admin.users.create') }}"
           class="bg-cyan-400 text-[#0A192F] px-6 py-2 rounded-lg font-semibold hover:bg-cyan-300 transition">
            + Tambah Pengguna
        </a>
    </div>

    <!-- Kotak Tabel -->
    <div class="bg-white/5 border border-white/10 rounded-xl p-8 mx-auto">

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4 mb-6" id="filterForm">

            <!-- Search Nama -->
            <input type="text" name="search" id="searchInput" 
            value="{{ request('search') }}"
            placeholder="Cari nama pengguna..."
            class="flex-2 bg-[#0A192F] border border-white/20 rounded-lg px-4 py-2 text-white">
            
            <!-- Filter Role -->
            <select name="role" id="roleFilter"
                class="bg-[#0A192F] border border-white/20 rounded-lg ps-4 pe-8 py-2 text-white">
                <option value="all">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="vj" {{ request('role') == 'vj' ? 'selected' : '' }}>VJ</option>
            </select>
            
        </form>

        <script>
            const roleFilter = document.getElementById('roleFilter');
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('filterForm');

            // Auto submit saat role berubah
            roleFilter.addEventListener('change', function() {
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


        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="w-full text-left">

                <thead class="border-b border-white/20">
                    <tr>
                        <th class="py-3">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $index => $user)
                        <tr class="border-b border-white/10 hover:bg-white/5">
                            <td class="py-3">{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="capitalize">{{ $user->role }}</td>
                            <td class="text-center space-x-2">

                                <a href="{{ route('admin.users.edit', $user->user_id) }}"
                                   class="px-3 py-1 bg-yellow-400 text-black rounded-md text-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.users.destroy', $user->user_id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded-md text-sm">
                                        Hapus
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 opacity-70">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>

@endsection
