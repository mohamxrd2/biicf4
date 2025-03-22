@extends('biicf.layout.navside')

@section('title', 'Service Indisponible')

@section('content')
<div class="flex flex-col items-center justify-center h-screen bg-gray-100">
    <h1 class="text-6xl font-bold text-gray-500">503</h1>
    <p class="text-xl mt-4 text-gray-700">Le site est actuellement en maintenance. Revenez plus tard.</p>
    <a href="{{ url('/') }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">
        Retour à l'accueil
    </a>
</div>
@endsection
