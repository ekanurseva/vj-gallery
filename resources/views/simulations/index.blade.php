@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">

    @if(session('success'))
        <div style="background:#16a34a; color:white; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center;
        ">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#dc2626;color:white;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-3xl font-bold mb-8 text-center">Simulasi Saya</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($simulations as $simulation)
            <div class="bg-slate-800 shadow rounded-lg overflow-hidden">
                @php
                    $slotZero = $simulation->simulationContents
                        ->where('slot', 0)
                        ->first();
                @endphp

                <div class="flex justify-between p-4">   
                    <h2 class="font-semibold text-white-400">
                        {{ $simulation->title }}
                    </h2>             

                    <form method="POST"
                        action="{{ route('simulations.destroy',$simulation->simulation_id) }}"
                        onsubmit="return confirm('Hapus simulasi ini?')">
                        @csrf
                        @method('DELETE')

                        <button class="text-red-600 ml-auto text-sm">
                            Hapus
                        </button>
                    </form>

                </div>
                
                <a href="{{ route('simulations.builder', $simulation->simulation_id) }}" class="text-white-600 text-sm">
                    <div class="h-40 bg-gray-200 overflow-hidden flex items-center justify-center">
                        @if($slotZero && $slotZero->content)

                            @if($slotZero->content->type === 'image')
                                <img src="{{ asset('storage/'.$slotZero->content->file_path) }}"
                                    class="w-full h-full object-cover">
                            @elseif($slotZero->content->type === 'video')
                                <video class="w-full h-full object-cover"
                                    muted>
                                    <source src="{{ asset('storage/'.$slotZero->content->file_path) }}">
                                </video>
                            @else
                                <div class="flex items-center justify-center h-full text-gray-500">
                                    Audio Content
                                </div>
                            @endif

                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                Thumbnail
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <p class="text-sm text-gray-500">
                            {{ $simulation->created_at->format('d M Y') }}
                        </p>

                        <span>{{ $simulation->description }}</span>
                    </div>
                </a>

                @if(
                    auth()->user()->role === 'admin' &&
                    $simulation->user_id === auth()->id()
                )

                    @if(!$simulation->is_template)

                        <form
                            action="{{ route('admin.simulations.makeTemplate', $simulation->simulation_id) }}"
                            method="POST"
                            class="inline"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="bg-purple-600 hover:bg-purple-700 mx-4 my-4 px-3 py-1 rounded"
                                onclick="return confirm('Jadikan Simulation ini sebagai Template Simulation?')"
                            >
                                Jadikan Template
                            </button>
                        </form>

                    @else

                        <span class="bg-green-600 mx-4 my-4 px-3 py-1 rounded text-sm">
                            Template Simulation
                        </span>

                    @endif

                @endif

            </div>
        @endforeach
    </div>

</div>
@endsection