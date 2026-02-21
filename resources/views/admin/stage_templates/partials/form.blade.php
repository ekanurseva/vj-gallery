<div>
    <label>Nama Template</label>
    <input type="text"
           name="name"
           value="{{ old('name', $stage_template->name ?? '') }}"
           class="w-full mt-1 p-2 rounded bg-slate-700 border border-slate-600">
</div>

<div>
    <label>Deskripsi</label>
    <textarea name="description"
              class="w-full mt-1 p-2 rounded bg-slate-700 border border-slate-600">
        {{ old('description', $stage_template->description ?? '') }}
    </textarea>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label>Canvas Width</label>
        <input type="number"
               name="canvas_width"
               value="{{ old('canvas_width',$stage_template->canvas_width ?? '') }}"
               class="w-full mt-1 p-2 rounded bg-slate-700">
    </div>

    <div>
        <label>Canvas Height</label>
        <input type="number"
               name="canvas_height"
               value="{{ old('canvas_height',$stage_template->canvas_height ?? '') }}"
               class="w-full mt-1 p-2 rounded bg-slate-700">
    </div>
</div>

<div>
    <label>Background Type</label>
    <select name="background_type"
            class="w-full mt-1 p-2 rounded bg-slate-700">
        <option value="color">Color</option>
        <option value="video">Video</option>
        <option value="image">Image</option>
    </select>
</div>

<div>
    <label>Background File</label>
    <input type="file" name="background_file"
           class="mt-1 block w-full">
</div>

<div>
    <label>Audio File</label>
    <input type="file" name="audio_file"
           class="mt-1 block w-full">
</div>