<div class="min-h-screen p-8">

    @if (!$tontineStart)

        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Header avec un design plus moderne -->
                <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8">
                    <h2 class="text-4xl font-bold  text-center tracking-tight text-white">Nouvelle Tontine</h2>
                    <p class=" mt-2 text-center text-lg text-white">Créez votre épargne collaborative en quelques clics
                    </p>
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

                        <!-- Pour le montant -->
                        @if ($errors['amount'])
                            <span class="text-sm text-red-500">{{ $errors['amount'] }}</span>
                        @endif
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

                        <!-- Pour la fréquence -->
                        @if ($errors['frequency'])
                            <span class="text-sm text-red-500">{{ $errors['frequency'] }}</span>
                        @endif
                    </div>

                    <!-- Date de fin avec calendrier moderne -->
                    <div class="space-y-2">
                        <label class="text-base font-medium text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span id="durationLabel">
                                <!-- Le label changera dynamiquement -->
                                Durée
                            </span>
                        </label>
                        <div class="relative mt-1">
                            <input type="number" id="duration" wire:model.defer="duration"
                                class="block w-full px-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                                placeholder="Entrez la durée" required>
                        </div>
                        <!-- Pour la durée -->
                        @if ($errors['duration'])
                            <span class="text-sm text-red-500">{{ $errors['duration'] }}</span>
                        @endif
                    </div>

                    <!-- Section Gain Potentiel -->
                    <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-indigo-900">Gain Potentiel</h3>
                            </div>
                            <div class="grid grid-cols-3 gap-6">
                                <div>
                                    <p class="text-sm text-indigo-600">Montant total</p>
                                    <p class="text-2xl font-bold text-indigo-700" id="potentialGain"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-indigo-600">Frais de service</p>
                                    <p class="text-lg font-semibold text-indigo-700" id="fraisDeSevice">-</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-indigo-600">Date de fin</p>
                                    <p class="text-lg font-semibold text-indigo-700" id="endDateDisplay">-</p>
                                </div>
                            </div>

                        </div>
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

                <script src="{{ asset('js/tontine.js') }}"></script>
            </div>
        </div>
    @else
        <!-- Affichage de la tontine active -->
        <div class="max-w-3xl mx-auto ">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tontines en cours </h2>
            <x-tontine-card id="{{ $tontineEnCours->id }}" montant="{{ $tontineEnCours->montant_cotisation }}"
                frequence="{{ $tontineEnCours->frequence }}" dateDebut="{{ $tontineEnCours->created_at }}"
                dateFin="{{ $tontineEnCours->date_fin }}" progression="65" cotisationsEffectuees="15"
                cotisationsTotales="24" montantCollecte="180000"
                prochainPaiement="{{ $tontineEnCours->next_payment_date }}" status="active" />
        </div>
    @endif

    <!-- Historique des tontines -->
    <div class="max-w-3xl mx-auto mt-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Historique des tontines</h2>
            <div class="flex gap-2">
                <select
                    class="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all">Toutes les tontines</option>
                    <option value="active">Tontines actives</option>
                    <option value="completed">Tontines terminées</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">

            <x-tontine-card :id="2989" :montant="278000" frequence="Hebdomadaire" dateDebut="12 Mai 2024"
                dateFin="12 Mai 2025" :progression="65" :cotisationsEffectuees="15" :cotisationsTotales="24" :montantCollecte="180000"
                prochainPaiement="19 Fév 2025" status="active" />
            <x-tontine-card :id="2989" :montant="278000" frequence="Hebdomadaire" dateDebut="12 Mai 2024"
                dateFin="12 Mai 2025" :progression="100" :cotisationsEffectuees="3" :cotisationsTotales="3" :montantCollecte="180000"
                prochainPaiement="19 Fév 2025" status="active" />


        </div>
    </div>
</div>
