@extends('admin.layout.navside')

@section('title', 'affichage de consommations')

@section('content')
    @if (session('success'))
        <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-green-200 text-red-800 px-4 py-2 rounded-md mb-4">
            {{ session('error') }}
        </div>
    @endif
    <div class="mb-3">
        <h1 class=" text-center font-bold text-2xl">DETAILS DE LA CONSOMMATION</h1>
    </div>


    @livewire('consommation-show', ['id' => $id])



@endsection
