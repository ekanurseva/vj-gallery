@extends('layouts.app')

@section('content')
<div class="p-8 text-white bg-slate-900 min-h-screen">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Template Panggung</h1>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.stage_templates.create') }}"
            class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">
                + Tambah Template
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-600 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-slate-800 rounded-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-700">
                <tr>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Canvas</th>
                    <th class="p-3">Background</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr class="border-t border-slate-700">
                        <td class="p-3">{{ $template->name }}</td>
                        <td class="p-3">
                            {{ $template->canvas_width }} x {{ $template->canvas_height }}
                        </td>
                        <td class="p-3">{{ $template->background_type }}</td>
                        <td class="p-3 flex gap-2">
                            <a href="{{ route('admin.stage_templates.show',$template) }}"
                            class="bg-indigo-600 px-3 py-1 rounded">
                                View
                            </a>

                            {{-- Tombol khusus admin --}}
                            @if(auth()->user()->role === 'admin')

                                <a href="{{ route('admin.stage_templates.edit',$template) }}"
                                class="bg-yellow-500 px-3 py-1 rounded">
                                    Edit
                                </a>

                                <a href="{{ route('admin.stage_templates.builder',$template) }}"
                                class="bg-green-600 px-3 py-1 rounded">
                                    Builder
                                </a>

                                <form action="{{ route('admin.stage_templates.destroy',$template) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 px-3 py-1 rounded"
                                            onclick="return confirm('Yakin hapus template?')">
                                        Hapus
                                    </button>
                                </form>

                            @endif

                            {{-- Semua role bisa pakai template --}}
                            <a href="{{ route('simulations.create',$template) }}"
                            class="bg-blue-600 px-3 py-1 rounded">
                                Gunakan Template
                            </a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center">
                            Belum ada template
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection