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

<body class="antialiased h-full bg-gray-200">
    <div class="flex  min-h-full flex-col justify-center  py-12 sm:px-6 lg:px-8">


        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-10 shadow sm:rounded-lg sm:px-12">

                
                <!-- resources/views/auth/signin.blade.php -->

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">Oops!</strong>
                        <span class="block sm:inline">{{ $errors->first('username') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">Oops!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif



                <form class="space-y-6" action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Nom
                            d'utilisateur</label>
                        <div class="mt-2">
                            <input id="username" name="username" type="text" autocomplete="username" required
                                class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Mot de
                            passe</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required
                                class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox"
                                class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                {{ old('remember_me') ? 'checked' : '' }}>
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">Resté conncté</label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Se connecter
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>

</html>
