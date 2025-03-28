<div>
    <!-- Conteneur principal -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg">
        <!-- Section de titre et compte à rebours -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Ajout de Quantités</h2>

            <!-- Compte à rebours -->
            <livewire:countdown :id="$id" />
        </div>

        <!-- Détails -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Nombre de participants</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroupcount }}</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Nombre total</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $sumquantite }}</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Mode de paiement choisi</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->payment }}</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Produit</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->productName }}</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Date début</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->dateTot }}</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Date fin</label>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $appelOffreGroup->dateTard }}</p>
            </div>
            <div class="bg-indigo-50 p-6 rounded-xl shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Prix unitaire Max trouvé</label>
                <p class="mt-2 text-lg font-semibold text-indigo-600">{{ $appelOffreGroup->lowestPricedProduct }}</p>
            </div>
        </div>

        <div x-data="{ isOpen: @entangle('isOpen') }" x-cloak>
            @if (!$appelOffreGroup->count)
                <!-- Button to open modal -->
                <button @click="isOpen = true"
                    class="block w-full max-w-xs mx-auto text-white bg-gradient-to-r  bg-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-6 py-3 text-center transition-all duration-300">
                    Ajouter votre quantité
                </button>
            @endif

            <!-- Modal -->
            <div x-show="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300">
                <div class="w-full max-w-lg bg-white rounded-xl shadow-lg transform transition-transform scale-95"
                    @click.away="isOpen = false" @keydown.escape.window="isOpen = false">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h3 class="text-lg font-bold text-gray-800">Ajouter votre quantité</h3>
                        <button @click="isOpen = false" class="text-gray-500 hover:text-gray-800 focus:outline-none">
                            ✖
                        </button>
                    </div>

                    <form wire:submit.prevent="storeOffre" class="p-6 space-y-6">
                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700">
                                Ajouter une quantité
                            </label>
                            <input type="number" wire:model.defer="quantite"
                                class="mt-1 py-3 px-4 w-full border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                placeholder="Ajouter une quantité..." required>
                        </div>
                        @if (!$existingQuantite)
                            <div>
                                <label for="localite" class="block text-sm font-medium text-gray-700">
                                    Entrez votre adresse
                                </label>
                                <input type="text" wire:model.defer="localite"
                                    class="mt-1 py-3 px-4 w-full border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    placeholder="Lieu de livraison" required>
                            </div>
                        @endif


                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 text-white bg-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Ajouter votre quantité</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Chargement...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Groupages existants -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6">Groupages existants</h3>
            <div class="space-y-4">
                @foreach ($groupages as $groupage)
                    <div
                        class="bg-gray-50 p-6 rounded-lg shadow-md flex items-center justify-between hover:bg-gray-100 transition-all duration-300 ease-in-out">
                        <span class="text-gray-700 font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12H9m4 8h-4m5-16H5m5 16h-1"></path>
                            </svg>
                            <span>{{ $groupage->user->name ?? 'Utilisateur inconnu' }} - {{ $groupage->quantite }}
                                (unités)
                                - {{ $groupage->quantite * $appelOffreGroup->lowestPricedProduct }} FCFA
                            </span>
                        </span>
                        <button type="button" class="text-red-500 hover:text-red-700 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/countdown.js') }}?v=1.0.0" defer></script>

</div>
