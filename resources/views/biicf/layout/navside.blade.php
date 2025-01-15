<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Vite Assets -->
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>

    @livewireStyles

</head>

<body class="bg-gray-100">

    <x-side-navigation />

    <x-bottom-navigation :unreadCount="$unreadCount" />

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
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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

    <!-- End Sidebar -->
    <div id="content-container" class="transition-all duration-300 w-full pt-10 px-2 md:px-8 lg:ps-72"
        style="margin-bottom: 6rem;">


        @yield('content')

    </div>
    <!-- End Content -->

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('formSubmitted', function(message) {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)"
                    }
                }).showToast();
            });
        });

        document.addEventListener('livewire:navigated', () => {
            HSOverlay.autoInit();
            HSDropdown.autoInit();

        });
    </script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('application-sidebar');
            const content = document.getElementById('content-container');
            const logo = document.getElementById('logo');
            const titles = document.querySelectorAll('.nav-title');
            const toggleButton = document.getElementById('toggleButton');
            const checkoutSection = document.getElementById('checkoutSection'); // Récupérer l'élément checkoutSection
            const hiddenSection = document.getElementById('hiddenSection'); // Récupérer l'élément hiddenSection

            // Toggle sidebar class between wide and narrow
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-28');

            // Adjust content padding-left based on sidebar width
            if (sidebar.classList.contains('w-28')) {
                content.classList.add('lg:ps-32');
                content.classList.remove('lg:ps-72');

                // Appliquer une marge à gauche à checkoutSection si l'élément existe
                if (checkoutSection) {
                    checkoutSection.style.marginLeft = '2rem';
                }

                // Appliquer une marge à gauche à hiddenSection si l'élément existe
                if (hiddenSection) {
                    hiddenSection.style.marginLeft = '2rem';
                }

                // Rotate the button
                toggleButton.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('lg:ps-72');
                content.classList.remove('lg:ps-32');

                // Remettre la marge à sa valeur initiale pour checkoutSection si l'élément existe
                if (checkoutSection) {
                    checkoutSection.style.marginLeft = '0';
                }

                // Remettre la marge à sa valeur initiale pour hiddenSection si l'élément existe
                if (hiddenSection) {
                    hiddenSection.style.marginLeft = '0';
                }

                // Reset button rotation
                toggleButton.style.transform = 'rotate(0deg)';
            }

            // Toggle hidden class for titles and center text if collapsed
            titles.forEach(title => {
                if (sidebar.classList.contains('w-28')) {
                    title.classList.add('hidden');
                    title.classList.add('text-center');
                } else {
                    title.classList.remove('hidden');
                    title.classList.remove('text-center');
                }
            });

            // Adjust logo font size based on sidebar width
            if (logo) {
                logo.style.fontSize = sidebar.classList.contains('w-28') ? '0.75rem' : '1.25rem';
            }
        }
    </script>

    <script script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</body>

</html>
