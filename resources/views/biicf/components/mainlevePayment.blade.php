<div class="max-w-4xl mx-auto">
    <div class="relative rounded-2xl shadow-2xl dark:bg-gray-800 transform transition-all">
        <!-- En-tête du modal -->
        <div class="flex items-center justify-between p-7 bg-gradient-to-r from-blue-700 to-blue-800 rounded-t-2xl">
            <div class="flex items-center space-x-4">
                <h3 class="text-2xl font-bold text-white">Main Levée</h3>

            </div>
        </div>

        <!-- Corps du modal -->
        <div class="p-8 space-y-8">
            <!-- Code de vérification -->
            <!-- Prix à payer -->
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 dark:from-blue-900 dark:to-indigo-900">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-blue-800 dark:text-blue-300">Prix à payer</h3>
                </div>
                <p class="text-4xl font-bold text-blue-900 dark:text-white tracking-wider">
                    {{ number_format($gelement->amount ?? 0, 0, ',', ' ') }} FCFA
                </p>
            </div>

            <!-- Avis de conformité -->
            <div class="bg-white dark:bg-gray-750 rounded-xl p-6 shadow-lg space-y-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3 mb-6">
                    <svg class="h-7 w-7 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Avis de conformité
                </h2>

                <!-- Grille des options -->
                <div class="grid gap-4">
                    @foreach (['quantite' => 'Quantité', 'qualite' => 'Qualité apparente', 'diversite' => 'Diversité'] as $key => $label)
                        <div
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors duration-200 dark:bg-gray-700 dark:border-gray-600">
                            <label class="text-gray-700 font-medium dark:text-gray-300">{{ $label }}</label>
                            <div class="flex gap-6">
                                @foreach (['oui' => 'OUI', 'non' => 'NON'] as $value => $text)
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $key }}" value="{{ $value }}"
                                            wire:model="{{ $key }}"
                                            class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm font-medium">{{ $text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Messages d'erreur et de succès -->
            @if (session()->has('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                @if ($notification->reponse == 'confirmation')
                    <div class="px-6 py-3 bg-green-500 text-white rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Achat effectué avec succès
                    </div>
                @elseif ($notification->reponse == 'refuser')
                    <div class="px-6 py-3 bg-red-500 text-white rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Achat refusé
                    </div>
                @else
                    <div x-data="{ loading: false }">
                        {{-- Bouton Refuser --}}
                        <button wire:click.prevent='refuseColis' x-show="!loading" x-transition.opacity
                            @click="loading = true"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-md hover:bg-gray-200 transition-colors">
                            Je Refuse
                        </button>

                        {{-- Bouton Accepter --}}
                        <button wire:click='acceptColis' x-show="!loading" x-transition.opacity @click="loading = true"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                            Je Paie
                        </button>

                        {{-- Indicateur de chargement --}}
                        <div x-show="loading" x-transition.opacity
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg shadow-md">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Traitement en cours...
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
