@extends('biicf.layout.navside')

@section('title', 'Accès refusé')

@section('content')
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center">
        <dotlottie-player src="https://lottie.host/5c8b63d6-79c4-42fb-aba1-bccededb9028/Kj8qDYcwyQ.lottie"
            background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
        {{-- <h1 class="text-6xl font-bold text-red-500">403</h1> --}}
        <p class="text-xl mt-4 text-gray-700">Vous n'avez pas la permission d'accéder à cette page.</p>
        <a href="{{ url('biicf/acceuil') }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">
            Retour à l'accueil
        </a>
    </div>
@endsection
