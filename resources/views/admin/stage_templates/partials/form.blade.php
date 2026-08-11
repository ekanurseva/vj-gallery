{{-- Nama Template --}}
<div>
    <label class="block font-medium">
        Nama Template
    </label>

    <input
        type="text"
        name="name"
        value="{{ old('name', $stage_template->name ?? '') }}"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
        placeholder="Contoh: EDM Concert"
        required
    >
</div>


{{-- Deskripsi --}}
<div>
    <label class="block font-medium">
        Deskripsi
    </label>

    <textarea
        name="description"
        rows="3"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
        placeholder="Deskripsi template..."
    >{{ old('description', $stage_template->description ?? '') }}</textarea>
</div>


{{-- Canvas Width --}}
<div>
    <label class="block font-medium">
        Canvas Width
    </label>

    <input
        type="number"
        name="canvas_width"
        value="{{ old('canvas_width', $stage_template->canvas_width ?? '') }}"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
        min="100"
        required
    >
</div>


{{-- Canvas Height --}}
<div>
    <label class="block font-medium">
        Canvas Height
    </label>

    <input
        type="number"
        name="canvas_height"
        value="{{ old('canvas_height', $stage_template->canvas_height ?? '') }}"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
        min="100"
        required
    >
</div>


{{-- Background Type --}}
<div>
    <label class="block font-medium">
        Background Type
    </label>

    <select
        name="background_type"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
        required
    >
        <option value="">Pilih Background</option>

        <option
            value="color"
            {{ old('background_type', $stage_template->background_type ?? '') == 'color' ? 'selected' : '' }}
        >
            Color
        </option>

        <option
            value="image"
            {{ old('background_type', $stage_template->background_type ?? '') == 'image' ? 'selected' : '' }}
        >
            Image
        </option>

        <option
            value="video"
            {{ old('background_type', $stage_template->background_type ?? '') == 'video' ? 'selected' : '' }}
        >
            Video
        </option>
    </select>
</div>


{{-- Background File --}}
<div>
    <label class="block font-medium">
        Background
    </label>

    <input
        type="file"
        name="background_file"
        accept=".jpg,.jpeg,.png,.mp4"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
    >

    @if(isset($stage_template) && $stage_template->background_path)

        <p class="text-sm text-gray-400 mt-2">
            Background saat ini:
            {{ basename($stage_template->background_path) }}
        </p>

    @endif
</div>


{{-- Audio --}}
<div>
    <label class="block font-medium">
        Audio Template
    </label>

    <input
        type="file"
        name="audio_file"
        accept=".mp3,.wav"
        class="w-full mt-1 p-2 rounded bg-slate-700 border border-white/10"
    >

    @if(isset($stage_template) && $stage_template->audio_path)

        <p class="text-sm text-gray-400 mt-2">
            Audio saat ini:
            {{ basename($stage_template->audio_path) }}
        </p>

    @endif
</div>


{{-- Tema --}}
<div>
    <label class="block font-medium mb-2">
        Tema Template
    </label>

    <p class="text-sm text-gray-400 mb-3">
        Pilih satu atau beberapa tema yang sesuai dengan template ini.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">

        @foreach($themes as $theme)

            @php
                $selectedThemes = old(
                    'theme_ids',
                    isset($stage_template)
                        ? $stage_template->themes->pluck('theme_id')->toArray()
                        : []
                );
            @endphp

            <label
                class="
                    flex items-center gap-3
                    bg-slate-700
                    border border-white/10
                    rounded-lg
                    px-4 py-3
                    cursor-pointer
                    hover:border-cyan-400
                    transition
                "
            >

                <input
                    type="checkbox"
                    name="theme_ids[]"
                    value="{{ $theme->theme_id }}"
                    {{ in_array($theme->theme_id, $selectedThemes) ? 'checked' : '' }}
                    class="
                        rounded
                        border-gray-500
                        text-cyan-400
                        focus:ring-cyan-400
                    "
                >

                <span>
                    {{ $theme->name }}
                </span>

            </label>

        @endforeach

    </div>

    @error('theme_ids')
        <p class="text-red-400 text-sm mt-2">
            {{ $message }}
        </p>
    @enderror

    @error('theme_ids.*')
        <p class="text-red-400 text-sm mt-2">
            {{ $message }}
        </p>
    @enderror

</div>