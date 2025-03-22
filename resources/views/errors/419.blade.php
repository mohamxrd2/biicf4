@extends('biicf.layout.navside')

@section('title', 'Session expirée')

@section('content')
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center">
        <dotlottie-player src="https://lottie.host/310fa29c-9bf3-4f45-9990-1a5a1c0f77b2/sAxqdIZO0y.lottie"
            background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
        {{-- <h1 class="text-6xl font-bold text-orange-500">419</h1> --}}
        <p class="text-xl mt-4 text-gray-700">Votre session a expiré. Veuillez vous reconnecter.</p>
        <a href="{{ url('/login') }}" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">
            Se reconnecter
        </a>
    </div>
@endsection
