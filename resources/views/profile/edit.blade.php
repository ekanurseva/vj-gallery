@extends('layouts.app')

@section('content')
<div style="display:flex; justify-content:center; margin-top:40px;">
    
    <div style="width:500px; background:#0B1E3D; padding:30px; border-radius:12px; color:white;">
        
        <h2 style="margin-bottom:20px;">Edit Profil</h2>
        @if(session('success'))
            <div style="
                background:#16a34a;
                color:white;
                padding:10px;
                border-radius:8px;
                margin-bottom:15px;
                text-align:center;
            ">
                {{ session('success') }}
            </div>
        @endif


        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div style="margin-bottom:15px;">
                <label>Nama</label>
                <input type="text" name="name"
                       value="{{ old('name', auth()->user()->name) }}"
                       class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
            </div>

            <div style="margin-bottom:15px;">
                <label>Email</label>
                <input type="email" name="email"
                       value="{{ old('email', auth()->user()->email) }}"
                       class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
            </div>

            <button type="submit"
                style="background:white; color:#0B1E3D; padding:8px 15px; border:none; border-radius:6px;">
                Simpan Perubahan
            </button>

        </form>
    </div>
</div>
@endsection