@extends('biicf.layout.navside')

@section('title', 'Details notification')



@section('content')


    <div class=" mx-auto">

        @if (session('success'))
            <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('error') }}
            </div>
        @endif



        @livewire('notification-show', ['id' => $id])


    </div>




@endsection
