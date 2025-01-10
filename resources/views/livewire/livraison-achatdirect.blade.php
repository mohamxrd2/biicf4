<div class="max-w-5xl mx-auto">
    <x-negociation-card name="NÉGOCIATION POUR LA LIVRAISON" :lastActivity="$lastActivity" :nombreParticipants="$nombreParticipants" :isNegociationActive="$isNegociationActive"
        :commentCount="$commentCount" />


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
                            <img src="{{ asset('post/all/' . $notification->data['photoProd']) }}" alt="Produit"
                                class="w-full h-80 object-cover rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105">
                        </div>

                        {{-- Section Détails --}}
                        <div class="space-y-6">

                            {{-- En-tête du produit --}}
                            <div class="border-b border-gray-200">
                                <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $produit->name }}</h1>
                                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                    <span>Voir le produit</span>
                                    <svg class="w-5 h-5 ml-2 transform transition-transform group-hover:translate-x-1"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                            <div class="relative">
                                <h1 class="text-2xl font-bold text-black cursor-pointer hover:underline"
                                    onclick="toggleModal()">
                                    Nombre de lieu de récupération ({{ count($usersLocations) }})
                                </h1>

                                <!-- Modal -->
                                <div id="modal"
                                    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white w-3/4 max-w-lg p-6 rounded-lg shadow-lg relative">
                                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tous les lieux de récupération
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


                            <div class=" bg-white">
                                <!-- Lieu de livraison -->
                                <div class="bg-white p-6 rounded-lg mt-6">

                                    <h2 class="text-xl font-bold text-gray-900 mb-4">Autres Spécifications</h2>
                                    <div class="grid gap-y-6">
                                        <!-- Quantité -->
                                        <div
                                            class="flex flex-col space-x-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">

                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Quantité</h3>
                                                <p class="text-gray-600">
                                                    {{ $achatdirect->quantité . ' (' . $produit->condProd . ')' }}
                                                </p>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Date de récupération
                                                </h3>
                                                <p class="text-gray-600">Non spécifiée</p>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Conditionnement</h3>
                                                <p class="text-gray-600">
                                                    {{ $notification->data['textareaContent'] }}</p>
                                            </div>
                                            <div>
                                                <h1 class="text-lg font-semibold text-gray-900">Lieu de livraison:
                                                    {{ $achatdirect->localite }}</h1>

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
