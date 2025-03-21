<div class="max-w-5xl mx-auto">
    <x-negociation-card name="NÉGOCIATION POUR LA LIVRAISON" mot='bas' :lastActivity="$lastActivity" :nombreParticipants="$nombreParticipants"
        :isNegociationActive="$isNegociationActive" :commentCount="$commentCount" />


    <x-information-nego :offreIniatiale="$offreIniatiale" :prixLePlusBas="$prixLePlusBas" :id="$id" />


    <div class="container mx-auto px-4" x-data="{ showDetails: true, showOffers: true }">
        <!-- Détails du produit -->
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            <div class="p-3">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Détails du produit</h2>
                    <button @click="showDetails = !showDetails"
                        class="text-blue-600 hover:text-blue-800 focus:outline-none">
                        <i class="fas" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>

                <div x-show="showDetails" x-collapse class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
                        {{-- Section Image --}}
                        <div class="relative group">
                            <div class="aspect-w-16 aspect-h-12 rounded-xl overflow-hidden bg-gray-100">
                                <img src="{{ asset('post/all/' . $notification->data['photoProd']) }}" alt="Produit"
                                    class="w-full h-full object-cover transition duration-300 ease-in-out transform group-hover:scale-110">
                                <div
                                    class="absolute inset-0  bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300">
                                </div>
                            </div>
                        </div>

                        {{-- Section Détails --}}
                        <div class="space-y-8">
                            {{-- En-tête du produit --}}
                            <div class="border-b border-gray-200 pb-4">
                                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $produit->name }}</h1>
                                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                    <span>Voir le produit</span>
                                    <svg class="w-5 h-5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                    </svg>
                                </a>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <button onclick="toggleModal()" class="w-full text-left">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-xl font-semibold text-gray-900">
                                            {{ $achatdirect->type_achat === 'OffreGrouper' ? 'Lieux de récupération' : 'Lieu de récupération' }}
                                            <span class="text-blue-600">({{ count($usersLocations) }})</span>
                                        </h2>
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </button>

                                <!-- Modal -->
                                <div id="modal"
                                    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white w-3/4 max-w-lg p-6 rounded-lg shadow-lg relative">
                                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                                            {{ $achatdirect->type_achat === 'OffreGrouper' ? 'Tous les lieux de récupération' : 'Lieu de récupération' }}
                                        </h2>
                                        <ul class="space-y-2">
                                            @foreach ($usersLocations as $location)
                                                <li
                                                    class="bg-gray-100 p-3 rounded-lg shadow-sm hover:bg-gray-200 transition">
                                                    {{ $location->localite }}
                                                </li>
                                            @endforeach
                                        </ul>
                                        <button onclick="toggleModal()"
                                            class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Spécifications --}}
                            <div class="lg:bg-white lg:rounded-xl lg:shadow-sm sm:bg-white sm:rounded-xl sm:shadow-sm">
                                <div class="lg:p-6 sm:p-6">
                                    <h2 class="text-xl font-bold text-gray-900 mb-6">Spécifications</h2>
                                    <div class="grid lg:grid-cols-1 col-span-1 gap-6">
                                        <div class="space-y-6">
                                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                                <div class="mr-4">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-500">Quantité</h3>
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        {{ $achatdirect->quantité . ' (' . $produit->condProd . ')' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                                <div class="mr-4">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-500">Date de récupération
                                                    </h3>
                                                    <p class="text-lg font-semibold text-gray-900">Non spécifiée</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                                <div class="mr-4">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-500">Conditionnement</h3>
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        {{ $notification->data['textareaContent'] }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                                <div class="mr-4">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-500">Lieu de livraison
                                                    </h3>
                                                    <p class="text-lg font-semibold text-gray-900">
                                                        {{ $achatdirect->localite }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-negociation :achatdirect="$achatdirect" :comments="$comments" />


    </div>

    <script>
        function toggleModal() {
            const modal = document.getElementById('modal');
            modal.classList.toggle('hidden');
        }
    </script>

</div>
