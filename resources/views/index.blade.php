<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BICMF - B2B Marketplace</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-gray-50 to-white">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-lg border-b border-gray-200/80 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-xl font-bold text-white">B</span>
                    </div>
                    <span
                        class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">BICMF</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-blue-600 transition-colors">Fonctionnalités</a>
                    <a href="#about" class="text-gray-600 hover:text-blue-600 transition-colors">À propos</a>
                    <a href="#contact" class="text-gray-600 hover:text-blue-600 transition-colors">Contact</a>
                    <a href="{{ route('biicf.login') }}"
                        class="text-gray-600 hover:text-blue-600 transition-colors">Login</a>
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-105 duration-200 shadow-md hover:shadow-lg">
                        Dashboard
                    </a>
                </div>

                <button class="md:hidden text-gray-600" id="menuButton">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden hidden" id="mobileMenu">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#features"
                        class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Fonctionnalités</a>
                    <a href="#about"
                        class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">À
                        propos</a>
                    <a href="#contact"
                        class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Contact</a>
                    <a href="{{ route('biicf.login') }}"
                        class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">Login</a>
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-3 py-2 rounded-md bg-gradient-to-r from-blue-600 to-indigo-600 text-white">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-20 lg:pt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative z-10 lg:flex lg:items-center lg:gap-12">
                <div class="text-center lg:text-left lg:w-1/2">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold">
                        <span
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Connecter</span>
                        les entreprises,
                        <br>
                        <span
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Favoriser</span>
                        la croissance
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 leading-relaxed">
                        BICMF est votre plateforme B2B de référence, connectant fournisseurs et acheteurs pour une
                        croissance durable des entreprises.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Commencer maintenant
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="#"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-blue-600 font-medium border-2 border-blue-600/10 hover:bg-blue-50 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            En savoir plus
                        </a>
                    </div>
                </div>
                <div class="hidden lg:block lg:w-1/2">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 rounded-3xl blur-3xl transform -rotate-6">
                        </div>
                        <div class="relative bg-white rounded-3xl shadow-xl p-8">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="https://www.finance-investissement.com/wp-content/uploads/sites/2/2020/07/fizkes_black-millennial-boss-leading-corporate-team-during-briefing-in-picture-id1139630453.jpg"
                                     alt="Business Growth"
                                     class="rounded-2xl object-cover shadow-lg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="p-6 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl text-white transform hover:scale-105 transition-all duration-200">
                    <div class="text-4xl font-bold">1000+</div>
                    <div class="mt-2 text-blue-100">Clients Actifs</div>
                </div>
                <div
                    class="p-6 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl text-white transform hover:scale-105 transition-all duration-200">
                    <div class="text-4xl font-bold">2000+</div>
                    <div class="mt-2 text-blue-100">Fournisseurs</div>
                </div>
                <div
                    class="p-6 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl text-white transform hover:scale-105 transition-all duration-200">
                    <div class="text-4xl font-bold">4.9</div>
                    <div class="mt-2 text-blue-100">Note Moyenne</div>
                </div>
                <div
                    class="p-6 bg-gradient-to-br from-pink-600 to-red-600 rounded-2xl text-white transform hover:scale-105 transition-all duration-200">
                    <div class="text-4xl font-bold">24/7</div>
                    <div class="mt-2 text-blue-100">Support Client</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h2
                    class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Pourquoi choisir BICMF ?
                </h2>
                <p class="mt-4 text-xl text-gray-600">
                    Des solutions innovantes pour une croissance durable
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Cards -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Rapidité d'exécution</h3>
                    <p class="mt-4 text-gray-600">Optimisez vos processus commerciaux avec notre plateforme rapide et
                        efficace.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Sécurité maximale</h3>
                    <p class="mt-4 text-gray-600">Protection avancée de vos données et transactions avec un cryptage de
                        bout en bout.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Réseau étendu</h3>
                    <p class="mt-4 text-gray-600">Accédez à un vaste réseau de partenaires commerciaux qualifiés.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2
                        class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        Contactez-nous
                    </h2>
                    <p class="mt-4 text-xl text-gray-600">
                        Notre équipe est là pour répondre à toutes vos questions
                    </p>

                    <div class="mt-8 space-y-6">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email</h3>
                                <p class="text-gray-600">infobiicf@gmail.com</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Téléphone</h3>
                                <p class="text-gray-600">+225 (07) 08 00 13 30</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Adresse</h3>
                                <p class="text-gray-600">Abidjan, Cocody Rivera Palmeraie, Rue Ixora</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form class="bg-white p-8 rounded-2xl shadow-xl">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                            <input type="text"
                                class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email"
                                class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea rows="4"
                                class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                            <span class="text-xl font-bold text-white">B</span>
                        </div>
                        <span class="text-xl font-bold text-white">BICMF</span>
                    </div>
                    <p class="mt-4 text-gray-400">
                        Votre partenaire de confiance pour la croissance B2B
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white">Services</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Marketplace
                                B2B</a></li>
                        <li><a href="#"
                                class="text-gray-400 hover:text-white transition-colors">Investissements</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Consulting</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white">Entreprise</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">À propos</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Carrières</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white">Légal</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#"
                                class="text-gray-400 hover:text-white transition-colors">Confidentialité</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Conditions</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-800">
                <p class="text-center text-gray-400">© 2024 BICMF. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const menuButton = document.getElementById('menuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu after clicking
                    mobileMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>
