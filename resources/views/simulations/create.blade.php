@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-900 text-white flex justify-center items-center">

    <div class="bg-gray-800 p-8 rounded shadow-lg w-full max-w-xl">

        <h1 class="text-2xl font-semibold mb-2">
            Gunakan Template
        </h1>

        <p class="text-gray-400 mb-6">
            Template: <span class="font-semibold text-white">
                {{ $template->name }}
            </span>
        </p>

        <form method="POST" action="{{ route('simulations.store',$template->template_id) }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 text-sm text-gray-300">
                    Judul Simulation
                </label>
                <input type="text" name="title" required
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 text-white">
            </div>

            <div class="mb-6">
                <label class="block mb-1 text-sm text-gray-300">
                    Deskripsi (Opsional)
                </label>
                <textarea name="description"
                    class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 text-white"></textarea>
            </div>

            <div class="flex justify-between">

                <a href="{{ route('admin.stage_templates.index') }}"
                   class="bg-gray-600 px-4 py-2 rounded">
                    Batal
                </a>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
                    Buat Simulation
                </button>

            </div>

        </form>

    </div>

</div>

@endsection