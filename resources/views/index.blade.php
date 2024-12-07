<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BICMF - B2B Marketplace</title>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900">

    <!-- Navbar -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-sm shadow z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-blue-600" width="512" height="512" viewBox="0 0 512 512" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect width="512" height="512" rx="256" fill="#2563EB" />
                    <rect x="156" y="100" width="200" height="312" rx="16" fill="white" />
                    <rect x="180" y="140" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="240" y="140" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="300" y="140" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="180" y="200" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="240" y="200" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="300" y="200" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="180" y="260" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="240" y="260" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="300" y="260" width="40" height="40" rx="8" fill="#2563EB" />
                    <rect x="220" y="340" width="72" height="52" rx="8" fill="#2563EB" />
                </svg>
                <span class="ml-2 text-xl font-bold text-gray-900">BICMF</span>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="#features" class="hover:text-blue-600">Fonctionnalités</a>
                <a href="#about" class="hover:text-blue-600">À propos</a>
                <a href="#contact" class="hover:text-blue-600">Contact</a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('biicf.login') }}" class="hover:text-blue-600">Login</a>
                <a href="{{ route('admin.dashboard') }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 text-white rounded hover:from-blue-600 hover:via-blue-700 hover:to-blue-800">
                    Dashboard
                </a>

            </div>
            <button class="md:hidden" id="hamburger-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
                </svg>
            </button>
        </div>

        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 hidden" id="mobile-menu">
            <a href="#features" class="block px-3 py-2 text-gray-700">Fonctionnalités</a>
            <a href="#about" class="block px-3 py-2 text-gray-700">À propos</a>
            <a href="#contact" class="block px-3 py-2 text-gray-700">Contact</a>

            <div class="mt-4 space-y-2">
                <a href="{{ route('biicf.login') }}" class="w-full px-4 py-2 text-blue-600">Login</a>
                <a href="{{ route('admin.dashboard') }}"
                    class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 text-white rounded-md hover:from-blue-600 hover:via-blue-700 hover:to-blue-800">
                    Dashboard
                </a>

            </div>
        </div>
    </nav>

    <script>
        const hamburgerButton = document.getElementById("hamburger-button");
        const mobileMenu = document.getElementById("mobile-menu");

        hamburgerButton.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
        });
    </script>



    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="text-center lg:text-left lg:flex lg:items-center lg:justify-between">
                <div class="lg:w-1/2">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                        Connecter les entreprises,
                        <span class="text-blue-600"> Favoriser la croissance</span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-600">
                        BICMF est votre plateforme de marché B2B de référence, connectant fournisseurs et acheteurs tout
                        en facilitant des investissements intelligents pour une croissance durable des entreprises.
                    </p>

                    <div class="mt-10 flex flex-col lg:flex-row justify-center lg:justify-start gap-4">
                        <button
                            class="px-8 py-4 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-600 hover:via-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 flex items-center justify-center">
                            Commencer →
                        </button>
                        <button
                            class="px-8 py-4 border-2 border-blue-600 text-blue-600 rounded-lg bg-gradient-to-r from-blue-100 via-blue-200 to-blue-300 hover:bg-gradient-to-r hover:from-blue-200 hover:via-blue-300 hover:to-blue-400 focus:ring-4 focus:ring-blue-300">
                            En savoir plus
                        </button>
                    </div>




                </div>
                <div class="hidden lg:block lg:w-1/2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-600 rounded-full opacity-10 blur-3xl"></div>
                        <svg class="-64 h-64 text-blue-600" width="512" height="512" viewBox="0 0 512 512"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="512" height="512" rx="256" fill="#2563EB" />
                            <rect x="156" y="100" width="200" height="312" rx="16" fill="white" />
                            <rect x="180" y="140" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="240" y="140" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="300" y="140" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="180" y="200" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="240" y="200" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="300" y="200" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="180" y="260" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="240" y="260" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="300" y="260" width="40" height="40" rx="8" fill="#2563EB" />
                            <rect x="220" y="340" width="72" height="52" rx="8" fill="#2563EB" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- stats Section -->
    <section class="bg-blue-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                <!-- Stat 1 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-white">1000+</div>
                    <div class="mt-2 text-blue-100">Clients</div>
                </div>
                <!-- Stat 2 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-white">2000+</div>
                    <div class="mt-2 text-blue-100">Fournisseurs</div>
                </div>
                <!-- Stat 3 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-white">4.9</div>
                    <div class="mt-2 text-blue-100">Rating</div>
                </div>
                <!-- Stat 4 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-white">24/7</div>
                    <div class="mt-2 text-blue-100">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section des fonctionnalités -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-extrabold text-gray-900">Pourquoi choisir BICMF ?</h2>
            <p class="mt-4 text-lg text-gray-600">Des solutions complètes pour une croissance durable de votre
                entreprise.</p>
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <!-- Carte Fonctionnalité 1 -->
                <div
                    class="p-8 bg-white shadow-lg rounded-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <div
                        class="w-16 h-16 bg-blue-600 text-white rounded-full mx-auto flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-800">Réseau de fournisseurs</h3>
                    <p class="mt-4 text-sm text-gray-600">Accédez à notre vaste réseau de fournisseurs vérifiés dans
                        divers secteurs.</p>
                </div>
                <!-- Carte Fonctionnalité 2 -->
                <div
                    class="p-8 bg-white shadow-lg rounded-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <div
                        class="w-16 h-16 bg-blue-600 text-white rounded-full mx-auto flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-800">Appariement intelligent</h3>
                    <p class="mt-4 text-sm text-gray-600">Un système d'appariement propulsé par IA pour vous connecter
                        avec les partenaires parfaits.</p>
                </div>
                <!-- Carte Fonctionnalité 3 -->
                <div
                    class="p-8 bg-white shadow-lg rounded-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <div
                        class="w-16 h-16 bg-blue-600 text-white rounded-full mx-auto flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-800">Transactions sécurisées</h3>
                    <p class="mt-4 text-sm text-gray-600">Plateforme cryptée de bout en bout pour des opérations
                        sécurisées.</p>
                </div>
                <!-- Carte Fonctionnalité 4 -->
                <div
                    class="p-8 bg-white shadow-lg rounded-lg transform hover:-translate-y-2 transition-transform duration-300">
                    <div
                        class="w-16 h-16 bg-blue-600 text-white rounded-full mx-auto flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-800">Opportunités d'investissement</h3>
                    <p class="mt-4 text-sm text-gray-600">Découvrez et participez à des opportunités d'investissement
                        sélectionnées.</p>
                </div>
            </div>
        </div>
    </section>



    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold">Contactez-nous</h2>
                <p class="mt-4 text-lg">Contactez-nous pour toute question ou demande d'assistance.</p>
            </div>
            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-8">
                <form class="bg-gray-50 p-8 shadow rounded-lg">
                    <div>
                        <label class="block font-semibold">Name</label>
                        <input type="text" class="w-full mt-2 p-3 border rounded-lg" />
                    </div>
                    <div class="mt-4">
                        <label class="block font-semibold">Email</label>
                        <input type="email" class="w-full mt-2 p-3 border rounded-lg" />
                    </div>
                    <div class="mt-4">
                        <label class="block font-semibold">Message</label>
                        <textarea rows="4" class="w-full mt-2 p-3 border rounded-lg"></textarea>
                    </div>
                    <button class="w-full mt-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Send
                        Message</button>
                </form>
                <div class="bg-gray-50 p-8 shadow-lg rounded-lg space-y-8">
                    <!-- Email Section -->
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-lg">Email</h3>
                            <p class="text-sm text-gray-600">infobiicf@gmail.com</p>
                        </div>
                    </div>

                    <!-- Phone Section -->
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-lg">Phone</h3>
                            <p class="text-sm text-gray-600">+225 (07) 08 00 13 30</p>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>

                        <div>
                            <h3 class="font-semibold text-lg">Address</h3>
                            <p class="text-sm text-gray-600">Abidjan, Cocody Rivera Palmeraie, Rue Ixora</p>
                        </div>
                    </div>

                    <!-- Social Media Section -->
                    <div class="flex items-center space-x-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>

                        <div>
                            <h3 class="font-semibold text-lg">Follow us</h3>
                            <div class="space-x-4">
                                <a href="https://www.facebook.com"
                                    class="text-blue-600 hover:text-blue-800">Facebook</a>
                                <a href="https://www.twitter.com"
                                    class="text-blue-600 hover:text-blue-800">Twitter</a>
                                <a href="https://www.linkedin.com"
                                    class="text-blue-600 hover:text-blue-800">LinkedIn</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">© 2024 BICMF. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>
