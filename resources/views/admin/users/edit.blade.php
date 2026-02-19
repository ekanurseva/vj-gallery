@extends('layouts.app')

@section('content')

<div class="px-20 py-16">
    
    <h2 class="text-2xl font-bold mb-6 text-center">
        Edit Pengguna
    </h2>

    <div class="bg-white/5 border border-white/10 rounded-xl px-20 py-12 max-w-5xl mx-auto">

        <form action="{{ route('admin.users.update', $user->user_id) }}"
              method="POST"
              class="space-y-5">
            @csrf
            @method('PUT')

            <input type="text" name="name"
                value="{{ $user->name }}"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">

            <input type="email" name="email"
                value="{{ $user->email }}"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">

            <select name="role"
                class="w-full bg-[#0A192F] border border-white/20 rounded-lg px-4 mb-4 py-2 text-white">
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="vj" {{ $user->role == 'vj' ? 'selected' : '' }}>VJ</option>
            </select>

            <button class="w-full bg-cyan-400 text-[#0A192F] py-2 rounded-lg font-semibold hover:bg-cyan-300">
                Update
            </button>

        </form>

    </div>

</div>

@endsection
