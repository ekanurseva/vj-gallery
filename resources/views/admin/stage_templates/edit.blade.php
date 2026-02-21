@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="bg-red-600 p-3 rounded mb-4">
        {{ $errors->first() }}
    </div>
@endif

<div class="p-8 bg-slate-900 min-h-screen text-white">

    <h1 class="text-2xl font-bold mb-6">Edit Template</h1>

    <form action="{{ route('admin.stage_templates.update',$stage_template) }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-slate-800 p-6 rounded-xl space-y-4">

        @csrf
        @method('PUT')

        @include('admin.stage_templates.partials.form')

        <button class="bg-yellow-500 px-5 py-2 rounded-lg hover:bg-yellow-600">
            Update
        </button>

    </form>

</div>
@endsection