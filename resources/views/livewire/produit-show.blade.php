<div class="min-h-screen bg-gray-50 py-8">
    {{-- Notifications --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 mb-6">
            <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 mb-6">
            <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-xl">
                <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4">
        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-center text-gray-900">Détails de la publication</h1>
            <div class="mt-2 flex justify-center">
                <span class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-full text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Publié {{ \Carbon\Carbon::parse($produits->created_at)->diffForHumans() }}
                </span>
            </div>
        </div>

        {{-- Contenu principal --}}
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Section gauche - Galerie d'images --}}
            <div class="lg:w-2/3">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <div class="hs-carousel relative overflow-hidden aspect-video bg-gray-100 rounded-t-2xl">
                            <div class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                                @php
                                    $photos = array_filter([$produits->photoProd1, $produits->photoProd2, $produits->photoProd3, $produits->photoProd4]);
                                @endphp

                                @if(count($photos) > 0)
                                    @foreach($photos as $photo)
                                        <div class="hs-carousel-slide w-full flex-shrink-0">
                                            <img src="{{ asset($photo) }}" class="w-full h-full object-cover" alt="Image produit">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <img src="{{ asset('img/noimg.jpeg') }}" class="w-full h-full object-cover" alt="Image par défaut">
                                    </div>
                                @endif
                            </div>

                            @if(count($photos) > 1)
                                {{-- Navigation --}}
                                <button type="button" class="hs-carousel-prev absolute inset-y-0 left-0 flex items-center justify-center w-12 h-full hover:bg-black/20 transition-colors">
                                    <span class="sr-only">Précédent</span>
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                <button type="button" class="hs-carousel-next absolute inset-y-0 right-0 flex items-center justify-center w-12 h-full hover:bg-black/20 transition-colors">
                                    <span class="sr-only">Suivant</span>
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                {{-- Pagination --}}
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                                    @foreach($photos as $index => $photo)
                                        <span class="w-2.5 h-2.5 rounded-full bg-white/50 hs-carousel-active:bg-white transition-colors cursor-pointer"></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                
            </div>
                        {{-- Section droite - Informations et actions --}}
                        <div class="lg:w-1/3 space-y-6">
                            {{-- Actions --}}
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                                <div class="p-6 space-y-4">
                                    @if ($produits->statuts === 'Accepté')
                                        <div class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-xl">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Publication acceptée
                                        </div>
                                    @else
                                        <button wire:click="accepter" 
                                                class="w-full px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl transition-colors flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Accepter la publication
                                        </button>
                                    @endif
            
                                    @if ($produits->statuts === 'Refusé')
                                        <div class="flex items-center justify-center px-4 py-3 bg-red-50 text-red-700 rounded-xl">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Publication refusée
                                        </div>
                                    @else
                                        <button wire:click="refuser" 
                                                class="w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl transition-colors flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Refuser la publication
                                        </button>
                                    @endif
                                </div>
                            </div>
            
                            {{-- Informations principales --}}
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations du produit</h2>
                                    <div class="space-y-4">
                                        {{-- Type et Prix --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-gray-50 p-4 rounded-xl">
                                                <h3 class="text-sm font-medium text-gray-600 mb-1">Type</h3>
                                                <p class="text-gray-900 font-medium">{{ $produits->type }}</p>
                                            </div>
                                            <div class="bg-gray-50 p-4 rounded-xl">
                                                <h3 class="text-sm font-medium text-gray-600 mb-1">Prix unitaire</h3>
                                                <p class="text-green-600 font-semibold">{{ number_format($produits->prix, 0, ',', ' ') }} FCFA</p>
                                            </div>
                                        </div>
            
                                        {{-- Conditionnement et Format --}}
                                        @if($produits->condProd || $produits->formatProd)
                                        <div class="grid grid-cols-2 gap-4">
                                            @if($produits->condProd)
                                            <div class="bg-gray-50 p-4 rounded-xl">
                                                <h3 class="text-sm font-medium text-gray-600 mb-1">Conditionnement</h3>
                                                <p class="text-gray-900">{{ $produits->condProd }}</p>
                                            </div>
                                            @endif
                                            @if($produits->formatProd)
                                            <div class="bg-gray-50 p-4 rounded-xl">
                                                <h3 class="text-sm font-medium text-gray-600 mb-1">Format</h3>
                                                <p class="text-gray-900">{{ $produits->formatProd }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
            
                                        {{-- Quantités --}}
                                        @if($produits->qteProd_min || $produits->qteProd_max)
                                        <div class="bg-gray-50 p-4 rounded-xl">
                                            <h3 class="text-sm font-medium text-gray-600 mb-1">Quantité traitée</h3>
                                            <p class="text-gray-900">[ {{ $produits->qteProd_min }} - {{ $produits->qteProd_max }} ]</p>
                                        </div>
                                        @endif
            
                                        {{-- Capacité de livraison --}}
                                        @if($produits->LivreCapProd)
                                        <div class="bg-gray-50 p-4 rounded-xl">
                                            <h3 class="text-sm font-medium text-gray-600 mb-1">Capacité de livraison</h3>
                                            <p class="text-gray-900">{{ $produits->LivreCapProd }}</p>
                                        </div>
                                        @endif
            
                                        {{-- Localisation --}}
                                        <div class="bg-gray-50 p-4 rounded-xl">
                                            <h3 class="text-sm font-medium text-gray-600 mb-2">Localisation</h3>
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="text-gray-900">{{ $produits->villeServ }}{{ $produits->comnServ ? ', '.$produits->comnServ : '' }}</span>
                                                </div>
                                                @if($produits->zonecoServ)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    <span class="text-gray-900">{{ $produits->zonecoServ }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>