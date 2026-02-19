@extends('layouts.app')

@section('content')

<div class="py-16 px-20">

    <h2 class="text-3xl font-bold mb-6 text-center">
        Tambah Pengguna
    </h2>
  
    <div class="bg-white/5 border border-white/10 rounded-xl px-20 py-12 max-w-5xl mx-auto">

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <input type="text" name="name" placeholder="Nama"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">

            <input type="email" name="email" placeholder="Email"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">

            <input type="password" name="password" placeholder="Password"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">

            <select name="role"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
                <option value="admin">Admin</option>
                <option value="vj">VJ</option>
            </select>

            <button class="w-full bg-cyan-400 text-[#0A192F] py-2 rounded-lg font-semibold hover:bg-cyan-300">
                Simpan
            </button>

        </form>

    </div>

</div>

@endsection
