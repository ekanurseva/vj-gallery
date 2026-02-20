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

            <input type="file" name="file" style="width:100%;margin-bottom:15px;">

            <div class="text-right">
                <button type="submit" class="bg-cyan-400 text-[#0A192F] py-2 px-5 rounded-lg font-semibold hover:bg-cyan-300">
                    Upload
                </button>                
            </div>
        </form>
    </div>
</div>

@endsection