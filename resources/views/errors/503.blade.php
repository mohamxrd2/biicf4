@extends('biicf.layout.navside')

@section('title', 'Service Indisponible')

@section('content')
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center">
        <dotlottie-player src="https://lottie.host/bfc480ae-efd1-43cf-aafe-6bf429fe5fa7/7AQTwqOjio.lottie"
            background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
        {{-- <h1 class="text-6xl font-bold text-yellow-500">503</h1> --}}
        <p class="text-xl mt-4 text-gray-700">Le service est temporairement indisponible.</p>
        <a href="{{ url('/') }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">
            Retour à l'accueil
        </a>
    </div>
@endsection
