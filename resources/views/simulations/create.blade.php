@extends('layouts.app')

@section('content')

<div class="min-h-screen p-8">

    <div class="max-w-3xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-8">

            <h1 class="text-3xl font-bold">
                Gunakan Template
            </h1>

            <p class="text-gray-400 mt-2">
                Buat simulasi baru berdasarkan template panggung yang dipilih.
            </p>

        </div>


        {{-- TEMPLATE INFO --}}
        <div class="bg-slate-800 border border-white/10 rounded-xl p-6 mb-6">

            <h2 class="text-xl font-semibold">
                {{ $template->name }}
            </h2>

            <p class="text-gray-400 mt-2">
                {{ $template->description ?? 'Tidak ada deskripsi template.' }}
            </p>


            {{-- CANVAS --}}
            <div class="mt-5 text-sm text-gray-400">

                Canvas:

                <span class="text-white font-medium">
                    {{ $template->canvas_width }}
                    ×
                    {{ $template->canvas_height }}
                </span>

            </div>


            {{-- TEMA --}}
            <div class="mt-5">

                <p class="text-sm text-gray-400 mb-2">
                    Tema Template
                </p>

                <div class="flex flex-wrap gap-2">

                    @forelse($template->themes as $theme)

                        <span
                            class="
                                px-3 py-1
                                rounded-full
                                text-sm
                                bg-cyan-400/10
                                text-cyan-300
                                border border-cyan-400/20
                            "
                        >
                            {{ $theme->name }}
                        </span>

                    @empty

                        <span class="text-sm text-gray-500">
                            Template belum memiliki tema.
                        </span>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- FORM SIMULATION --}}
        <div class="bg-slate-800 border border-white/10 rounded-xl p-6">

            <form
                method="POST"
                action="{{ route('simulations.store', $template->template_id) }}"
            >

                @csrf


                {{-- TITLE --}}
                <div class="mb-5">

                    <label
                        for="title"
                        class="block mb-2 text-sm text-gray-300"
                    >
                        Judul Simulation
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Contoh: Konser Malam Minggu"
                        required
                        class="
                            w-full
                            px-4 py-2.5
                            rounded-lg
                            bg-[#0A192F]
                            border border-white/10
                            text-white
                            placeholder-gray-500
                            focus:outline-none
                            focus:border-cyan-400
                        "
                    >

                    @error('title')

                        <p class="text-red-400 text-sm mt-2">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- DESCRIPTION --}}
                <div class="mb-6">

                    <label
                        for="description"
                        class="block mb-2 text-sm text-gray-300"
                    >
                        Deskripsi
                        <span class="text-gray-500">
                            (Opsional)
                        </span>
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Tambahkan deskripsi simulation..."
                        class="
                            w-full
                            px-4 py-2.5
                            rounded-lg
                            bg-[#0A192F]
                            border border-white/10
                            text-white
                            placeholder-gray-500
                            focus:outline-none
                            focus:border-cyan-400
                        "
                    >{{ old('description') }}</textarea>

                    @error('description')

                        <p class="text-red-400 text-sm mt-2">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ACTION --}}
                <div class="flex justify-between items-center">

                    <a
                        href="{{ auth()->user()->role === 'admin'
                            ? route('admin.stage_templates.index')
                            : route('vj.templates.index') }}"
                        class="
                            bg-slate-700
                            hover:bg-slate-600
                            px-5 py-2.5
                            rounded-lg
                            transition
                        "
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="
                            bg-cyan-400
                            hover:bg-cyan-300
                            text-[#0A192F]
                            font-semibold
                            px-5 py-2.5
                            rounded-lg
                            transition
                        "
                    >
                        Gunakan Template
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection