@extends('layouts.app')

@section('content')
<div class="p-8 bg-slate-900 min-h-screen text-white">

    <h1 class="text-2xl font-bold mb-6">Tambah Template Panggung</h1>

    <form action="{{ route('admin.stage_templates.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-slate-800 p-6 rounded-xl space-y-4">

        @csrf

        @include('admin.stage_templates.partials.form')

        <button class="bg-blue-600 px-5 py-2 rounded-lg hover:bg-blue-700">
            Simpan
        </button>

    </form>

</div>
@endsection