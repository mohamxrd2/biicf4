@extends('biicf.layout.navside')

@section('title', 'Page Expirée')

@section('content')
<div class="flex flex-col items-center justify-center h-screen bg-gray-100">
    <h1 class="text-6xl font-bold text-yellow-500">419</h1>
    <p class="text-xl mt-4 text-gray-700">Votre session a expiré. Veuillez actualiser la page et réessayer.</p>
    <a href="{{ url()->previous() }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">
        Recharger la page
    </a>
</div>
@endsection
