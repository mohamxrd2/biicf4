<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BICMF - Plateforme B2B Innovante</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .gradient-text {
            @apply bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600;
        }
        .nav-link {
            @apply text-gray-700 hover:text-purple-600 transition-colors duration-300;
        }
        .mobile-nav-link {
            @apply block px-3 py-2 rounded-lg text-gray-700 hover:text-purple-600 hover:bg-purple-50 transition-all duration-300;
        }
        .mobile-nav-link-special {
            @apply block px-3 py-2 rounded-lg text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all duration-300;
        }
        .feature-card {
            @apply bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300;
        }
        .gradient-border {
            position: relative;
            border-radius: 0.75rem;
        }
        .gradient-border::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #6366f1, #a855f7, #ec4899);
            border-radius: 0.875rem;
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        .gradient-border:hover::before {
            opacity: 1;
        }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ isOpen: false, scrolled: false }" 
      @scroll.window="scrolled = window.pageYOffset > 20">

    <!-- Navbar -->
    <nav class="fixed w-full transition-all duration-300 z-50"
         :class="{ 'bg-white/80 backdrop-blur-lg shadow-lg': scrolled || isOpen, 'bg-transparent': !scrolled && !isOpen }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3" data-aos="fade-right">
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg blur opacity-60 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative w-12 h-12 bg-black rounded-lg flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">B</span>
                        </div>
                    </div>
                    <span class="text-2xl font-bold gradient-text">BICMF</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8" data-aos="fade-left">
                    <a href="#features" class="nav-link">Fonctionnalités</a>
                    <a href="#about" class="nav-link">À propos</a>
                    <a href="#contact" class="nav-link">Contact</a>
   
                    <a href="{{ route('biicf.login') }}" 
                       class="relative inline-flex group">
                        <div class="absolute transitiona-all duration-1000 opacity-70 -inset-px bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 rounded-xl blur-lg group-hover:opacity-100 group-hover:-inset-1 group-hover:duration-200"></div>
                        <button class="relative inline-flex items-center justify-center px-6 py-2 text-lg font-medium text-white transition-all duration-200 bg-black rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                            Connexion
                        </button>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-600" @click="isOpen = !isOpen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': isOpen, 'inline-flex': !isOpen }" stroke-linecap="round" 
                              stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !isOpen, 'inline-flex': isOpen }" stroke-linecap="round" 
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden" x-show="isOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#features" class="mobile-nav-link">Fonctionnalités</a>
                    <a href="#about" class="mobile-nav-link">À propos</a>
                    <a href="#contact" class="mobile-nav-link">Contact</a>
                    <a href="{{ route('biicf.login') }}" class="mobile-nav-link">Connexion</a>
                    <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link-special">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-delay="200">
                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight">
                        <span class="gradient-text">Innovez</span> votre
                        <br>business avec BICMF
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 leading-relaxed">
                        Transformez votre entreprise avec notre plateforme B2B nouvelle génération. 
                        Connectez-vous à un écosystème d'opportunités infinies.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('biicf.login') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Démarrer maintenant
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="#" class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-purple-600 bg-white rounded-xl border-2 border-purple-600/10 hover:bg-purple-50 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            En savoir plus
                        </a>
                    </div>
                </div>
                <div class="relative" data-aos="fade-left" data-aos-delay="400">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-3xl blur-3xl transform rotate-6"></div>
                    <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2074&q=80" 
                         alt="Business Growth" 
                         class="relative rounded-3xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>
        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
                    <h2 class="text-4xl font-bold gradient-text">
                        Solutions innovantes pour votre entreprise
                    </h2>
                    <p class="mt-4 text-xl text-gray-600">
                        Découvrez nos outils puissants conçus pour accélérer votre croissance
                    </p>
                </div>
    
                <div class="mt-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature Cards -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Marketplace B2B</h3>
                        <p class="mt-4 text-gray-600 leading-relaxed">
                            Plateforme de mise en relation directe entre entreprises avec des outils de négociation avancés.
                        </p>
                    </div>
    
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Solutions Financières</h3>
                        <p class="mt-4 text-gray-600 leading-relaxed">
                            Accès à des solutions de financement adaptées et sécurisées pour votre développement.
                        </p>
                    </div>
    
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Sécurité Avancée</h3>
                        <p class="mt-4 text-gray-600 leading-relaxed">
                            Protection maximale de vos données et transactions avec les dernières technologies de cryptage.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    
        <!-- Stats Section -->
        <section class="py-20 bg-gradient-to-br from-purple-50 to-pink-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="feature-card text-center" data-aos="zoom-in" data-aos-delay="100">
                        <div class="text-4xl font-bold gradient-text">5000+</div>
                        <div class="mt-2 text-gray-600">Entreprises Actives</div>
                    </div>
                    <div class="feature-card text-center" data-aos="zoom-in" data-aos-delay="200">
                        <div class="text-4xl font-bold gradient-text">₣10M+</div>
                        <div class="mt-2 text-gray-600">Transactions Mensuelles</div>
                    </div>
                    <div class="feature-card text-center" data-aos="zoom-in" data-aos-delay="300">
                        <div class="text-4xl font-bold gradient-text">98%</div>
                        <div class="mt-2 text-gray-600">Taux de Satisfaction</div>
                    </div>
                    <div class="feature-card text-center" data-aos="zoom-in" data-aos-delay="400">
                        <div class="text-4xl font-bold gradient-text">24/7</div>
                        <div class="mt-2 text-gray-600">Support Client</div>
                    </div>
                </div>
            </div>
        </section>
    
        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div data-aos="fade-right">
                        <h2 class="text-4xl font-bold gradient-text">
                            Parlons de votre projet
                        </h2>
                        <p class="mt-4 text-xl text-gray-600">
                            Notre équipe d'experts est là pour vous accompagner dans votre transformation digitale
                        </p>
    
                        <div class="mt-12 space-y-8">
                            <div class="flex items-center space-x-6">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                                    <p class="text-gray-600">contact@bicmf.com</p>
                                </div>
                            </div>
    
                            <div class="flex items-center space-x-6">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Téléphone</h3>
                                    <p class="text-gray-600">+225 07 08 00 13 30</p>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <form class="feature-card" data-aos="fade-left">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                                <input type="text" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea rows="4" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-200"></textarea>
                            </div>
                            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium rounded-xl hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
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
                <!-- Logo et Description -->
                <div data-aos="fade-up">
                    <div class="flex items-center space-x-3">
                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg blur opacity-60 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative w-12 h-12 bg-black rounded-lg flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">B</span>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-white">BICMF</span>
                    </div>
                    <p class="mt-4 text-gray-400">
                        Votre partenaire de confiance pour la transformation digitale et la croissance B2B en Afrique.
                    </p>
                    <!-- Réseaux sociaux -->
                    <div class="mt-6 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Services -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-lg font-semibold text-white">Services</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Marketplace B2B</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Solutions Financières</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Consulting Digital</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Formation</a></li>
                    </ul>
                </div>

                <!-- Entreprise -->
                <div data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-lg font-semibold text-white">Entreprise</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Carrières</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Partenaires</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-lg font-semibold text-white">Support</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Centre d'aide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Statut API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-12 pt-8 border-t border-gray-800">
                <div class="text-center">
                    <p class="text-gray-400">© 2024 BICMF. Tous droits réservés.</p>
                    <div class="mt-4 flex justify-center space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Confidentialité</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Conditions d'utilisation</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Mentions légales</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Initialisation des animations AOS
        AOS.init({
            duration: 1000,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // Gestion du menu mobile
        const menuButton = document.getElementById('menuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        menuButton?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Fermeture du menu mobile lors du clic en dehors
        document.addEventListener('click', (e) => {
            if (!menuButton?.contains(e.target) && !mobileMenu?.contains(e.target)) {
                mobileMenu?.classList.add('hidden');
            }
        });

        // Défilement fluide pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    mobileMenu?.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>