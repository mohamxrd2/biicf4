<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="preload" href="{{ asset('build/assets/app-BqjtQzdG.css') }}" as="style">
    @livewireStyles
</head>

<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside class="hidden md:flex fixed top-0 left-0 h-full w-20 bg-white p-4 flex-col justify-between shadow-lg">
        <div>
            <!-- Logo -->
            <div class="mb-8">
                <div
                    class="bg-green-500 rounded-md p-2 w-12 h-12 mx-auto flex items-center justify-center font-bold text-white">
                    BF


                </div>
            </div>
            <!-- Icons -->
            <nav class="space-y-4">


                @include('admin.components.navfin', [
                    'routeSelf' => 'finance.acceuil',
                    'route' => route('finance.acceuil'),
                    'iconSvg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"
                                                            class="size-6">
                                                            <path
                                                                d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                                                            <path
                                                                d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                                                        </svg>',
                ])
                @include('admin.components.navfin', [
                    'routeSelf' => 'finance.search',
                    'route' => route('finance.search'),
                    'iconSvg' => '<svg class="w-6 h-6 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Z" />
                                            <path fill-rule="evenodd"
                                                d="M21.707 21.707a1 1 0 0 1-1.414 0l-3.5-3.5a1 1 0 0 1 1.414-1.414l3.5 3.5a1 1 0 0 1 0 1.414Z"
                                                clip-rule="evenodd" />
                                        </svg>',
                ])
                @include('admin.components.navfin', [
                    'routeSelf' => 'finance.notif',
                    'route' => route('finance.notif'),
                    'iconSvg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path fill-rule="evenodd"
                                                d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z"
                                                clip-rule="evenodd" />
                                        </svg>',
                ])
                @include('admin.components.navfin', [
                    'routeSelf' => 'finance.wallet',
                    'route' => route('finance.wallet'),
                    'iconSvg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path d="M12 7.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                                            <path fill-rule="evenodd"
                                                d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 14.625v-9.75ZM8.25 9.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM18.75 9a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V9.75a.75.75 0 0 0-.75-.75h-.008ZM4.5 9.75A.75.75 0 0 1 5.25 9h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V9.75Z"
                                                clip-rule="evenodd" />
                                            <path
                                                d="M2.25 18a.75.75 0 0 0 0 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 0 0-.75-.75H2.25Z" />
                                        </svg>',
                ])

                @include('admin.components.navfin', [
                    'routeSelf' => 'finance.projet',
                    'route' => route('finance.projet'),
                    'iconSvg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path
                                                d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
                                        </svg>',
                ])

            </nav>
        </div>
        <!-- User Profile -->
        <div class="border-t border-gray-300">

            @include('admin.components.navfin', [
                'routeSelf' => 'finance.profile',
                'route' => route('finance.profile'),
                'iconSvg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6">
                                    <path fill-rule="evenodd"
                                        d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                        clip-rule="evenodd" />
                                </svg>',
            ])

            <div class="text-center">
                <a href="#" class="text-gray-600 hover:bg-gray-100 p-2 rounded-md inline-block" data-hs-overlay="#hs-sign-out-alert">
                    <!-- User Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"
                        class="size-6">
                        <path fill-rule="evenodd"
                            d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>

                </a>
            </div>

            

        </div>

    </aside>

    <!-- Main Content -->
    <div class="md:ml-20 flex-1">
        <!-- Navbar -->
        <header class="hidden md:flex bg-white px-6 py-4 justify-end items-center shadow-sm">



            <div class="flex   space-x-4 items-center">
                <div class="hs-dropdown relative inline-flex mt-2">
                    <button id="hs-dropdown-with-icons" type="button"
                        class="hs-dropdown-toggle py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                        aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        
                      
                        Bourse du financement
               
                        <svg class="hs-dropdown-open:rotate-180 size-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
    
                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white shadow-md rounded-lg mt-2 divide-y divide-gray-200"
                        role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-with-icons">
                        <div class="p-1 space-y-0.5">
                            <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                href="{{ route('biicf.acceuil') }}">
                               <div class=" bg-green-500 w-5 h-5 text-center rounded-full text-white roun">C</div>
                                Bourse du commerce
                            </a>
                            
                        </div>
                    </div>
                </div>
                <a href="{{ route('finance.addproject') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md">
                    Ajouter un projet
                </a>
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                    <!-- Placeholder for profile image -->
                    <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                        src="{{ asset($user->photo) }}" alt="">
                </div>
            </div>
        </header>

        <div id="hs-sign-out-alert"
                class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                <div
                    class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                    <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-gray-800">
                        <div class="absolute top-2 end-2">
                            <button type="button"
                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-gray-700"
                                data-hs-overlay="#hs-sign-out-alert">
                                <span class="sr-only">Close</span>
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18" />
                                    <path d="m6 6 12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-4 sm:p-10 text-center overflow-y-auto">
                            <!-- Icon -->
                            <span
                                class="mb-4 inline-flex justify-center items-center size-[62px] rounded-full border-4 border-yellow-50 bg-yellow-100 text-yellow-500 dark:bg-yellow-700 dark:border-yellow-600 dark:text-yellow-100">
                                <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </svg>
                            </span>
                            <!-- End Icon -->

                            <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-gray-200">
                                Se deconnecter
                            </h3>
                            <p class="text-gray-500">
                                Voulez vous vraiment vous déconnecter?
                            </p>


                            <div class="mt-6 flex justify-center gap-x-4">
                                <form id="logout-form" action="{{ route('biicf.logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>

                                <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800"
                                    href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Déconnecter
                                </a>

                                <button type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                    data-hs-overlay="#hs-sign-out-alert">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <!-- Content Section -->
        <main class="py-8 px-10 bg-gray-50 h-full">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>


</html>
