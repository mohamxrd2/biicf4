<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      body {
    background-image: url({{ asset('img/bg.jpg') }});
    background-size: cover;
    background-position: center;
    position: relative;
}

.body-overlay::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Couche noire semi-transparente */
    z-index: 0;
}

.blur-overlay {
    position: relative;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 1; /* Pour que le contenu soit au-dessus */
}
    </style>
</head>

<body class="body-overlay min-h-screen flex items-center justify-center">
    <div class="blur-overlay bg-white/80 shadow-2xl rounded-2xl overflow-hidden w-[90%] max-w-lg">
        <div class="px-8 py-6">

            @if (session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ $errors->first() }}</span>
            </div>
        @endif
            <!-- Logo -->
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-8 h-8 text-white" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 7.5A4.5 4.5 0 1112 3a4.5 4.5 0 014.5 4.5zm-5.79 8.29L4.22 12.9a1 1 0 00-1.21.19L1 15v2.5A2.5 2.5 0 003.5 20h7.75a3.5 3.5 0 01-.54-1H3.5a1.5 1.5 0 01-1.5-1.5V15l1.57-1.57 7.17 2.63a3.5 3.5 0 012.56-.83z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Bienvenue !</h1>
                <p class="mt-2 text-gray-600">Connectez-vous pour accéder à votre espace</p>
            </div>

            
            <!-- Form -->
            <form action="{{ route('biicf.login') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <!-- Username/Email -->
                <div class="relative">
                    <input type="text" id="login" name="login"
                        class="peer py-3 px-4 ps-11 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Username ou email">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                </div>
                <!-- Password -->
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="peer py-3 px-4 ps-11 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Mot de passe">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z" />
                            <circle cx="16.5" cy="7.5" r=".5" />
                        </svg>
                    </div>
                </div>
                <!-- Remember me -->
                <div class="flex justify-between items-center">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember_me" id="remember_me"
                            class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-blue-500">
                        <span class="ml-2">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="text-sm text-blue-600 hover:underline">Mot de passe oublié ?</a>
                </div>
                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 text-white font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-md hover:from-blue-700 hover:to-indigo-700 transition">
                    Se connecter
                </button>
            </form>
            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Pas encore inscrit ?
                    <a href="{{ route('biicf.signup') }}" class="text-blue-600 font-medium hover:underline">Créer un
                        compte</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
