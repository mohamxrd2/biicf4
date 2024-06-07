<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Se connecter</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body  style="background-image: url({{ asset('img/bg.jpg') }}); background-position: center; background-size: cover;">

    <div class="w-full h-[100vh] flex justify-center items-center p-5">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="w-[430px] bg-white border border-gray-200 rounded-xl shadow-sm" style="z-index: 1;">
            <div class="p-4 sm:p-7">
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
                <div class="text-center">
                    <h1 class="block text-2xl font-bold text-gray-800">Se connecter</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Vous n'avez pas de compte?
                        <a class="text-blue-600 decoration-2  font-medium" href="{{ route('biicf.signup') }}">
                            Créer un compte
                        </a>
                    </p>
                </div>
                <div class="mt-5">
                    <!-- Form -->
                    <form action="{{ route('biicf.login') }}" method="POST">
                        @csrf
                        <div class="grid gap-y-4">
                            <div class="w-full space-y-4">
                                <div class="relative">
                                    <input type="text" id="login" name="login"
                                        class="peer py-3 px-4 ps-11 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Username ou email">
                                    <div
                                        class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                                        <svg class="flex-shrink-0 size-4 text-gray-500 dark:text-neutral-500"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <input type="password" id="password" name="password"
                                        class="peer py-3 px-4 ps-11 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Mot de passe">
                                    <div
                                        class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                                        <svg class="flex-shrink-0 size-4 text-gray-500 dark:text-neutral-500"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z">
                                            </path>
                                            <circle cx="16.5" cy="7.5" r=".5"></circle>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                           

                            <!-- Checkbox -->
                            <div class="flex justify-between">
                                <div class="flex items-center">

                                    <div class="flex">
                                        <input id="remember_me" name="remember_me" type="checkbox"
                                            class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500"
                                            {{ old('remember_me') ? 'checked' : '' }}>
                                    </div>
                                    <div class="ms-3">
                                        <label for="remember_me" class="text-sm text-gray-600">Resté connecté</label>
                                    </div>
                                </div>

                                <a class="text-blue-600 text-sm decoration-2  font-medium" href="#">
                                    Mot de passe oublié
                                </a>

                            </div>

                            <!-- End Checkbox -->

                            <button type="submit"
                                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">Se
                                connecter</button>
                        </div>
                    </form>
                    <!-- End Form -->
                </div>
            </div>
        </div>


    </div>

</body>

</html>
