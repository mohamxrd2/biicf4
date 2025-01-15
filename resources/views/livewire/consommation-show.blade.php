<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Section principale -->
        <div class="flex-grow lg:w-2/3">
            <!-- En-tête -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="relative overflow-hidden p-8 bg-gradient-to-r from-teal-50 to-blue-50">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-gradient-to-br from-teal-500/10 to-blue-500/10 blur-3xl"></div>
                    <div class="relative">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $consommations->name }}
                        </h1>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Créé {{ \Carbon\Carbon::parse($consommations->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Grille d'informations -->
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <!-- Type -->
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Type</h3>
                        <p class="text-gray-900">{{ $consommations->type }}</p>
                    </div>

                    @if($consommations->format)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Format</h3>
                        <p class="text-gray-900">{{ $consommations->format }}</p>
                    </div>
                    @endif

                    @if($consommations->conditionnement)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Conditionnement</h3>
                        <p class="text-gray-900">{{ $consommations->conditionnement }}</p>
                    </div>
                    @endif

                    @if($consommations->qte)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Quantité</h3>
                        <p class="text-gray-900">{{ $consommations->qte }}</p>
                    </div>
                    @endif

                    @if($consommations->prix)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Prix</h3>
                        <p class="text-gray-900">{{ number_format($consommations->prix, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endif

                    @if($consommations->qteProd_min)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Fréquence de consommation</h3>
                        <p class="text-gray-900">{{ $consommations->qteProd_min }}</p>
                    </div>
                    @endif

                    @if($consommations->jourAch_cons)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Jour d'achat</h3>
                        <p class="text-gray-900">{{ $consommations->jourAch_cons }}</p>
                    </div>
                    @endif

                    @if($consommations->qualif_serv)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Qualification</h3>
                        <p class="text-gray-900">{{ $consommations->qualif_serv }}</p>
                    </div>
                    @endif

                    @if($consommations->specialité)
                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Spécialité</h3>
                        <p class="text-gray-900">{{ $consommations->specialité }}</p>
                    </div>
                    @endif

                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Zone d'activité</h3>
                        <p class="text-gray-900">{{ $consommations->zonecoServ }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors duration-200">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Ville</h3>
                        <p class="text-gray-900">{{ $consommations->villeCons }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panneau latéral des actions -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Actions</h2>
                
                <div class="space-y-4">
                    @if($consommation->statuts == 'Accepté')
                        <div class="bg-green-50 text-green-700 px-4 py-3 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Demande acceptée
                        </div>
                    @else
                        <button wire:click="accepter" class="w-full bg-teal-50 hover:bg-teal-100 text-teal-700 px-4 py-3 rounded-xl transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Accepter la demande
                        </button>
                    @endif

                    @if($consommation->statuts == 'Refusé')
                        <div class="bg-red-50 text-red-700 px-4 py-3 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Demande refusée
                        </div>
                    @else
                        <button wire:click="refuser" class="w-full bg-red-50 hover:bg-red-100 text-red-700 px-4 py-3 rounded-xl transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Refuser la demande
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>