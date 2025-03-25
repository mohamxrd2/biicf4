@extends('biicf.layout.navside')

@section('title', 'Erreur Serveur')

@section('content')
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center">
        <dotlottie-player src="https://lottie.host/8845d38a-dd55-4f20-970f-a5a2402aed97/KbFkOU0CKU.lottie"
            background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
        {{-- <h1 class="text-6xl font-bold text-red-500">500</h1> --}}
        <p class="text-xl mt-4 text-gray-700">Oops ! Quelque chose s'est mal passé.</p>
        <a href= "{{ url('biicf/acceuil') }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">
            Retour à l'accueil
        </a>
    </div>
@endsection
