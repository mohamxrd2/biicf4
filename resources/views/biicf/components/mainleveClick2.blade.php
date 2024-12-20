<!-- Modal content -->
<div class="relative  rounded-xl shadow-2xl dark:bg-gray-800 transform transition-all">
    <!-- Modal header -->
    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-xl">
        <h3 class="text-2xl font-bold text-white">Main Levée</h3>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 text-sm bg-blue-500 text-white rounded-full shadow-lg">
                Livraison #{{ $codeVerification ?? 'N/A' }}
            </span>
        </div>
    </div>

    <!-- Modal body -->
    <div class="p-8">
        <div class="bg-gray-50 rounded-xl shadow-lg dark:bg-gray-700 overflow-hidden">
            <!-- Code de vérification -->
            <div
                class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 dark:from-blue-900 dark:to-blue-800 dark:border-blue-700">
                <div class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300">Code de
                        vérification</h3>
                </div>
                <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-white tracking-wider">
                    {{ $codeVerification ?? 'N/A' }}
                </p>
            </div>

            <div class="p-6 space-y-8">
                <!-- Avis de conformité -->
                <div class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                        <svg class="h-7 w-7 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Avis de conformité
                    </h2>

                    <div class="grid gap-6">
                        <!-- Radio button groups with improved styling -->
                        <div
                            class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm dark:bg-gray-750">
                            <label class="text-gray-700 font-medium dark:text-gray-300">Quantité</label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="quantite" value="oui" wire:model="quantite"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="ml-2">OUI</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="quantite" value="non" wire:model="quantite"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="ml-2">NON</span>
                                </label>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm dark:bg-gray-750">
                            <label class="text-gray-700 font-medium dark:text-gray-300">Qualité
                                apparente</label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="qualite" value="oui" wire:model="qualite"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="ml-2">OUI</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="qualite" value="non" wire:model="qualite"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="ml-2">NON</span>
                                </label>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm dark:bg-gray-750">
                            <label class="text-gray-700 font-medium dark:text-gray-300">Diversité</label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="diversite" value="oui" wire:model="diversite"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="ml-2">OUI</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="diversite" value="non" wire:model="diversite"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="ml-2">NON</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>



                @if (session()->has('error'))
                    <div class="p-4 mt-4 text-red-700 bg-red-100 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                <!-- Modal footer -->
                <div class="flex justify-end px-8 py-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" wire:click="mainleve" wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all duration-200">
                        <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span wire:loading.remove>J'accepte</span>
                        <span wire:loading>Traitement...</span>
                    </button>
                    <button type="button" wire:click='refuseVerif'
                        class="ml-4 px-6 py-2.5 bg-gray-200 text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-400 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                        Je refuse
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
