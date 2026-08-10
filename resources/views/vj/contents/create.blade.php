@extends('layouts.app')

@section('content')

<div class="py-6 px-64">

    <h2 class="text-3xl font-bold mb-6 text-center">Upload Karya</h2>

    <div class="bg-white/5 border border-white/10 rounded-xl px-20 py-12 max-w-5xl mx-auto">
        <form action="{{ route('vj.contents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="title" placeholder="Judul karya" class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
            
            <input type="text" name="description" placeholder="Deskripsi karya" class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
            
            <select name="category_id" class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}">
                    {{ $cat->name }} - {{ $cat->target_type }}
                </option>
                @endforeach
            </select>

            <div class="mt-4 mb-4">

                <label class="block mb-2 font-semibold">
                    Tema Konten
                </label>

                <p class="text-sm text-gray-400 mb-3">
                    Pilih satu atau beberapa tema yang sesuai dengan karya ini.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">

                    @foreach($themes as $theme)

                        <label
                            class="flex items-center gap-3
                                bg-[#0A192F]
                                border border-white/20
                                rounded-lg
                                px-4 py-3
                                cursor-pointer
                                hover:border-cyan-400
                                transition">

                            <input
                                type="checkbox"
                                name="theme_ids[]"
                                value="{{ $theme->theme_id }}"
                                {{ in_array(
                                    $theme->theme_id,
                                    old('theme_ids', [])
                                ) ? 'checked' : '' }}
                                class="rounded
                                    border-gray-500
                                    text-cyan-400
                                    focus:ring-cyan-400">

                            <span class="text-white">
                                {{ $theme->name }}
                            </span>

                        </label>

                    @endforeach

                </div>

            </div>

            <div class="mt-6 mb-5">

                <label class="block mb-2 font-semibold">
                    File Karya
                </label>

                <label
                    for="file"
                    class="flex flex-col items-center justify-center
                        w-full h-32
                        border-2 border-dashed border-white/20
                        rounded-xl
                        bg-[#0A192F]
                        cursor-pointer
                        hover:border-cyan-400
                        hover:bg-cyan-400/5
                        transition duration-200">

                    {{-- ICON --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-8 h-8 mb-2 text-cyan-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 16V4m0 0L8 8m4-4 4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />

                    </svg>

                    <span
                        id="fileLabel"
                        class="text-sm text-gray-300">
                        Klik untuk memilih file
                    </span>

                    <span class="text-xs text-gray-500 mt-1">
                        Gambar, video, atau audio
                    </span>

                </label>

                <input
                    id="file"
                    type="file"
                    name="file"
                    class="hidden">

                @error('file')
                    <p class="text-red-400 text-sm mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="text-right">
                <button type="submit" class="bg-cyan-400 text-[#0A192F] py-2 px-5 rounded-lg font-semibold hover:bg-cyan-300">
                    Upload
                </button>                
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const fileInput = document.getElementById('file');
        const fileLabel = document.getElementById('fileLabel');

        if (!fileInput || !fileLabel) {
            return;
        }

        fileInput.addEventListener('change', function () {

            if (this.files && this.files.length > 0) {

                fileLabel.textContent = this.files[0].name;

                fileLabel.classList.remove('text-gray-300');
                fileLabel.classList.add('text-cyan-400');

            } else {

                fileLabel.textContent = 'Klik untuk memilih file';

                fileLabel.classList.remove('text-cyan-400');
                fileLabel.classList.add('text-gray-300');

            }

        });

    });
</script>

@endsection