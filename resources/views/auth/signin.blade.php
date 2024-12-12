<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600
 flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-gray-800">Bienvenue</h1>
            <p class="text-sm text-gray-600">Connectez-vous à votre compte</p>
        </div>

        <!-- Notifications -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <span>{{ $errors->first('username') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
            @csrf
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                <div class="mt-1">
                    <input type="text" name="username" id="username" autocomplete="username" required
                        class="w-full px-4 py-2 border rounded-md text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <div class="mt-1">
                    <input type="password" name="password" id="password" autocomplete="current-password" required
                        class="w-full px-4 py-2 border rounded-md text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-start">
                <div class="flex items-center">
                    <input type="checkbox" name="remember_me" id="remember_me"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        {{ old('remember_me') ? 'checked' : '' }}>
                    <label for="remember_me" class="ml-2 block text-sm text-gray-800">Se souvenir de moi</label>
                </div>
                
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Se connecter
                </button>
            </div>
        </form>

        
    </div>
</body>

</html>
