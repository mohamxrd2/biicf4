<div class="min-h-screen p-8">
    @if ($tontineStart)
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Header avec un design plus moderne -->
                <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8">
                    <h2 class="text-4xl font-bold  text-center tracking-tight">Nouvelle Tontine</h2>
                    <p class=" mt-2 text-center text-lg">Créez votre épargne collaborative en quelques clics</p>
                </div>

                <form wire:submit.prevent="initiateTontine" class="p-8 space-y-8">
                    <!-- Montant avec design amélioré -->
                    <div class="space-y-2">
                        <label for="amount" class="text-base font-medium text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Montant de cotisation
                        </label>
                        <div class="relative mt-1">
                            <input type="number" id="amount" wire:model.defer="amount"
                                class="block w-full pl-12 pr-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                                placeholder="Montant en FCFA" required>

                        </div>
                        @error('amount')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fréquence avec badges -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-3 block">Fréquence de cotisation</label>
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Option Quotidienne -->
                            <label class="relative">
                                <input type="radio" name="frequency" wire:model.defer="frequency" value="quotidienne"
                                    class="peer sr-only">
                                <div
                                    class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                    Quotidienne
                                </div>
                            </label>

                            <!-- Option Hebdomadaire -->
                            <label class="relative">
                                <input type="radio" name="frequency" wire:model.defer="frequency" value="hebdomadaire"
                                    class="peer sr-only">
                                <div
                                    class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                    Hebdomadaire
                                </div>
                            </label>

                            <!-- Option Mensuelle -->
                            <label class="relative">
                                <input type="radio" name="frequency" wire:model.defer="frequency" value="mensuelle"
                                    class="peer sr-only">
                                <div
                                    class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                    Mensuelle
                                </div>
                            </label>
                        </div>

                        @error('frequency')
                            <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date de fin avec calendrier moderne -->
                    <div class="space-y-2">
                        <label for="end_date" class="text-base font-medium text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Date de fin
                        </label>
                        <input type="date" id="end_date" wire:model.defer="end_date"
                            class="block w-full px-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                            min="{{ date('Y-m-d') }}" required>
                        @error('end_date')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Section Gain Potentiel -->
                    <div class="text-left">
                        <p class="text-sm text-gray-500">Gain potentiel</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ number_format($this->potentialGain, 0, ',', ' ') }} FCFA
                        </p>
                    </div>



                    <!-- Information Box -->
                    <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-purple-800">Information importante</h3>
                                <div class="mt-2 text-sm text-purple-700">
                                    <p>Le premier paiement couvre les frais de gestion.</p>
                                    <p>Les paiements suivants seront automatiquement ajoutés au CEDD.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition-all duration-300 ease-in-out hover:-translate-y-1">
                        Lancer la Tontine
                        <span class="ml-2">→</span>
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Affichage de la tontine active -->
        <div class="max-w-3xl mx-auto ">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tontines en cours</h2>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Tontine #2989</h3>
                        <p class="text-lg text-gray-600 mt-2">Montant: <span class="font-semibold">278,000
                                FCFA</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-bold text-gray-900">12 Mai 2098</p>
                    </div>
                </div>

                <!-- Barre de progression améliorée -->
                <div class="relative pt-4">
                    <div class="overflow-hidden h-6 rounded-full bg-gray-100">
                        <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full relative group transition-all duration-300"
                            style="width: 70%">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent
                                transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                            </div>
                        </div>
                    </div>
                    <span class="absolute right-0 top-0 text-sm font-medium text-gray-600">70%</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Historique des tontines -->
    <div class="max-w-3xl mx-auto mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Historique des tontines</h2>

        <div class="space-y-4">
            <!-- Carte de tontine -->
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tontine #2989</h3>
                            <p class="text-gray-600 mt-1">Montant: <span class="font-semibold">278,000 FCFA</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-bold text-gray-900">12 Mai 2098</p>
                        </div>
                    </div>


                </div>
            </div>
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tontine #2989</h3>
                            <p class="text-gray-600 mt-1">Montant: <span class="font-semibold">278,000 FCFA</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-bold text-gray-900">12 Mai 2098</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
